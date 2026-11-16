import * as AuthSession from 'expo-auth-session';
import * as Crypto from 'expo-crypto';
import * as SecureStore from 'expo-secure-store';
import { createContext, ReactNode, useContext, useMemo, useRef, useState } from 'react';
import { Platform } from 'react-native';
import { getLang, t } from '../i18n';

export type Audience = 'staff' | 'customer' | 'practitioner' | 'tester' | 'partner';
export type Account = { id: string; name: string; email: string; roles: string[]; scopes: string[] };
export type ApiRecord = Record<string, unknown>;
export type Token = { access_token: string; refresh_token?: string; expires_at?: number; scope?: string };
export type FeatureId = 'quotes' | 'customers' | 'contacts' | 'partnerApps' | 'deals' | 'serviceRequests' | 'tickets' | 'invoices' | 'customerProfile' | 'practitionerProfile' | 'courses' | 'certificates' | 'assignments' | 'bugReports' | 'partnerships' | 'partnerApplication' | 'partnerAccounts' | 'notifications';
export type Feature = { id: FeatureId; title: string; description: string; endpoint: string; readScope?: string; writeScope?: string; createAction?: string };

const API = process.env.EXPO_PUBLIC_OHS_API_URL ?? 'https://opeshealthsystems.com/api/v1';
const CLIENT_ID = process.env.EXPO_PUBLIC_OHS_OAUTH_CLIENT_ID ?? '';
const TOKEN_KEY = 'opes.health.session.v1';
const MUTATION_SALT_KEY = 'opes.health.mutation-salt.v1';
const MUTATION_KEY_PREFIX = 'opes.health.pending-mutation.v1.';
const MUTATION_KEY_TTL_MS = 23 * 60 * 60 * 1000;
const discovery = { authorizationEndpoint: 'https://opeshealthsystems.com/oauth/authorize', tokenEndpoint: `${API}/oauth/user-token` };
type PendingMutationKey = { value: string; expiresAt: number; storageKey: string };

export const workspaceTitles: Record<Audience, string> = {
  staff: 'Staff & administration', customer: 'Customer workspace', practitioner: 'Practitioner workspace', tester: 'Testing workspace', partner: 'Partner workspace',
};

export const featureCatalog: Record<FeatureId, Feature> = {
  quotes: { id: 'quotes', title: 'Quotes', description: 'Review, prepare and update customer quotations.', endpoint: '/finance/quotes', readScope: 'finance:read', writeScope: 'finance:write', createAction: 'quote' },
  customers: { id: 'customers', title: 'Customers', description: 'Review customer facilities and create customer profiles.', endpoint: '/admin/customer-profiles', readScope: 'administration:read', writeScope: 'administration:write', createAction: 'customer' },
  contacts: { id: 'contacts', title: 'Contacts & leads', description: 'Review contact enquiries, research participation and health data licensing proposals.', endpoint: '/admin/enquiries/leads', readScope: 'enquiries:read', writeScope: 'enquiries:write', createAction: 'contact' },
  partnerApps: { id: 'partnerApps', title: 'Partner applications', description: 'Review incoming partnership applications.', endpoint: '/crm/partner-applications', readScope: 'crm:read', writeScope: 'crm:write' },
  deals: { id: 'deals', title: 'CRM deals', description: 'Track business opportunities and deal value.', endpoint: '/crm/deals', readScope: 'crm:read', writeScope: 'crm:write', createAction: 'deal' },
  serviceRequests: { id: 'serviceRequests', title: 'Service requests', description: 'Request and track services for your organization.', endpoint: '/customer/service-requests', readScope: 'customer:read', writeScope: 'customer:write', createAction: 'serviceRequest' },
  tickets: { id: 'tickets', title: 'Support tickets', description: 'Open a support request and follow replies.', endpoint: '/customer/tickets', readScope: 'customer:read', writeScope: 'customer:write', createAction: 'ticket' },
  invoices: { id: 'invoices', title: 'Invoices', description: 'Review invoices issued to your organization.', endpoint: '/customer/invoices', readScope: 'customer:read' },
  customerProfile: { id: 'customerProfile', title: 'Account profile', description: 'Maintain your organization and contact details.', endpoint: '/customer/profile', readScope: 'customer:read', writeScope: 'customer:write', createAction: 'customerProfile' },
  practitionerProfile: { id: 'practitionerProfile', title: 'Professional profile', description: 'Maintain your practitioner profile and workplace details.', endpoint: '/practitioner/profile', readScope: 'practitioner:read', writeScope: 'practitioner:write', createAction: 'practitionerProfile' },
  courses: { id: 'courses', title: 'Courses', description: 'Browse courses, study lessons, save progress and view certificates.', endpoint: '/practitioner/courses', readScope: 'practitioner:read', writeScope: 'practitioner:write' },
  certificates: { id: 'certificates', title: 'Certificates', description: 'View certificates associated with your account.', endpoint: '/practitioner/certificates', readScope: 'practitioner:read' },
  assignments: { id: 'assignments', title: 'Assignments', description: 'Update testing progress and report bugs.', endpoint: '/testing/assignments', readScope: 'testing:read', writeScope: 'testing:write' },
  bugReports: { id: 'bugReports', title: 'Bug reports', description: 'Review issues reported from your assigned product tests.', endpoint: '/testing/bug-reports', readScope: 'testing:read' },
  partnerships: { id: 'partnerships', title: 'Partner directory', description: 'Browse organizations in the OPES partner network.', endpoint: '/public/partnerships' },
  partnerApplication: { id: 'partnerApplication', title: 'Apply to partner', description: 'Send a partnership application to OPES Health Systems.', endpoint: '/public/become-a-partner', createAction: 'partnerApplication' },
  partnerAccounts: { id: 'partnerAccounts', title: 'My organizations', description: 'View approved organizations explicitly linked to your partner account and maintain owner-managed details.', endpoint: '/partner/accounts', readScope: 'identity:read', writeScope: 'partner:write' },
  notifications: { id: 'notifications', title: 'Notifications', description: 'Review account updates and mark alerts as read.', endpoint: '/notifications', readScope: 'identity:read', writeScope: 'identity:write' },
};

export const featureIds: Record<Audience, FeatureId[]> = {
  staff: ['quotes', 'customers', 'contacts', 'partnerApps', 'deals'],
  customer: ['serviceRequests', 'tickets', 'invoices', 'customerProfile'],
  practitioner: ['practitionerProfile', 'courses', 'certificates'],
  tester: ['assignments', 'bugReports'],
  partner: ['partnerAccounts', 'partnerships'],
};

type SessionValue = {
  account: Account | null; audience: Audience | null; token: Token | null;
  setAccount: (value: Account) => void; setAccessToken: (value: Token | null) => Promise<void>;
  clearSession: () => Promise<void>; rowsFor: (feature: FeatureId) => Promise<ApiRecord[]>;
  request: <T = unknown>(path: string, method?: string, body?: unknown, idempotencyKey?: string) => Promise<T>;
  isAllowed: (feature: Feature, write?: boolean) => boolean;
};
const SessionContext = createContext<SessionValue | null>(null);

export function SessionProvider({ children }: { children: ReactNode }) {
  const [account, setAccountState] = useState<Account | null>(null);
  const [audience, setAudience] = useState<Audience | null>(null);
  const [token, setTokenState] = useState<Token | null>(null);
  const tokenRef = useRef<Token | null>(null);
  const refreshInFlight = useRef<Promise<Token | null> | null>(null);
  const mutationSalt = useRef<Promise<string> | null>(null);
  const webMutationSalt = useRef(Crypto.randomUUID());
  const pendingMutationKeys = useRef(new Map<string, PendingMutationKey>());
  const creatingMutationKeys = useRef(new Map<string, Promise<PendingMutationKey>>());

  const setAccount = (value: Account) => {
    setAccountState(value);
    const roles = value.roles.map(role => role.toLowerCase().replace(/[^a-z0-9]+/g, '_'));
    const has = (...values: string[]) => roles.some(role => values.includes(role));
    if (has('super_admin','admin','staff','employee','manager','finance','accountant','operations','hr','support')) setAudience('staff');
    else if (roles.some(role => /(^|_)(practitioner|clinician|health_practitioner|doctor|nurse)(_|$)/.test(role)) || value.scopes.includes('practitioner:read')) setAudience('practitioner');
    else if (roles.some(role => /(^|_)(tester|quality_assurance|qa_tester)(_|$)/.test(role)) || value.scopes.includes('testing:read')) setAudience('tester');
    else if (roles.some(role => /(^|_)(partner|vendor|institution)(_|$)/.test(role))) setAudience('partner');
    else if (has('customer','client','patient') || value.scopes.includes('customer:read')) setAudience('customer');
    else setAudience(null);
  };

  const setAccessToken = async (value: Token | null) => {
    tokenRef.current = value; setTokenState(value);
    if (Platform.OS !== 'web') {
      if (value) await SecureStore.setItemAsync(TOKEN_KEY, JSON.stringify(value));
      else await SecureStore.deleteItemAsync(TOKEN_KEY);
    }
  };

  const clearSession = async () => {
    await setAccessToken(null); setAccountState(null); setAudience(null);
  };

  const refreshToken = async (force = false): Promise<Token | null> => {
    const active = tokenRef.current;
    if (!active?.refresh_token) return force ? null : active;
    if (!force && (!active.expires_at || active.expires_at > Date.now() + 45000)) return active;
    if (!refreshInFlight.current) {
      refreshInFlight.current = AuthSession.refreshAsync({ clientId: CLIENT_ID, refreshToken: active.refresh_token }, discovery)
        .then(async refreshed => {
          const renewed: Token = { access_token: refreshed.accessToken, refresh_token: refreshed.refreshToken ?? active.refresh_token, expires_at: Date.now() + (refreshed.expiresIn ?? 900) * 1000, scope: refreshed.scope };
          await setAccessToken(renewed); return renewed;
        }).catch(async () => { await clearSession(); return null; }).finally(() => { refreshInFlight.current = null; });
    }
    return refreshInFlight.current;
  };

  const request = async <T,>(path: string, method = 'GET', body?: unknown, idempotencyKey?: string): Promise<T> => {
    const upperMethod = method.toUpperCase();
    const mutating = !['GET', 'HEAD', 'OPTIONS'].includes(upperMethod);
    let pending: PendingMutationKey | null = null;
    if (mutating && !idempotencyKey) {
      if (!path.startsWith('/') || path.startsWith('//')) throw new TypeError('API requests must use a relative path.');
      const salt = Platform.OS === 'web' ? webMutationSalt.current : await (mutationSalt.current ??= (async () => {
        try {
          const stored = await SecureStore.getItemAsync(MUTATION_SALT_KEY);
          if (stored) return stored;
          const generated = Crypto.randomUUID();
          await SecureStore.setItemAsync(MUTATION_SALT_KEY, generated);
          return generated;
        } catch { return webMutationSalt.current; }
      })());
      const fingerprintInput = `${account?.id ?? 'anonymous'}\n${upperMethod}\n${path}\n${body === undefined ? '' : JSON.stringify(body)}`;
      const fingerprint = await Crypto.digestStringAsync(Crypto.CryptoDigestAlgorithm.SHA256, `${salt}\n${fingerprintInput}`);
      const storageKey = `${MUTATION_KEY_PREFIX}${fingerprint}`;
      pending = pendingMutationKeys.current.get(storageKey) ?? null;
      if (!pending) {
        let creation = creatingMutationKeys.current.get(storageKey);
        if (!creation) {
          creation = (async () => {
            const now = Date.now();
            if (Platform.OS !== 'web') {
              try {
                const stored = await SecureStore.getItemAsync(storageKey);
                if (stored) {
                  const value = JSON.parse(stored) as { value?: unknown; expiresAt?: unknown };
                  if (typeof value.value === 'string' && typeof value.expiresAt === 'number' && value.expiresAt > now) {
                    return { value: value.value, expiresAt: value.expiresAt, storageKey };
                  }
                  await SecureStore.deleteItemAsync(storageKey);
                }
              } catch { /* Use an in-memory key when secure storage is temporarily unavailable. */ }
            }
            const value = `mobile-${Crypto.randomUUID()}`;
            const entry = { value, expiresAt: now + MUTATION_KEY_TTL_MS, storageKey };
            if (Platform.OS !== 'web') {
              try { await SecureStore.setItemAsync(storageKey, JSON.stringify({ value, expiresAt: entry.expiresAt })); } catch { /* Keep the key in memory for this session. */ }
            }
            return entry;
          })();
          creatingMutationKeys.current.set(storageKey, creation);
        }
        try { pending = await creation; }
        finally { creatingMutationKeys.current.delete(storageKey); }
        pendingMutationKeys.current.set(storageKey, pending);
      }
    }
    // Persist retry keys before sending; keep them after network/5xx/429 ambiguity and reuse them on the next attempt.
    const key = idempotencyKey ?? pending?.value;
    const clearPendingKey = async () => {
      if (!pending) return;
      pendingMutationKeys.current.delete(pending.storageKey);
      if (Platform.OS !== 'web') {
        try { await SecureStore.deleteItemAsync(pending.storageKey); } catch { /* A stale key is safer than duplicating a command. */ }
      }
    };
    const send = (active: Token | null) => {
      const headers: Record<string, string> = { Accept: 'application/json', 'Accept-Language': getLang() };
      if (active?.access_token) headers.Authorization = `Bearer ${active.access_token}`;
      if (body !== undefined) headers['Content-Type'] = 'application/json';
      if (key) headers['Idempotency-Key'] = key;
      return fetch(`${API}${path}`, { method, headers, ...(body !== undefined ? { body: JSON.stringify(body) } : {}) });
    };
    const sentWith = await refreshToken();
    let response = await send(sentWith);
    if (response.status === 401 && sentWith?.refresh_token) {
      // The access token may have been revoked or expired early; try one forced refresh before ending the session.
      const renewed = await refreshToken(true);
      if (renewed) response = await send(renewed);
    }
    const data = await response.json().catch(() => ({}));
    if (response.status === 401) {
      await clearSession();
      throw new Error(t('Your session has expired. Sign in again to continue.'));
    }
    if (!response.ok) {
      if (pending && response.status >= 400 && response.status < 500 && ![408, 425, 429].includes(response.status)) await clearPendingKey();
      const fieldErrors = data?.errors && typeof data.errors === 'object' ? Object.values(data.errors).flat().filter(value => typeof value === 'string').join(' ') : '';
      const requestId = typeof data?.request_id === 'string' ? t(' Reference: {id}', { id: data.request_id }) : '';
      throw new Error(`${data?.detail || data?.message || fieldErrors || data?.title || t('Request failed ({status})', { status: response.status })}${requestId}`);
    }
    await clearPendingKey();
    return (data?.data ?? data) as T;
  };

  const rowsFor = async (id: FeatureId): Promise<ApiRecord[]> => {
    const feature = featureCatalog[id];
    if (!feature || !isAllowed(feature)) throw new Error(t('Your account does not have permission to view this workspace.'));
    const result = await request<unknown>(feature.endpoint);
    if (Array.isArray(result)) return result as ApiRecord[];
    if (result && typeof result === 'object') {
      const obj = result as Record<string, unknown>;
      for (const key of ['items','data','results','records','accounts','quotes','profiles','profile','customerProfiles','leads','applications','partnerApplications','deals','serviceRequests','tickets','reports','assignments','bug_reports','bugReports','invoices','courses','certificates','notifications']) if (Array.isArray(obj[key])) return obj[key] as ApiRecord[];
      if (obj.profile && typeof obj.profile === 'object') return [obj.profile as ApiRecord];
      if (id.endsWith('Profile') || id === 'practitionerProfile') return [obj as ApiRecord];
    }
    return [];
  };

  const isAllowed = (feature: Feature, write = false) => {
    if (!feature[write ? 'writeScope' : 'readScope']) return true;
    return !!account?.scopes.includes(feature[write ? 'writeScope' : 'readScope'] as string);
  };

  // Function closures read session state represented in the dependency list; keep a stable context value for route screens.
  // eslint-disable-next-line react-hooks/exhaustive-deps
  const value = useMemo(() => ({ account, audience, token, setAccount, setAccessToken, clearSession, rowsFor, request, isAllowed }), [account, audience, token]);
  return <SessionContext.Provider value={value}>{children}</SessionContext.Provider>;
}

export function useSession() {
  const value = useContext(SessionContext);
  if (!value) throw new Error('useSession must be used inside SessionProvider.');
  return value;
}

export { API, CLIENT_ID, TOKEN_KEY, discovery };
