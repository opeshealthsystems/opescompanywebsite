import * as AuthSession from 'expo-auth-session';
import * as SecureStore from 'expo-secure-store';
import { createContext, ReactNode, useContext, useMemo, useRef, useState } from 'react';
import { Platform } from 'react-native';
import { getLang, t } from '../i18n';

export type Audience = 'staff' | 'customer' | 'practitioner' | 'tester' | 'partner';
export type Account = { id: string; name: string; email: string; roles: string[]; scopes: string[] };
export type ApiRecord = Record<string, unknown>;
export type Token = { access_token: string; refresh_token?: string; expires_at?: number; scope?: string };
export type FeatureId = 'quotes' | 'customers' | 'contacts' | 'partnerApps' | 'deals' | 'serviceRequests' | 'tickets' | 'invoices' | 'customerProfile' | 'practitionerProfile' | 'courses' | 'certificates' | 'assignments' | 'bugReports' | 'partnerships' | 'partnerApplication' | 'partnerAccounts' | 'notifications';
export type Feature = { id: FeatureId; title: string; description: string; endpoint: string; readScope?: string; writeScope?: string; createAction?: string; demoOnly?: boolean };
export type DemoAccount = { id: string; label: string; audience: Audience; account: Account; organization: string; reference: string };

const API = process.env.EXPO_PUBLIC_OHS_API_URL ?? 'https://opeshealthsystems.com/api/v1';
const CLIENT_ID = process.env.EXPO_PUBLIC_OHS_OAUTH_CLIENT_ID ?? '';
const TOKEN_KEY = 'opes.health.session.v1';
const discovery = { authorizationEndpoint: 'https://opeshealthsystems.com/oauth/authorize', tokenEndpoint: `${API}/oauth/user-token` };

export const workspaceTitles: Record<Audience, string> = {
  staff: 'Staff & administration', customer: 'Customer workspace', practitioner: 'Practitioner workspace', tester: 'Testing workspace', partner: 'Partner workspace',
};

export const featureCatalog: Record<FeatureId, Feature> = {
  quotes: { id: 'quotes', title: 'Quotes', description: 'Review, prepare and update customer quotations.', endpoint: '/finance/quotes', readScope: 'finance:read', writeScope: 'finance:write', createAction: 'quote' },
  customers: { id: 'customers', title: 'Customers', description: 'Review customer facilities and create customer profiles.', endpoint: '/admin/customer-profiles', readScope: 'administration:read', writeScope: 'administration:write', createAction: 'customer' },
  contacts: { id: 'contacts', title: 'Contacts & leads', description: 'Add contacts, review enquiries and update follow-up status.', endpoint: '/admin/enquiries/leads', readScope: 'enquiries:read', writeScope: 'enquiries:write', createAction: 'contact' },
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

export const demoAccounts: DemoAccount[] = [
  { id: 'staff', label: 'Staff member', audience: 'staff', account: { id: 'demo-staff', name: 'Taylor Kim', email: 'staff.preview@example.test', roles: ['staff'], scopes: ['finance:read','finance:write','administration:read','crm:read','enquiries:read','enquiries:write'] }, organization: 'OPES Health Systems · Demo', reference: 'DEMO-STF-001' },
  { id: 'customer', label: 'Customer', audience: 'customer', account: { id: 'demo-customer', name: 'Maya Clinic Admin', email: 'customer.preview@example.test', roles: ['customer'], scopes: ['customer:read','customer:write'] }, organization: 'Example Care Clinic', reference: 'DEMO-CUS-001' },
  { id: 'practitioner', label: 'Health practitioner', audience: 'practitioner', account: { id: 'demo-practitioner', name: 'Dr. Amina Ncube', email: 'practitioner.preview@example.test', roles: ['practitioner'], scopes: ['practitioner:read','practitioner:write'] }, organization: 'Example Community Hospital', reference: 'DEMO-PRA-001' },
  { id: 'tester', label: 'Tester', audience: 'tester', account: { id: 'demo-tester', name: 'Jordan Bell', email: 'tester.preview@example.test', roles: ['tester'], scopes: ['testing:read','testing:write'] }, organization: 'OPES Product Testing · Demo', reference: 'DEMO-TST-001' },
  { id: 'partner', label: 'Partner', audience: 'partner', account: { id: 'demo-partner', name: 'Sam Okoro', email: 'partner.preview@example.test', roles: ['partner'], scopes: [] }, organization: 'Example Health Network', reference: 'DEMO-PAR-001' },
];

// Demo logins open an empty, local-only workspace: no sample records ship with the app.
// Anything created during a demo session stays in memory and is cleared on exit.

type SessionValue = {
  account: Account | null; audience: Audience | null; token: Token | null; demo: DemoAccount | null; demoRows: Record<string, ApiRecord[]>;
  setAccount: (value: Account) => void; setAccessToken: (value: Token | null) => Promise<void>; startDemo: (id: string) => DemoAccount;
  clearSession: () => Promise<void>; rowsFor: (feature: FeatureId) => Promise<ApiRecord[]>;
  request: <T = unknown>(path: string, method?: string, body?: unknown, idempotencyKey?: string) => Promise<T>;
  mutateDemo: (feature: FeatureId, action: string, body?: ApiRecord, recordId?: string) => ApiRecord;
  isAllowed: (feature: Feature, write?: boolean) => boolean;
};
const SessionContext = createContext<SessionValue | null>(null);

export function SessionProvider({ children }: { children: ReactNode }) {
  const [account, setAccountState] = useState<Account | null>(null);
  const [audience, setAudience] = useState<Audience | null>(null);
  const [token, setTokenState] = useState<Token | null>(null);
  const [demo, setDemo] = useState<DemoAccount | null>(null);
  const [demoRows, setDemoRows] = useState<Record<string, ApiRecord[]>>({});
  const tokenRef = useRef<Token | null>(null);
  const refreshInFlight = useRef<Promise<Token | null> | null>(null);

  const setAccount = (value: Account) => {
    setDemo(null); setDemoRows({}); setAccountState(value);
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

  const startDemo = (id: string) => {
    const next = demoAccounts.find(item => item.id === id);
    if (!next) throw new Error('Demo workspace not found.');
    setDemo(next); setAccountState(next.account); setAudience(next.audience); setTokenState(null); tokenRef.current = null; setDemoRows({});
    return next;
  };

  const clearSession = async () => {
    await setAccessToken(null); setAccountState(null); setAudience(null); setDemo(null); setDemoRows({});
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
    // One key per logical request so a retry after token refresh cannot create a duplicate write.
    const key = !['GET', 'HEAD', 'OPTIONS'].includes(method.toUpperCase()) ? idempotencyKey ?? `mobile-${Date.now()}-${Math.random().toString(36).slice(2)}` : undefined;
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
      const fieldErrors = data?.errors && typeof data.errors === 'object' ? Object.values(data.errors).flat().filter(value => typeof value === 'string').join(' ') : '';
      const requestId = typeof data?.request_id === 'string' ? t(' Reference: {id}', { id: data.request_id }) : '';
      throw new Error(`${data?.detail || data?.message || fieldErrors || data?.title || t('Request failed ({status})', { status: response.status })}${requestId}`);
    }
    return (data?.data ?? data) as T;
  };

  const rowsFor = async (id: FeatureId): Promise<ApiRecord[]> => {
    if (demo) return demoRows[id] ?? [];
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

  const mutateDemo = (feature: FeatureId, action: string, body: ApiRecord = {}, recordId?: string): ApiRecord => {
    if (!demo) throw new Error('This action is only available in a demo workspace.');
    const id = recordId ?? `DEMO-${Date.now()}`;
    const defaults: Partial<Record<FeatureId, ApiRecord>> = {
      quotes: { status: 'draft' }, customers: { status: 'pending' }, contacts: { status: 'new' },
      deals: { stage: 'qualification' }, serviceRequests: { status: 'submitted' }, tickets: { status: 'open', replies: [] },
      partnerApps: { status: 'pending' }, partnerApplication: { status: 'submitted' }, assignments: { status: 'open' },
    };
    let result: ApiRecord = { ...(defaults[feature] ?? {}), ...body, id, demo: true };
    if (feature === 'quotes' && Array.isArray(body.items)) {
      const total = (body.items as ApiRecord[]).reduce((sum, item) => sum + Number(item.quantity ?? 0) * Number(item.unit_price ?? 0), 0);
      result = { ...result, subtotal: total, total };
    }
    setDemoRows(current => {
      const rows = [...(current[feature] ?? [])];
      let certificates = current.certificates ?? [];
      if (action === 'update-profile') {
        result = { ...rows[0], ...body, id: 'profile', demo: true }; rows[0] = result;
      } else if (action === 'update-status' && recordId) {
        const index = rows.findIndex(row => String(row.id) === recordId);
        if (index >= 0) { rows[index] = { ...rows[index], ...body }; result = rows[index]; }
      } else if (action === 'reply' && recordId) {
        const index = rows.findIndex(row => String(row.id) === recordId);
        if (index >= 0) { const replies = Array.isArray(rows[index].replies) ? rows[index].replies as ApiRecord[] : []; rows[index] = { ...rows[index], replies: [...replies, { body: body.body, author: demo.account.name, demo: true }] }; result = rows[index]; }
      } else if (action === 'enroll' && recordId) {
        const index = rows.findIndex(row => String(row.slug ?? row.id) === recordId);
        if (index >= 0) { rows[index] = { ...rows[index], status: 'enrolled', progress_percent: 0, completed_lesson_ids: rows[index].completed_lesson_ids ?? [] }; result = rows[index]; }
      } else if (action === 'complete-lesson' && recordId) {
        const index = rows.findIndex(row => String(row.slug ?? row.id) === recordId);
        if (index >= 0) {
          const course = rows[index];
          const lessonIds = Array.isArray(course.completed_lesson_ids) ? course.completed_lesson_ids as unknown[] : [];
          const completedLessonIds = [...new Set([...lessonIds.map(String), String(body.lesson_id)])];
          const lessons = Array.isArray(course.lessons) ? course.lessons as ApiRecord[] : [];
          const progressPercent = lessons.length ? Math.round(completedLessonIds.length / lessons.length * 100) : 0;
          rows[index] = { ...course, completed_lesson_ids: completedLessonIds, progress_percent: progressPercent, status: progressPercent === 100 ? 'completed' : 'in_progress' };
          result = rows[index];
          if (progressPercent === 100 && !certificates.some(certificate => String(certificate.course_id) === String(course.id))) {
            const certificate: ApiRecord = { id: `DEMO-CERT-${Date.now()}`, course_id: course.id, title: course.title, certificate_number: `OHS-DEMO-${Date.now()}`, issued_at: new Date().toISOString(), status: 'issued', demo: true };
            certificates = [certificate, ...certificates];
          }
        }
      } else if (action === 'bug-report' && recordId) {
        const index = rows.findIndex(row => String(row.id) === recordId);
        if (index >= 0) { const reports = Array.isArray(rows[index].bug_reports) ? rows[index].bug_reports as ApiRecord[] : []; rows[index] = { ...rows[index], bug_reports: [...reports, result] }; }
      } else {
        rows.unshift(result);
      }
      return { ...current, [feature]: rows, ...(action === 'bug-report' ? { bugReports: [result, ...(current.bugReports ?? [])] } : {}), ...(feature === 'courses' ? { certificates } : {}) };
    });
    return result;
  };

  const isAllowed = (feature: Feature, write = false) => {
    if (demo) return true;
    if (!feature[write ? 'writeScope' : 'readScope']) return true;
    return !!account?.scopes.includes(feature[write ? 'writeScope' : 'readScope'] as string);
  };

  // Function closures read session state represented in the dependency list; keep a stable context value for route screens.
  // eslint-disable-next-line react-hooks/exhaustive-deps
  const value = useMemo(() => ({ account, audience, token, demo, demoRows, setAccount, setAccessToken, startDemo, clearSession, rowsFor, request, mutateDemo, isAllowed }), [account, audience, token, demo, demoRows]);
  return <SessionContext.Provider value={value}>{children}</SessionContext.Provider>;
}

export function useSession() {
  const value = useContext(SessionContext);
  if (!value) throw new Error('useSession must be used inside SessionProvider.');
  return value;
}

export { API, CLIENT_ID, TOKEN_KEY, discovery };
