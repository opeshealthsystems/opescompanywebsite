import { OpesBusinessApiClient, OpesApiError } from '../src/index.js';

const { OPES_DELEGATED_ACCESS_TOKEN, OPES_MACHINE_CLIENT_ID, OPES_API_URL } = process.env;
if (!OPES_DELEGATED_ACCESS_TOKEN || !OPES_MACHINE_CLIENT_ID) {
  throw new Error('Set an approved administrator access token and machine client ID in the server environment.');
}

const api = new OpesBusinessApiClient({
  baseUrl: OPES_API_URL,
  getAccessToken: async () => OPES_DELEGATED_ACCESS_TOKEN,
});

try {
  const result = await api.request('admin_apiClients_usage', {
    path: { client: OPES_MACHINE_CLIENT_ID },
  });
  const report = result.data.data;
  console.log(JSON.stringify({
    client: report.client,
    periodStart: report.usage.period_start,
    periodEnd: report.usage.period_end,
    requestsToday: report.usage.requests_today,
    requestsLast30Days: report.usage.requests_last_30_days,
    dailyUsage: report.usage.daily_usage,
    requestId: result.requestId,
  }));
} catch (error) {
  if (error instanceof OpesApiError) {
    console.error(JSON.stringify({ status: error.status, requestId: error.requestId, code: error.code }));
  } else {
    console.error('Unable to read the selected machine client usage.');
  }
  process.exitCode = 1;
}
