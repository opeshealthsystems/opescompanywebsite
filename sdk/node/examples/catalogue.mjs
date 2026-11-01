import { OpesClient, OpesApiError } from '../src/index.js';

const { OPES_CLIENT_ID, OPES_CLIENT_SECRET, OPES_API_URL, OPES_SCOPES = 'catalogue:read integration:read' } = process.env;
if (!OPES_CLIENT_ID || !OPES_CLIENT_SECRET) throw new Error('Set OPES_CLIENT_ID and OPES_CLIENT_SECRET privately on your server.');
const client = new OpesClient({ baseUrl: OPES_API_URL, oauth: {
  clientId: OPES_CLIENT_ID, clientSecret: OPES_CLIENT_SECRET, scopes: OPES_SCOPES.split(/\s+/),
} });
try {
  const catalogue = await client.catalogue({ perPage: 2 });
  console.log(JSON.stringify({ returned: catalogue.data.length, total: catalogue.meta.total }));
} catch (error) {
  if (error instanceof OpesApiError) console.error(JSON.stringify({ status: error.status, requestId: error.requestId, code: error.code }));
  else console.error('Unable to read catalogue.');
  process.exitCode = 1;
}
