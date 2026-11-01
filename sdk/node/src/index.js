import { randomUUID } from 'node:crypto';
import { businessOperations } from './business-operations.js';

export const createIdempotencyKey = () => randomUUID();

export class OpesApiError extends Error {
  constructor(status, { requestId = null, code = null, retryAfter = null, idempotencyKey = null } = {}) {
    super(status ? `OHS API returned HTTP ${status}` : 'Unable to reach OHS API');
    this.name = 'OpesApiError';
    Object.assign(this, { status, requestId, code, retryAfter, idempotencyKey });
  }
}

export class OpesClient {
  #base; #oauth; #fetch; #timeout; #token; #tokenPromise;

  constructor({ baseUrl = 'https://opeshealthsystems.com/api/v1', oauth, fetch: transport = globalThis.fetch, timeoutMs = 15000 } = {}) {
    const url = new URL(baseUrl);
    if (url.username || url.password || url.search || url.hash ||
        (url.protocol !== 'https:' && !(url.protocol === 'http:' && ['localhost', '127.0.0.1', '[::1]'].includes(url.hostname)))) {
      throw new TypeError('Use an HTTPS API URL; HTTP is allowed only on localhost.');
    }
    if (typeof transport !== 'function' || !Number.isInteger(timeoutMs) || timeoutMs < 1 || timeoutMs > 60000) throw new TypeError('Invalid transport or timeout.');
    if (oauth && (typeof oauth.clientId !== 'string' || !oauth.clientId || typeof oauth.clientSecret !== 'string' || !oauth.clientSecret || !Array.isArray(oauth.scopes) || !oauth.scopes.length ||
        oauth.scopes.some(scope => !['catalogue:read', 'integration:read'].includes(scope)))) throw new TypeError('Explicit supported scopes and server credentials are required.');
    this.#base = url.href.replace(/\/$/, '');
    this.#oauth = oauth ? { ...oauth, scopes: [...new Set(oauth.scopes)] } : null;
    this.#fetch = transport;
    this.#timeout = timeoutMs;
  }

  async #request(path, { method = 'GET', headers = {}, body, idempotencyKey = null } = {}) {
    let response;
    try {
      response = await this.#fetch(this.#base + path, {
        method, headers: { Accept: 'application/json', ...headers }, body,
        redirect: 'error', credentials: 'omit', signal: AbortSignal.timeout(this.#timeout),
      });
    } catch {
      throw new OpesApiError(0, { code: 'network_error', idempotencyKey });
    }
    const requestId = response.headers.get('x-request-id');
    let data;
    try { data = await response.json(); }
    catch { throw new OpesApiError(response.status, { requestId, code: 'invalid_response', idempotencyKey }); }
    if (!response.ok) {
      const retry = response.headers.get('retry-after');
      const seconds = retry === null ? null : /^\d+$/.test(retry) ? Number(retry) : Math.max(0, Math.ceil((Date.parse(retry) - Date.now()) / 1000));
      const code = typeof data?.error === 'string' && /^[a-z0-9_-]{1,80}$/i.test(data.error) ? data.error : null;
      throw new OpesApiError(response.status, { requestId, code, retryAfter: Number.isFinite(seconds) ? seconds : null, idempotencyKey });
    }
    return { data, requestId, replayed: response.headers.get('idempotency-replayed') === 'true' };
  }

  async #accessToken() {
    if (!this.#oauth) throw new TypeError('Server credentials are required for this operation.');
    if (this.#token && Date.now() < this.#token.expiresAt) return this.#token.value;
    if (this.#tokenPromise) return this.#tokenPromise;
    this.#tokenPromise = (async () => {
      const result = await this.#request('/oauth/token', {
        method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ grant_type: 'client_credentials', client_id: this.#oauth.clientId,
          client_secret: this.#oauth.clientSecret, scope: this.#oauth.scopes.join(' ') }).toString(),
      });
      const token = result.data;
      if (typeof token?.access_token !== 'string' || !token.access_token || token.token_type !== 'Bearer' ||
          !Number.isInteger(token.expires_in) || token.expires_in < 1 || token.expires_in > 900) throw new OpesApiError(200, { code: 'invalid_token_response', requestId: result.requestId });
      this.#token = { value: token.access_token, expiresAt: Date.now() + (token.expires_in - Math.min(30, token.expires_in / 2)) * 1000 };
      return this.#token.value;
    })();
    try { return await this.#tokenPromise; }
    finally { this.#tokenPromise = null; }
  }

  async #authenticated(path) {
    const token = await this.#accessToken();
    try { return (await this.#request(path, { headers: { Authorization: `Bearer ${token}` } })).data; }
    catch (error) {
      if (!(error instanceof OpesApiError) || error.status !== 401) throw error;
      // Only GET resources are retried once after refreshing; enquiry POSTs are never silently replayed.
      if (this.#token?.value === token) this.#token = null;
      const refreshed = await this.#accessToken();
      return (await this.#request(path, { headers: { Authorization: `Bearer ${refreshed}` } })).data;
    }
  }

  catalogue({ page = 1, perPage = 20, locale = 'en' } = {}) {
    if (!Number.isInteger(page) || page < 1 || page > 10000 || !Number.isInteger(perPage) || perPage < 1 || perPage > 50 || !['en', 'fr'].includes(locale)) throw new TypeError('Invalid catalogue pagination or locale.');
    return this.#authenticated('/catalogue/products?' + new URLSearchParams({ page: String(page), per_page: String(perPage), locale }));
  }

  integration() { return this.#authenticated('/integration'); }

  async submitEnquiry(input, { idempotencyKey } = {}) {
    if (typeof idempotencyKey !== 'string' || idempotencyKey.trim() !== idempotencyKey || !/^[A-Za-z0-9._:-]{16,128}$/.test(idempotencyKey)) throw new TypeError('Supply and retain a valid Idempotency-Key for this intended enquiry.');
    const result = await this.#request('/enquiries', {
      method: 'POST', headers: { 'Content-Type': 'application/json', 'Idempotency-Key': idempotencyKey },
      body: JSON.stringify(input), idempotencyKey,
    });
    return { ...result.data, idempotencyKey, replayed: result.replayed, requestId: result.requestId };
  }
}

/** Client for API operations that use user-delegated OAuth access tokens. */
export class OpesBusinessApiClient {
  #base; #getAccessToken; #fetch; #timeout;

  constructor({ baseUrl = 'https://opeshealthsystems.com/api/v1', accessToken, getAccessToken, fetch: transport = globalThis.fetch, timeoutMs = 15000 } = {}) {
    const url = new URL(baseUrl);
    if (url.username || url.password || url.search || url.hash ||
        (url.protocol !== 'https:' && !(url.protocol === 'http:' && ['localhost', '127.0.0.1', '[::1]'].includes(url.hostname)))) {
      throw new TypeError('Use an HTTPS API URL; HTTP is allowed only on localhost.');
    }
    if ((accessToken !== undefined && (typeof accessToken !== 'string' || !accessToken.trim())) ||
        (getAccessToken !== undefined && typeof getAccessToken !== 'function') ||
        (accessToken !== undefined && getAccessToken !== undefined)) throw new TypeError('Provide one access token or an access-token provider.');
    if (typeof transport !== 'function' || !Number.isInteger(timeoutMs) || timeoutMs < 1 || timeoutMs > 60000) throw new TypeError('Invalid transport or timeout.');
    this.#base = url.href.replace(/\/$/, '');
    this.#getAccessToken = getAccessToken ?? (() => accessToken ?? null);
    this.#fetch = transport;
    this.#timeout = timeoutMs;
  }

  async request(operationId, { path = {}, query = {}, body, idempotencyKey, locale, signal } = {}) {
    const operation = businessOperations[operationId];
    if (!operation) throw new TypeError('Unknown OHS business operation. Use an operation ID in the OpenAPI contract.');
    if (operation.idempotencyRequired && (typeof idempotencyKey !== 'string' || !/^[A-Za-z0-9._:-]{16,128}$/.test(idempotencyKey))) {
      throw new TypeError('Supply and retain a valid Idempotency-Key for this command.');
    }
    if (operation.requestBodyRequired && body === undefined) throw new TypeError('This operation requires a request body.');
    if (locale !== undefined && !['en', 'fr'].includes(locale)) throw new TypeError('Locale must be en or fr.');

    const requiredPath = new Set(operation.pathParameters);
    for (const name of requiredPath) if (path[name] === undefined || path[name] === null || String(path[name]) === '') throw new TypeError(`Missing path parameter: ${name}`);
    for (const name of Object.keys(path)) if (!requiredPath.has(name)) throw new TypeError(`Unexpected path parameter: ${name}`);
    const route = operation.path.replace(/\{([^}]+)\}/g, (_, name) => encodeURIComponent(String(path[name])));
    const url = new URL(this.#base + route);
    const acceptedQuery = new Set(operation.queryParameters);
    for (const [name, value] of Object.entries(query)) {
      if (!acceptedQuery.has(name)) throw new TypeError(`Unexpected query parameter: ${name}`);
      if (!['string', 'number', 'boolean'].includes(typeof value)) throw new TypeError(`Query parameter ${name} must be a scalar.`);
      url.searchParams.set(name, String(value));
    }

    const headers = { Accept: 'application/json' };
    if (operation.acceptLanguage && locale) headers['Accept-Language'] = locale;
    if (operation.idempotencyRequired) headers['Idempotency-Key'] = idempotencyKey;
    if (operation.authenticated) {
      const token = await this.#getAccessToken();
      if (typeof token !== 'string' || !token.trim()) throw new TypeError('A valid delegated OAuth access token is required.');
      headers.Authorization = `Bearer ${token}`;
    }

    let requestBody;
    if (body !== undefined) {
      if (typeof FormData !== 'undefined' && body instanceof FormData) requestBody = body;
      else { headers['Content-Type'] = 'application/json'; requestBody = JSON.stringify(body); }
    }
    const requestSignal = signal ? AbortSignal.any([AbortSignal.timeout(this.#timeout), signal]) : AbortSignal.timeout(this.#timeout);
    let response;
    try {
      response = await this.#fetch(url.href, { method: operation.method, headers, body: requestBody, redirect: 'error', credentials: 'omit', signal: requestSignal });
    } catch {
      throw new OpesApiError(0, { code: 'network_error', idempotencyKey: idempotencyKey ?? null });
    }

    const requestId = response.headers.get('x-request-id');
    if (response.status === 204) return { data: null, requestId, replayed: response.headers.get('idempotency-replayed') === 'true' };
    let data;
    try {
      const contentType = response.headers.get('content-type') ?? '';
      data = contentType.includes('json') ? await response.json() : await response.arrayBuffer();
    } catch { throw new OpesApiError(response.status, { requestId, code: 'invalid_response', idempotencyKey: idempotencyKey ?? null }); }
    if (!response.ok) {
      const retry = response.headers.get('retry-after');
      const seconds = retry === null ? null : /^\d+$/.test(retry) ? Number(retry) : Math.max(0, Math.ceil((Date.parse(retry) - Date.now()) / 1000));
      const rawCode = typeof data?.error === 'string' ? data.error : typeof data?.type === 'string' ? data.type.split('/').pop() : null;
      const code = rawCode && /^[a-z0-9_-]{1,80}$/i.test(rawCode) ? rawCode : null;
      throw new OpesApiError(response.status, { requestId, code, retryAfter: Number.isFinite(seconds) ? seconds : null, idempotencyKey: idempotencyKey ?? null });
    }
    return { data, requestId, replayed: response.headers.get('idempotency-replayed') === 'true' };
  }
}
