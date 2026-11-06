import { OpesApiError, OpesClient } from '../src/index.js';

const client = new OpesClient();
try {
  const discovery = await client.discovery({ locale: process.env.OPES_LOCALE === 'fr' ? 'fr' : 'en' });
  console.log(JSON.stringify(discovery.data._links, null, 2));
} catch (error) {
  if (error instanceof OpesApiError) {
    console.error(JSON.stringify({ status: error.status, requestId: error.requestId, code: error.code }));
  } else {
    console.error('Unable to read OPES API discovery.');
  }
  process.exitCode = 1;
}
