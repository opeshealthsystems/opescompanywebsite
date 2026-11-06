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
export type Feature = { id: FeatureId; title: string; description: string; endpoint: string; readScope?: string; writeScope?: string; createAction?: string; demoOnly?: boolean };
export type DemoAccount = { id: string; label: string; audience: Audience; account: Account; organization: string; reference: string };

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

const sampleRows: Record<Audience, Record<string, ApiRecord[]>> = {
  staff: {
    quotes: [{ id: 'DEMO-QUOTE-101', reference: 'DEMO-Q-101', title: 'Example clinic platform proposal', status: 'draft', created_at: '2026-11-01T09:00:00Z', valid_until: '2026-12-01', currency: 'XAF', subtotal: 1250000, tax_rate: 0, tax_amount: 0, total: 1250000, notes: 'Fictional preview quotation.', lead: { name: 'Amina Demo', email: 'amina@example.test', organization_name: 'Example Community Clinic', country: 'Cameroon' }, creator: { name: 'Taylor Kim', email: 'staff.preview@example.test' }, items: [{ id: 'DEMO-LINE-1', product_name: 'Clinic information system', description: 'Illustrative annual subscription', quantity: 1, unit_price: 950000, total: 950000 }, { id: 'DEMO-LINE-2', product_name: 'Implementation and training', quantity: 1, unit_price: 300000, total: 300000 }] }],
    customers: [{ id: 'DEMO-CUSTOMER-201', user_id: 201, name: 'Example Community Clinic', facility_name: 'Example Community Clinic', facility_type: 'clinic', country: 'Cameroon', city: 'Yaounde', status: 'active', email: 'clinic@example.test', phone: '+237 600 000 201' }],
    contacts: [{ id: 'DEMO-LEAD-301', name: 'Amina Demo', email: 'amina@example.test', phone: '+237 600 000 301', organization_name: 'Example Community Clinic', facility_type: 'clinic', products: 'Clinic platform', message: 'Fictional request for an introductory product discussion.', source: 'contact', status: 'new', created_at: '2026-11-02T10:30:00Z' }],
    partnerApps: [{ id: 'DEMO-PARTNER-401', organization_name: 'Example Digital Health Institute', contact_name: 'Noah Example', email: 'partner@example.test', country: 'Ghana', partner_type: 'academic', description: 'Fictional academic collaboration request for preview purposes.', status: 'pending', created_at: '2026-11-03T08:15:00Z' }],
    deals: [{ id: 'DEMO-DEAL-501', title: 'Example district health network', stage: 'proposal', value: 6800000, currency: 'XAF', probability: 50, expected_close_date: '2026-12-15', status: 'open', notes: 'Fictional opportunity for the mobile preview.' }],
    notifications: [{ id: 'DEMO-NOTICE-001', title: 'Welcome to the sample workspace', message: 'All records shown here are fictional and remain on this device.', created_at: '2026-11-06T08:00:00Z', status: 'unread' }],
  },
  customer: {
    serviceRequests: [{ id: 'DEMO-SERVICE-101', reference: 'DEMO-SR-101', type: 'training', product_slug: 'clinic-platform', description: 'Example request for a product orientation session.', preferred_date: '2026-11-20', preferred_time: '10:00', location: 'Example Care Clinic', status: 'submitted', created_at: '2026-11-04T09:15:00Z' }],
    tickets: [{ id: 'DEMO-TICKET-201', reference: 'DEMO-TK-201', subject: 'Example onboarding question', description: 'Fictional support conversation for preview purposes.', type: 'support', priority: 'normal', status: 'open', created_at: '2026-11-05T13:20:00Z', replies: [{ id: 'DEMO-REPLY-1', author: 'OPES Support Preview', body: 'This is a sample reply. Real support replies appear in your live account.', created_at: '2026-11-05T14:00:00Z' }] }],
    invoices: [{ id: 'DEMO-INVOICE-301', reference: 'DEMO-INV-301', title: 'Example service invoice', currency: 'XAF', total: 450000, due_date: '2026-12-05', status: 'pending', issued_at: '2026-11-05' }],
    customerProfile: [{ id: 'DEMO-PROFILE-C1', name: 'Maya Clinic Admin', facility_name: 'Example Care Clinic', facility_type: 'clinic', country: 'Cameroon', city: 'Douala', phone: '+237 600 000 401', address: 'Example Avenue', status: 'sample' }],
    notifications: [{ id: 'DEMO-NOTICE-002', title: 'Service request received', message: 'Your fictional training request is shown in the sample workspace.', created_at: '2026-11-04T09:16:00Z', status: 'unread' }],
  },
  practitioner: {
    practitionerProfile: [{ id: 'DEMO-PROFILE-P1', name: 'Dr. Amina Ncube', phone: '+237 600 000 501', specialty: 'Health informatics', workplace_name: 'Example Community Hospital', workplace_city: 'Bamenda', workplace_country: 'Cameroon', years_of_experience: 5, bio: 'Fictional profile for the OPES Health app preview.' }],
    courses: [
      { id: 'DEMO-COURSE-101', slug: 'health-informatics-foundations-demo', title: 'Health Informatics Foundations · Sample', description: 'A fictional preview course covering digital health workflows, information quality and safe system use.', duration_hours: 3, status: 'in_progress', progress_percent: 33, completed_lesson_ids: ['DEMO-LESSON-1'], lessons: [
        { id: 'DEMO-LESSON-1', title: 'Digital health workflows', duration_minutes: 20, content: 'A digital health workflow describes how people, information and systems work together to complete a service.\n\nWhen studying a workflow, map the people involved, the information they need, the decisions they make and the hand-offs between steps.\n\nThis sample lesson is educational preview content and contains no patient data or clinical advice.' },
        { id: 'DEMO-LESSON-2', title: 'Information quality and stewardship', duration_minutes: 25, content: 'Information quality depends on clear definitions, appropriate access and consistent capture.\n\nA stewardship plan identifies who maintains a data element, how changes are reviewed and how users report an issue.\n\nThis sample lesson is educational preview content and contains no patient data or clinical advice.' },
        { id: 'DEMO-LESSON-3', title: 'Safe adoption and improvement', duration_minutes: 30, content: 'A responsible digital health rollout includes training, accessible support and a way to learn from operational feedback.\n\nReview the workflow with the people who use it, document risks and measure whether the change improves the intended process.\n\nThis sample lesson is educational preview content and contains no patient data or clinical advice.' },
      ] },
      { id: 'DEMO-COURSE-102', slug: 'health-data-governance-demo', title: 'Health Data Governance · Sample', description: 'A fictional preview course on stewardship, access boundaries and responsible data use.', duration_hours: 2, status: 'available', progress_percent: 0, completed_lesson_ids: [], lessons: [{ id: 'DEMO-LESSON-4', title: 'Roles and access boundaries', duration_minutes: 25, content: 'A data access boundary describes which people and systems may use a dataset for a defined purpose.\n\nGood governance records the purpose, authorized users, permitted fields, retention period and review process.\n\nThis sample lesson is educational preview content and contains no patient data or clinical advice.' }] },
    ],
    certificates: [{ id: 'DEMO-CERT-501', course_id: 'DEMO-COURSE-090', title: 'Digital Health Orientation · Sample', certificate_number: 'DEMO-CERT-501', issued_at: '2026-10-28T12:00:00Z', status: 'sample' }],
    notifications: [{ id: 'DEMO-NOTICE-003', title: 'Learning progress saved', message: 'Your sample course progress is available in Courses.', created_at: '2026-11-05T10:00:00Z', status: 'unread' }],
  },
  tester: {
    assignments: [{ id: 'DEMO-ASSIGNMENT-101', reference: 'DEMO-TEST-101', title: 'Review the course catalogue', product: 'OPES Health mobile preview', status: 'in_progress', priority: 'normal', due_date: '2026-11-18', description: 'Fictional test assignment: review course navigation and report any usability issues.', bug_reports: [{ id: 'DEMO-BUG-101', subject: 'Example: course card spacing', description: 'Sample issue already filed against this preview assignment.', priority: 'low', status: 'open' }] }, { id: 'DEMO-ASSIGNMENT-102', reference: 'DEMO-TEST-102', title: 'Check account profile', product: 'OPES Health mobile preview', status: 'pending', priority: 'normal', due_date: '2026-11-22', description: 'Fictional test assignment: review profile details and save behavior.', bug_reports: [] }],
    bugReports: [{ id: 'DEMO-BUG-101', reference: 'DEMO-BUG-101', subject: 'Example: course card spacing', description: 'Sample report linked to a fictional assignment.', priority: 'low', status: 'open', assignment_id: 'DEMO-ASSIGNMENT-101', created_at: '2026-11-05T11:00:00Z' }],
    notifications: [{ id: 'DEMO-NOTICE-004', title: 'New sample assignment', message: 'A fictional assignment is ready in your Testing workspace.', created_at: '2026-11-05T08:00:00Z', status: 'unread' }],
  },
  partner: {
    partnerAccounts: [{ id: 'DEMO-ORG-101', application_id: 'DEMO-APPLICATION-101', organization_name: 'Example Health Network', partner_type: 'implementation', membership_role: 'member', country: 'Cameroon', city: 'Garoua', status: 'approved', description: 'Fictional partner organization for mobile preview purposes.' }],
    partnerships: [{ id: 'DEMO-PARTNER-DIR-1', name: 'Example Academic Health Institute', partner_type: 'academic', country: 'Ghana', description: 'Fictional institution shown in the sample directory.' }, { id: 'DEMO-PARTNER-DIR-2', name: 'Example Digital Care Network', partner_type: 'technology', country: 'Kenya', description: 'Fictional organization shown in the sample directory.' }],
    notifications: [{ id: 'DEMO-NOTICE-005', title: 'Partner workspace preview', message: 'The linked organization and directory records are fictional samples.', created_at: '2026-11-05T08:00:00Z', status: 'unread' }],
  },
};

function sampleRowsFor(audience: Audience): Record<string, ApiRecord[]> {
  return JSON.parse(JSON.stringify(sampleRows[audience])) as Record<string, ApiRecord[]>;
}

// Demo accounts use fictional local-only records. Mutations stay on-device and are cleared on exit.

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
  const mutationSalt = useRef<Promise<string> | null>(null);
  const webMutationSalt = useRef(Crypto.randomUUID());
  const pendingMutationKeys = useRef(new Map<string, PendingMutationKey>());
  const creatingMutationKeys = useRef(new Map<string, Promise<PendingMutationKey>>());

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
    setDemo(next); setAccountState(next.account); setAudience(next.audience); setTokenState(null); tokenRef.current = null; setDemoRows(sampleRowsFor(next.audience));
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
