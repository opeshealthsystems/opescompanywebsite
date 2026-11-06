import { OpesBusinessApiClient } from '../src/index.js';

const token = process.env.OPES_USER_ACCESS_TOKEN;
if (!token) throw new Error('Set OPES_USER_ACCESS_TOKEN to a delegated OAuth token with identity:read.');

const api = new OpesBusinessApiClient({ accessToken: token });
const result = await api.request('readOwnBusinessUsage');
console.log(JSON.stringify(result.data.data, null, 2));
