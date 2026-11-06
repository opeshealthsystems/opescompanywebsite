import { OpesClient, OpesApiError } from '../src/index.js';

const { OPES_CLIENT_ID, OPES_CLIENT_SECRET, OPES_API_URL } = process.env;
if (!OPES_CLIENT_ID || !OPES_CLIENT_SECRET) {
  throw new Error('Set OPES_CLIENT_ID and OPES_CLIENT_SECRET privately on your server.');
}

const client = new OpesClient({ baseUrl: OPES_API_URL, oauth: {
  clientId: OPES_CLIENT_ID,
  clientSecret: OPES_CLIENT_SECRET,
  scopes: ['integration:read'],
} });

try {
  const result = await client.integrationUsage();
  const usage = result.data;
  console.log(JSON.stringify({
    periodStart: usage.period_start,
    periodEnd: usage.period_end,
    requestsToday: usage.requests_today,
    requestsLast30Days: usage.requests_last_30_days,
    dailyUsage: usage.daily_usage,
  }));
} catch (error) {
  if (error instanceof OpesApiError) {
    console.error(JSON.stringify({ status: error.status, requestId: error.requestId, code: error.code }));
  } else {
    console.error('Unable to read integration usage.');
  }
  process.exitCode = 1;
}
