import { StatusBar } from 'expo-status-bar';
import * as AuthSession from 'expo-auth-session';
import * as SecureStore from 'expo-secure-store';
import * as WebBrowser from 'expo-web-browser';
import { useEffect, useMemo, useRef, useState } from 'react';
import {
  ActivityIndicator, Image, Linking, Platform, Pressable, SafeAreaView,
  ScrollView, StyleSheet, Text, View,
} from 'react-native';

WebBrowser.maybeCompleteAuthSession();

const API = process.env.EXPO_PUBLIC_OHS_API_URL ?? 'https://opeshealthsystems.com/api/v1';
const CLIENT_ID = process.env.EXPO_PUBLIC_OHS_OAUTH_CLIENT_ID ?? '';
const REDIRECT_URI = AuthSession.makeRedirectUri({ scheme: 'opeshealth', path: 'oauth/callback' });
const TOKEN_KEY = 'opes.health.session.v1';

type Audience = 'staff' | 'customer' | 'practitioner' | 'tester' | 'partner';
type Feature = { label: string; endpoint: string; scope: string; note?: string };
type Profile = { id: string; name: string; email: string; roles: string[]; scopes: string[] };
type Token = { access_token: string; refresh_token?: string; expires_in?: number; expires_at?: number; scope?: string };

const requestedScopes = ['identity:read', 'finance:read', 'finance:write', 'administration:read', 'administration:write', 'crm:read', 'crm:write', 'customer:read', 'customer:write', 'practitioner:read', 'practitioner:write', 'testing:read', 'testing:write'];
const workspaceTitles: Record<Audience, string> = {
  staff: 'Staff & administrators', customer: 'Customer account', practitioner: 'Practitioner workspace',
  tester: 'Testing workspace', partner: 'Partner workspace',
};

function accountWorkspace(account: Profile): Audience | null {
  const roles = account.roles.map(role => role.toLowerCase().replace(/[^a-z0-9]+/g, '_'));
  const has = (...names: string[]) => roles.some(role => names.includes(role));
  if (has('super_admin', 'admin', 'staff', 'employee', 'manager', 'finance', 'accountant', 'operations', 'hr', 'support')) return 'staff';
  if (roles.some(role => /(^|_)(practitioner|clinician|health_practitioner|doctor|nurse)(_|$)/.test(role)) || account.scopes.includes('practitioner:read')) return 'practitioner';
  if (roles.some(role => /(^|_)(tester|quality_assurance|qa_tester)(_|$)/.test(role)) || account.scopes.includes('testing:read')) return 'tester';
  if (roles.some(role => /(^|_)(partner|vendor|institution)(_|$)/.test(role))) return 'partner';
  if (has('customer', 'client', 'patient') || account.scopes.includes('customer:read')) return 'customer';
  return null;
}

const features: Record<Audience, Feature[]> = {
  staff: [
    { label: 'Quotes', endpoint: '/finance/quotes', scope: 'finance:read' },
    { label: 'Customer profiles', endpoint: '/admin/customer-profiles', scope: 'administration:read' },
    { label: 'Partner applications', endpoint: '/crm/partner-applications', scope: 'crm:read' },
    { label: 'CRM deals', endpoint: '/crm/deals', scope: 'crm:read' },
    { label: 'Contact directory', endpoint: '', scope: 'crm:read', note: 'A contact directory endpoint is not in the current live API contract.' },
  ],
  customer: [
    { label: 'Service requests', endpoint: '/customer/service-requests', scope: 'customer:read' },
    { label: 'Support tickets', endpoint: '/customer/tickets', scope: 'customer:read' },
    { label: 'Invoices', endpoint: '/customer/invoices', scope: 'customer:read' },
    { label: 'Profile', endpoint: '/customer/profile', scope: 'customer:read' },
  ],
  practitioner: [
    { label: 'Profile', endpoint: '/practitioner/profile', scope: 'practitioner:read' },
    { label: 'Courses', endpoint: '/practitioner/courses', scope: 'practitioner:read' },
    { label: 'Certificates', endpoint: '/practitioner/certificates', scope: 'practitioner:read' },
  ],
  tester: [
    { label: 'Assignments', endpoint: '/testing/assignments', scope: 'testing:read' },
  ],
  partner: [
    { label: 'Partnership directory', endpoint: '/public/partnerships', scope: '' },
    { label: 'Partner account', endpoint: '', scope: '', note: 'Self-service partner accounts are not in the current live API contract yet.' },
  ],
};

function recordCount(value: unknown): string {
  if (Array.isArray(value)) return `${value.length} items`;
  if (value && typeof value === 'object') {
    const object = value as Record<string, unknown>;
    for (const key of ['data', 'items', 'quotes', 'profiles', 'applications', 'deals', 'assignments', 'service_requests', 'tickets']) {
      if (Array.isArray(object[key])) return `${(object[key] as unknown[]).length} items`;
    }
    return 'Connected';
  }
  return 'Connected';
}

export default function App() {
  const [audience, setAudience] = useState<Audience | null>(null);
  const [token, setToken] = useState<Token | null>(null);
  const sessionRef = useRef<Token | null>(null);
  const [profile, setProfile] = useState<Profile | null>(null);
  const [items, setItems] = useState<Record<string, string>>({});
  const [busy, setBusy] = useState(false);
  const [message, setMessage] = useState('');
  const discovery = useMemo(() => ({ authorizationEndpoint: 'https://opeshealthsystems.com/oauth/authorize', tokenEndpoint: `${API}/oauth/user-token` }), []);
  const [request, response, promptAsync] = AuthSession.useAuthRequest({
    clientId: CLIENT_ID || 'app-client-not-configured',
    redirectUri: REDIRECT_URI,
    responseType: AuthSession.ResponseType.Code,
    scopes: requestedScopes,
    usePKCE: true,
  }, discovery);

  async function saveToken(value: Token | null) {
    sessionRef.current = value;
    setToken(value);
    if (Platform.OS !== 'web') {
      if (value) await SecureStore.setItemAsync(TOKEN_KEY, JSON.stringify(value));
      else await SecureStore.deleteItemAsync(TOKEN_KEY);
    }
  }

  async function apiGet(path: string, accessToken: string) {
    let active = sessionRef.current;
    if (active?.refresh_token && active.expires_at && active.expires_at < Date.now() + 45_000) {
      const refreshed = await AuthSession.refreshAsync({ clientId: CLIENT_ID, refreshToken: active.refresh_token }, discovery);
      active = {
        access_token: refreshed.accessToken,
        refresh_token: refreshed.refreshToken ?? active.refresh_token,
        expires_in: refreshed.expiresIn,
        expires_at: Date.now() + (refreshed.expiresIn ?? 900) * 1000,
        scope: refreshed.scope,
      };
      await saveToken(active);
    }
    const response = await fetch(`${API}${path}`, { headers: { Authorization: `Bearer ${active?.access_token ?? accessToken}`, Accept: 'application/json' } });
    const data = await response.json().catch(() => ({}));
    if (!response.ok) throw new Error(data?.detail || data?.message || `Request failed (${response.status})`);
    return data?.data ?? data;
  }

  async function loadAccount(activeToken: Token) {
    const account = await apiGet('/me', activeToken.access_token) as Profile;
    setProfile(account);
    const workspace = accountWorkspace(account);
    setAudience(workspace);
    if (!workspace) {
      setItems({});
      setMessage('Your account is signed in, but it does not have a supported OPES mobile role. Contact support to review your account access.');
      return;
    }
    setMessage('');
    const available = features[workspace].filter(feature => !feature.scope || account.scopes.includes(feature.scope));
    const statuses: Record<string, string> = {};
    for (const feature of available) {
      if (!feature.endpoint) { statuses[feature.label] = feature.note ?? 'Not available in the live API contract.'; continue; }
      try { statuses[feature.label] = recordCount(await apiGet(feature.endpoint, activeToken.access_token)); }
      catch (error) { statuses[feature.label] = error instanceof Error ? error.message : 'Unavailable'; }
    }
    for (const feature of features[workspace]) {
      if (feature.scope && !account.scopes.includes(feature.scope)) statuses[feature.label] = 'This account does not have access.';
    }
    setItems(statuses);
  }

  useEffect(() => {
    if (Platform.OS === 'web') return;
    SecureStore.getItemAsync(TOKEN_KEY).then(async value => {
      if (!value) return;
      try { const stored = JSON.parse(value) as Token; setBusy(true); await saveToken(stored); await loadAccount(stored); }
      catch { await saveToken(null); }
      finally { setBusy(false); }
    });
  }, []);

  useEffect(() => {
    if (response?.type !== 'success' || !request?.codeVerifier) return;
    const code = response.params.code;
    setBusy(true); setMessage('Signing you in securely…');
    AuthSession.exchangeCodeAsync({
      clientId: CLIENT_ID,
      code,
      redirectUri: REDIRECT_URI,
      extraParams: { code_verifier: request.codeVerifier },
    }, discovery).then(async result => {
      const activeToken: Token = {
        access_token: result.accessToken,
        refresh_token: result.refreshToken,
        expires_in: result.expiresIn,
        expires_at: Date.now() + ((result.expiresIn ?? 900) * 1000),
        scope: result.scope,
      };
      await saveToken(activeToken);
      await loadAccount(activeToken);
    }).catch(error => setMessage(error instanceof Error ? error.message : 'Sign in could not be completed.'))
      .finally(() => setBusy(false));
  }, [response]);

  async function signIn() {
    if (Platform.OS === 'web') { setMessage('Sign-in is available in the installed Android preview. Use the download link on the OPES Health app page.'); return; }
    if (!CLIENT_ID) { setMessage('The reviewed OPES app client has not been provisioned yet. The API uses OAuth 2.0 with S256 PKCE; no demo login is available.'); return; }
    setMessage('');
    await promptAsync();
  }

  async function signOut() {
    await saveToken(null); setProfile(null); setItems({}); setMessage('You are signed out.');
  }

  const selectedTitle = audience ? workspaceTitles[audience] : 'Account access';
  return (
    <SafeAreaView style={styles.safe}>
      <StatusBar style="dark" />
      <ScrollView contentContainerStyle={styles.page}>
        <View style={styles.loginBrand}>
          <Image source={require('./assets/icon.png')} style={styles.loginIcon} accessibilityLabel="OPES Health app icon" />
          <Text style={styles.brand}>OPES <Text style={styles.brandLight}>HEALTH</Text></Text>
          <Text style={styles.brandCaption}>HEALTH SYSTEMS</Text>
        </View>
        <View style={styles.heading}><Text style={styles.eyebrow}>{profile ? selectedTitle.toUpperCase() : 'SECURE ACCOUNT ACCESS'}</Text><Text style={styles.title}>{profile ? `Hello, ${profile.name.split(' ')[0]}` : 'Sign in to OPES Health'}</Text><Text style={styles.subtitle}>{profile ? `${selectedTitle} · ${profile.email}` : 'Use your OPES account. Your role and permissions will open the correct workspace automatically.'}</Text></View>

        {!profile && <>
          <Pressable onPress={signIn} disabled={busy || !request} style={[styles.primary, (busy || !request) && styles.disabled]}>{busy ? <ActivityIndicator color="#fff" /> : <Text style={styles.primaryText}>Sign in securely</Text>}</Pressable>
          <Text style={styles.signinNote}>Your account role determines the workspace and available actions. No demo accounts.</Text>
        </>}

        {profile && <>
          <View style={styles.profileCard}><Text style={styles.profileLabel}>SIGNED IN AS</Text><Text style={styles.profileName}>{profile.name}</Text><Text style={styles.profileEmail}>{profile.email}</Text><Text style={styles.roleText}>{profile.roles.join(' · ') || 'Account'}</Text></View>
          {audience && <>
            <Text style={styles.sectionTitle}>Workspace</Text>
            {features[audience].map(feature => <View key={feature.label} style={styles.feature}><View style={styles.featureMark}><Text style={styles.featureCheck}>✓</Text></View><View style={styles.featureCopy}><Text style={styles.featureTitle}>{feature.label}</Text><Text style={styles.featureStatus}>{items[feature.label] ?? feature.note ?? 'Loading…'}</Text></View></View>)}
          </>}
          <Pressable onPress={signOut} style={styles.secondary}><Text style={styles.secondaryText}>Sign out</Text></Pressable>
        </>}

        {!!message && <View style={styles.message}><Text style={styles.messageText}>{message}</Text></View>}
        <View style={styles.footer}><Text style={styles.footerText}>Secure access · API-backed · No demo accounts</Text><Pressable onPress={() => Linking.openURL('https://opeshealthsystems.com/en/mobile-app')}><Text style={styles.footerLink}>App information & updates</Text></Pressable></View>
      </ScrollView>
    </SafeAreaView>
  );
}

const navy = '#082D55';
const styles = StyleSheet.create({
  safe: { flex: 1, backgroundColor: '#F5F8FC' }, page: { width: '100%', maxWidth: 560, alignSelf: 'center', paddingHorizontal: 22, paddingTop: 18, paddingBottom: 42 },
  loginBrand: { alignItems: 'center', gap: 4 }, loginIcon: { width: 86, height: 86, resizeMode: 'contain', marginBottom: 8 }, brand: { color: navy, fontSize: 20, fontWeight: '800', letterSpacing: 0.7 }, brandLight: { color: '#2977B9', fontWeight: '600' }, brandCaption: { color: '#62778B', fontSize: 10, fontWeight: '700', letterSpacing: 2 },
  heading: { alignItems: 'center', marginTop: 34, marginBottom: 28 }, eyebrow: { color: '#2C6EAA', fontSize: 11, fontWeight: '700', letterSpacing: 1.5, textAlign: 'center' }, title: { color: navy, fontSize: 30, lineHeight: 37, fontWeight: '700', marginTop: 9, textAlign: 'center' }, subtitle: { color: '#52677D', fontSize: 15, lineHeight: 22, marginTop: 8, textAlign: 'center' }, sectionTitle: { color: navy, fontSize: 17, fontWeight: '700', marginBottom: 12 },
  primary: { marginTop: 13, minHeight: 50, borderRadius: 6, backgroundColor: navy, alignItems: 'center', justifyContent: 'center' }, primaryText: { color: '#FFFFFF', fontSize: 15, fontWeight: '700' }, disabled: { opacity: 0.6 }, signinNote: { textAlign: 'center', color: '#66798B', fontSize: 12, lineHeight: 18, marginTop: 12 },
  profileCard: { marginTop: 4, marginBottom: 24, padding: 18, backgroundColor: '#FFFFFF', borderWidth: 1, borderColor: '#D9E2EC', borderRadius: 8 }, profileLabel: { color: '#59748D', fontSize: 10, letterSpacing: 1.2, fontWeight: '700' }, profileName: { color: navy, fontSize: 21, fontWeight: '700', marginTop: 8 }, profileEmail: { color: '#52677D', fontSize: 14, marginTop: 3 }, roleText: { color: '#2C6EAA', fontSize: 12, fontWeight: '600', marginTop: 12 },
  feature: { minHeight: 66, flexDirection: 'row', alignItems: 'center', backgroundColor: '#FFFFFF', borderBottomWidth: 1, borderBottomColor: '#E6ECF2', paddingHorizontal: 13, gap: 12 }, featureMark: { width: 25, height: 25, borderRadius: 5, backgroundColor: '#E8F1F8', alignItems: 'center', justifyContent: 'center' }, featureCheck: { color: '#1766A7', fontSize: 15, fontWeight: '700' }, featureCopy: { flex: 1 }, featureTitle: { color: '#183853', fontSize: 14, fontWeight: '600' }, featureStatus: { color: '#62778B', fontSize: 12, marginTop: 3 }, secondary: { marginTop: 18, minHeight: 46, borderWidth: 1, borderColor: '#BBC9D6', borderRadius: 6, alignItems: 'center', justifyContent: 'center', backgroundColor: '#FFFFFF' }, secondaryText: { color: navy, fontSize: 14, fontWeight: '600' },
  message: { marginTop: 16, padding: 12, borderRadius: 6, backgroundColor: '#FFF6E5', borderWidth: 1, borderColor: '#E8D7B5' }, messageText: { color: '#574314', fontSize: 13, lineHeight: 19 }, footer: { marginTop: 36, alignItems: 'center', gap: 8 }, footerText: { color: '#718397', fontSize: 11 }, footerLink: { color: '#1766A7', fontSize: 12, fontWeight: '600' },
});
