import * as AuthSession from 'expo-auth-session';
import * as SecureStore from 'expo-secure-store';
import { createContext, ReactNode, useContext, useMemo, useRef, useState } from 'react';
import { Platform } from 'react-native';

export type Audience = 'staff' | 'customer' | 'practitioner' | 'tester' | 'partner';
export type Account = { id: string; name: string; email: string; roles: string[]; scopes: string[] };
export type ApiRecord = Record<string, unknown>;
export type Token = { access_token: string; refresh_token?: string; expires_at?: number; scope?: string };
export type FeatureId = 'quotes' | 'customers' | 'contacts' | 'partnerApps' | 'deals' | 'serviceRequests' | 'tickets' | 'invoices' | 'customerProfile' | 'practitionerProfile' | 'courses' | 'certificates' | 'assignments' | 'bugReports' | 'partnerships' | 'partnerApplication' | 'notifications';
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
  notifications: { id: 'notifications', title: 'Notifications', description: 'Review account updates and mark alerts as read.', endpoint: '/notifications', readScope: 'identity:read', writeScope: 'identity:write' },
};

export const featureIds: Record<Audience, FeatureId[]> = {
  staff: ['quotes', 'customers', 'contacts', 'partnerApps', 'deals'],
  customer: ['serviceRequests', 'tickets', 'invoices', 'customerProfile'],
  practitioner: ['practitionerProfile', 'courses', 'certificates'],
  tester: ['assignments', 'bugReports'],
  partner: ['partnerships', 'partnerApplication'],
};

export const demoAccounts: DemoAccount[] = [
  { id: 'staff', label: 'Staff member', audience: 'staff', account: { id: 'demo-staff', name: 'Taylor Kim', email: 'staff.preview@example.test', roles: ['staff'], scopes: ['finance:read','finance:write','administration:read','crm:read','enquiries:read','enquiries:write'] }, organization: 'OPES Health Systems · Demo', reference: 'DEMO-STF-001' },
  { id: 'customer', label: 'Customer', audience: 'customer', account: { id: 'demo-customer', name: 'Maya Clinic Admin', email: 'customer.preview@example.test', roles: ['customer'], scopes: ['customer:read','customer:write'] }, organization: 'Example Care Clinic', reference: 'DEMO-CUS-001' },
  { id: 'practitioner', label: 'Health practitioner', audience: 'practitioner', account: { id: 'demo-practitioner', name: 'Dr. Amina Ncube', email: 'practitioner.preview@example.test', roles: ['practitioner'], scopes: ['practitioner:read','practitioner:write'] }, organization: 'Example Community Hospital', reference: 'DEMO-PRA-001' },
  { id: 'tester', label: 'Tester', audience: 'tester', account: { id: 'demo-tester', name: 'Jordan Bell', email: 'tester.preview@example.test', roles: ['tester'], scopes: ['testing:read','testing:write'] }, organization: 'OPES Product Testing · Demo', reference: 'DEMO-TST-001' },
  { id: 'partner', label: 'Partner', audience: 'partner', account: { id: 'demo-partner', name: 'Sam Okoro', email: 'partner.preview@example.test', roles: ['partner'], scopes: [] }, organization: 'Example Health Network', reference: 'DEMO-PAR-001' },
];

const sampleRows: Partial<Record<FeatureId, ApiRecord[]>> = {
  quotes: [
    { id: 'DEMO-Q-1042', reference: 'DEMO-Q-1042', title: 'Clinic management starter package', status: 'draft', currency: 'XAF', total: 1850000, valid_until: '2026-12-15', items: [{ product_name: 'Clinic setup and onboarding', quantity: 1, unit_price: 1850000 }] },
    { id: 'DEMO-Q-1041', reference: 'DEMO-Q-1041', title: 'Laboratory workflow deployment', status: 'sent', currency: 'XAF', total: 3250000, valid_until: '2026-12-01', items: [{ product_name: 'Laboratory workflow deployment', quantity: 1, unit_price: 3250000 }] },
  ],
  customers: [{ id: 'DEMO-C-021', name: 'Example Care Clinic', email: 'office@example.test', facility_name: 'Example Care Clinic', country: 'Cameroon', city: 'Douala', status: 'active' }, { id: 'DEMO-C-022', name: 'Northside Diagnostics', email: 'team@example.test', facility_name: 'Northside Diagnostics', country: 'Cameroon', city: 'Yaoundé', status: 'pending' }],
  contacts: [{ id: 'DEMO-L-101', name: 'Maya Nfor', email: 'maya@example.test', phone: '+237 600 000 010', facility_type: 'clinic', products: 'OPES EMR', message: 'Interested in a clinic onboarding discussion.', status: 'new' }, { id: 'DEMO-L-102', name: 'Kwame Owusu', email: 'kwame@example.test', phone: '+233 200 000 010', facility_type: 'hospital', products: 'OPES Hospital', message: 'Requested follow-up about a regional deployment.', status: 'contacted' }],
  partnerApps: [{ id: 'DEMO-PA-011', organization_name: 'Example Health Network', contact_name: 'Sam Okoro', email: 'partner@example.test', partner_type: 'referral', status: 'pending' }, { id: 'DEMO-PA-010', organization_name: 'CareLink Group', contact_name: 'Nia Mensah', email: 'carelink@example.test', partner_type: 'technology', status: 'reviewing' }],
  deals: [{ id: 'DEMO-D-041', title: 'Regional hospital group rollout', value: 12500000, currency: 'XAF', probability: 60, status: 'open' }, { id: 'DEMO-D-040', title: 'Clinic network support agreement', value: 4200000, currency: 'XAF', probability: 35, status: 'qualification' }],
  serviceRequests: [{ id: 'DEMO-SR-201', product_slug: 'opes-emr', description: 'Onboarding for our new clinic location', preferred_date: '2026-12-10', status: 'scheduled' }],
  tickets: [{ id: 'DEMO-T-301', subject: 'Help with user access', description: 'We need to invite two additional staff members.', type: 'support', priority: 'medium', status: 'open', replies: [{ body: 'Your support request is in the queue.', author: 'OPES support' }] }],
  invoices: [{ id: 'DEMO-I-501', reference: 'DEMO-I-501', description: 'November platform subscription', currency: 'XAF', total: 250000, status: 'due', due_date: '2026-12-01' }, { id: 'DEMO-I-500', reference: 'DEMO-I-500', description: 'Implementation milestone 1', currency: 'XAF', total: 750000, status: 'paid', due_date: '2026-10-25' }],
  customerProfile: [{ id: 'profile', name: 'Maya Clinic Admin', facility_name: 'Example Care Clinic', facility_type: 'Clinic', country: 'Cameroon', city: 'Douala', phone: '+237 600 000 001', address: 'Demo Avenue' }],
  practitionerProfile: [{ id: 'profile', name: 'Dr. Amina Ncube', specialty: 'Family medicine', workplace_name: 'Example Community Hospital', workplace_city: 'Douala', workplace_country: 'Cameroon', years_of_experience: 8, bio: 'Fictional demo profile for mobile workflow preview.' }],
  courses: [
    { id: 'DEMO-COURSE-01', slug: 'safe-digital-records', title: 'Safe digital health records', description: 'Learn the everyday practices that keep digital health records accurate, private and useful to care teams.', level: 'Beginner', duration_hours: 1, status: 'available', progress_percent: 0, completed_lesson_ids: [], lessons: [
      { id: 101, title: 'A reliable patient record', duration_minutes: 8, content: 'A good digital record is accurate, timely and attributable. Record information in the correct patient chart, identify the source, and correct mistakes through the approved amendment process. Never create a second chart to work around a duplicate record.\n\nBefore saving, confirm the patient identity and review the key fields for completeness.' },
      { id: 102, title: 'Protecting health information', duration_minutes: 9, content: 'Use your own account and keep your sign-in credentials private. Lock the screen when you step away. Access only the records needed for your assigned work, and avoid discussing identifiable information in public spaces.\n\nIf information is sent to the wrong person or device, report it promptly through your organization’s incident process.' },
      { id: 103, title: 'Safe handover and continuity', duration_minutes: 7, content: 'A useful handover states what changed, what action is needed, who is responsible and when follow-up is due. Use the approved handover fields so the next team member can find the information.\n\nThis learning module supports safe information practice; it does not replace local clinical policy or professional judgement.' },
    ] },
    { id: 'DEMO-COURSE-02', slug: 'clinic-data-quality', title: 'Clinic data quality essentials', description: 'Practice simple checks that improve completeness and consistency in clinic reporting.', level: 'Intermediate', duration_hours: 1, status: 'in_progress', progress_percent: 50, completed_lesson_ids: [201], lessons: [
      { id: 201, title: 'Completeness and consistency', duration_minutes: 10, content: 'Completeness means required fields are present. Consistency means values agree across related records and time periods. Use the same agreed definitions across a team, and resolve unclear entries with the record owner instead of guessing.\n\nA weekly review of missing fields can reveal where a workflow or form needs improvement.' },
      { id: 202, title: 'Reviewing a data-quality issue', duration_minutes: 12, content: 'Start with the source record, identify the affected field, and determine whether the issue is a one-off or a repeated pattern. Correct individual records through the approved process, then address the underlying cause with training or a workflow change.\n\nKeep an audit trail of meaningful changes and follow your organization’s retention and access rules.' },
    ] },
    { id: 'DEMO-COURSE-03', slug: 'respectful-digital-communication', title: 'Respectful digital communication', description: 'Build clear, professional and privacy-aware communication habits for digital care teams.', level: 'Beginner', duration_hours: 1, status: 'completed', progress_percent: 100, completed_lesson_ids: [301, 302], lessons: [
      { id: 301, title: 'Clear and respectful messages', duration_minutes: 7, content: 'Use concise language, a clear subject and a specific request. Confirm the intended recipient before sending. Keep messages respectful and professional, including when a conversation is time-sensitive.' },
      { id: 302, title: 'Choosing the right channel', duration_minutes: 8, content: 'Use approved channels for work information. Share only the minimum necessary information with people who need it for their role. Move urgent clinical matters to the escalation channel defined by your workplace.' },
    ] },
  ],
  certificates: [{ id: 'DEMO-CERT-01', title: 'Respectful digital communication', course_id: 'DEMO-COURSE-03', certificate_number: 'OHS-DEMO-2026-014', issued_at: '2026-09-12', status: 'issued', reference: 'DEMO-CERT-01' }],
  assignments: [{ id: 'DEMO-TA-01', title: 'Android sign-in smoke test', status: 'in_progress', due_date: '2026-12-05', product: 'OPES Health mobile' }, { id: 'DEMO-TA-02', title: 'Customer service request flow', status: 'pending', due_date: '2026-12-08', product: 'OPES Health mobile' }],
  bugReports: [{ id: 'DEMO-BR-01', subject: 'Sample display issue', description: 'Fictional issue used to preview tester workflows.', priority: 'low', status: 'triaged', product: 'OPES Health mobile' }],
  notifications: [{ id: 'DEMO-N-01', title: 'Welcome to your OPES workspace', body: 'Your account workspace is ready to explore.', read_at: null, created_at: '2026-11-01T09:00:00Z' }],
  partnerships: [{ id: 'DEMO-P-01', name: 'Example Health Network', country: 'Cameroon', type: 'Community health' }, { id: 'DEMO-P-02', name: 'CareLink Group', country: 'Ghana', type: 'Technology partner' }],
};

function cloneSamples(): Record<string, ApiRecord[]> {
  return Object.fromEntries(Object.entries(sampleRows).map(([key, rows]) => [key, JSON.parse(JSON.stringify(rows ?? [])) as ApiRecord[]]));
}

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
    setDemo(next); setAccountState(next.account); setAudience(next.audience); setTokenState(null); tokenRef.current = null; setDemoRows(cloneSamples());
    return next;
  };

  const clearSession = async () => {
    await setAccessToken(null); setAccountState(null); setAudience(null); setDemo(null); setDemoRows({});
  };

  const refreshToken = async (): Promise<Token | null> => {
    const active = tokenRef.current;
    if (!active?.refresh_token || !active.expires_at || active.expires_at > Date.now() + 45000) return active;
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
    const active = await refreshToken();
    const headers: Record<string, string> = { Accept: 'application/json' };
    if (active?.access_token) headers.Authorization = `Bearer ${active.access_token}`;
    if (body !== undefined) { headers['Content-Type'] = 'application/json'; headers['Idempotency-Key'] = idempotencyKey ?? `mobile-${Date.now()}-${Math.random().toString(36).slice(2)}`; }
    const response = await fetch(`${API}${path}`, { method, headers, ...(body !== undefined ? { body: JSON.stringify(body) } : {}) });
    const data = await response.json().catch(() => ({}));
    if (response.status === 401) {
      await clearSession();
      throw new Error('Your session has expired. Sign in again to continue.');
    }
    if (!response.ok) {
      const fieldErrors = data?.errors && typeof data.errors === 'object' ? Object.values(data.errors).flat().filter(value => typeof value === 'string').join(' ') : '';
      const requestId = typeof data?.request_id === 'string' ? ` Reference: ${data.request_id}` : '';
      throw new Error(`${data?.detail || data?.message || fieldErrors || data?.title || `Request failed (${response.status})`}${requestId}`);
    }
    return (data?.data ?? data) as T;
  };

  const rowsFor = async (id: FeatureId): Promise<ApiRecord[]> => {
    if (demo) return demoRows[id] ?? [];
    const feature = featureCatalog[id];
    if (!feature || !isAllowed(feature)) throw new Error('Your account does not have permission to view this workspace.');
    const result = await request<unknown>(feature.endpoint);
    if (Array.isArray(result)) return result as ApiRecord[];
    if (result && typeof result === 'object') {
      const obj = result as Record<string, unknown>;
      for (const key of ['items','data','results','records','quotes','profiles','profile','customerProfiles','leads','applications','partnerApplications','deals','serviceRequests','tickets','reports','assignments','bug_reports','bugReports','invoices','courses','certificates','notifications']) if (Array.isArray(obj[key])) return obj[key] as ApiRecord[];
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
