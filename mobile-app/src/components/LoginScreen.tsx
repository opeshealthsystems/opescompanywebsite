import * as AuthSession from 'expo-auth-session';
import * as SecureStore from 'expo-secure-store';
import * as WebBrowser from 'expo-web-browser';
import { useLocalSearchParams, useRouter } from 'expo-router';
import { useEffect, useRef, useState } from 'react';
import { Platform, Pressable, Text, View } from 'react-native';
import { Account, CLIENT_ID, demoAccounts, discovery, TOKEN_KEY, Token, useSession } from '../context/SessionContext';
import { ActionButton, Brand, Card, Heading, Notice, Page, colors } from './ui';
import { AppIcon, getRoleIconName } from './icons';

WebBrowser.maybeCompleteAuthSession();

export default function LoginScreen() {
  const router = useRouter();
  const { registered } = useLocalSearchParams<{ registered?: string }>();
  const session = useSession();
  const [busy, setBusy] = useState(false);
  const [message, setMessage] = useState('');
  const autoLoginStarted = useRef(false);
  const handledResponse = useRef(false);
  const redirectUri = AuthSession.makeRedirectUri({ scheme: 'opeshealth', path: 'oauth/callback' });
  const expoGo = redirectUri.startsWith('exp://');
  const [request, response, promptAsync] = AuthSession.useAuthRequest({
    clientId: CLIENT_ID || 'app-client-not-configured', redirectUri,
    responseType: AuthSession.ResponseType.Code, scopes: ['identity:read','identity:write','finance:read','finance:write','administration:read','administration:write','crm:read','crm:write','enquiries:read','enquiries:write','customer:read','customer:write','practitioner:read','practitioner:write','testing:read','testing:write'],
    usePKCE: true,
  }, discovery);

  useEffect(() => {
    if (Platform.OS === 'web' || autoLoginStarted.current) return;
    autoLoginStarted.current = true;
    SecureStore.getItemAsync(TOKEN_KEY).then(async value => {
      if (!value) return;
      const stored = JSON.parse(value) as Token;
      await session.setAccessToken(stored);
      const user = await session.request<Account>('/me');
      if (!user || !Array.isArray(user.roles) || !Array.isArray(user.scopes)) throw new Error('Your account could not be loaded. Please sign in again.');
      session.setAccount(user);
      router.replace('/workspace');
    }).catch(async error => {
      await session.clearSession();
      if (error instanceof Error) setMessage(`Saved sign-in expired. Sign in again. ${error.message}`);
    }).finally(() => setBusy(false));
  }, [router, session]);

  useEffect(() => {
    if (response?.type === 'error') {
      if (handledResponse.current) return;
      handledResponse.current = true;
      const text = response.params?.error_description ?? response.error?.message ?? 'Sign-in could not be completed. Try again.';
      void Promise.resolve().then(() => { setBusy(false); setMessage(text); }); return;
    }
    if (response?.type !== 'success' || !request?.codeVerifier) return;
    if (handledResponse.current) return;
    handledResponse.current = true;
    AuthSession.exchangeCodeAsync({ clientId: CLIENT_ID, code: response.params.code, redirectUri, extraParams: { code_verifier: request.codeVerifier } }, discovery)
      .then(async result => {
        setBusy(true); setMessage('Completing secure sign-in…');
        const token: Token = { access_token: result.accessToken, refresh_token: result.refreshToken, expires_at: Date.now() + (result.expiresIn ?? 900) * 1000, scope: result.scope };
        await session.setAccessToken(token);
        const user = await session.request<Account>('/me');
        if (!user || !Array.isArray(user.roles) || !Array.isArray(user.scopes)) throw new Error('The signed-in account response is incomplete.');
        session.setAccount(user); setMessage(''); router.replace('/workspace');
      })
      .catch(error => setMessage(error instanceof Error ? error.message : 'Sign-in could not be completed.'))
      .finally(() => setBusy(false));
  }, [response, request, redirectUri, router, session]);

  const signIn = async () => {
    setMessage('');
    if (Platform.OS === 'web') { setMessage('Secure account sign-in opens in the installed OPES Health app. This web preview stays on this screen.'); return; }
    if (expoGo) { setMessage('Expo Go cannot receive the OPES Health sign-in callback. Install the OPES Health app build to sign in to your account.'); return; }
    if (!CLIENT_ID) { setMessage('Secure sign-in is not provisioned in this app build. Please contact OPES Health support.'); return; }
    handledResponse.current = false;
    setBusy(true);
    try { await promptAsync(); } catch (error) { setMessage(error instanceof Error ? error.message : 'Could not open secure sign-in.'); setBusy(false); }
  };

  const chooseDemo = async (id: string) => { await session.startDemo(id); router.replace('/workspace'); };
  const healthDemos = demoAccounts.filter(demo => demo.category !== 'hotel');
  const hotelDemos = demoAccounts.filter(demo => demo.category === 'hotel');

  return <Page>
    <Brand />
    <Heading eyebrow="Secure account access" title="OPES Health" subtitle="Sign in to your OPES account. Your role and permissions open the right workspace automatically." />
    {registered === '1' && <Notice tone="success">Your account was created on OPES Health. Continue with secure sign-in to open your live workspace.</Notice>}
    <ActionButton label="Sign in securely" onPress={signIn} disabled={busy || (!request && Platform.OS !== 'web')} busy={busy} />
    <Text style={{ color: colors.muted, fontSize: 12, lineHeight: 18, textAlign: 'center', marginBottom: 18 }}>
      Sign-in uses OAuth 2.0 with PKCE. The secure OPES sign-in sheet returns you to this app after authentication.
    </Text>
    <Text style={{ color: colors.navy, fontSize: 13, lineHeight: 19, textAlign: 'center', marginBottom: 14 }}>
      Real administrators use secure sign-in and their server-assigned permissions. The super admin below is a fictional sample profile with no access to real accounts or data.
    </Text>
    <Pressable accessibilityRole="button" onPress={() => router.push('/signup' as never)} style={{ alignSelf: 'center', padding: 10, marginBottom: 14 }}>
      <Text style={{ color: colors.blue, fontSize: 14, fontWeight: '800' }}>Create an OPES account</Text>
    </Pressable>
    <Pressable accessibilityRole="button" onPress={() => router.push('/forgot-password' as never)} style={{ alignSelf: 'center', padding: 10, marginBottom: 14 }}>
      <Text style={{ color: colors.blue, fontSize: 14, fontWeight: '700' }}>Forgot your password?</Text>
    </Pressable>
    {!!message && <Notice tone={message.startsWith('Completing') ? 'info' : 'error'}>{message}</Notice>}
    <Heading eyebrow="Interactive sample mode" title="Explore health workspaces" subtitle="Tap a sample role to open its workspace. These fictional profiles never sign in to or change live OPES accounts." />
    {healthDemos.map(demo => <Pressable key={demo.id} onPress={() => void chooseDemo(demo.id)} accessibilityRole="button">
      <Card style={{ flexDirection: 'row', alignItems: 'center', gap: 12 }}>
        <View style={{ width: 44, height: 44, borderRadius: 9, backgroundColor: '#E8F1F8', alignItems: 'center', justifyContent: 'center' }}><AppIcon name={getRoleIconName(demo.id)} size={22} color={colors.navy} /></View>
        <View style={{ flex: 1, minWidth: 0 }}><Text style={{ color: colors.navy, fontSize: 15, fontWeight: '700', flexShrink: 1 }}>{demo.label}</Text><Text style={{ color: colors.muted, fontSize: 13, marginTop: 3, flexShrink: 1 }}>{demo.account.name} · tap to explore</Text></View>
        <AppIcon name="chevronRight" size={20} color={colors.muted} />
      </Card>
    </Pressable>)}
    <Heading eyebrow="Interactive hotel sample mode" title="Explore hotel operations roles" subtitle="One tap opens a role-specific hotel dashboard with fictional reservations, rooms, stock and service records. Sample actions stay on this device." />
    {hotelDemos.map(demo => <Pressable key={demo.id} onPress={() => void chooseDemo(demo.id)} accessibilityRole="button">
      <Card style={{ flexDirection: 'row', alignItems: 'center', gap: 12 }}>
        <View style={{ width: 44, height: 44, borderRadius: 9, backgroundColor: '#E8F1F8', alignItems: 'center', justifyContent: 'center' }}><AppIcon name={getRoleIconName(demo.id)} size={22} color={colors.navy} /></View>
        <View style={{ flex: 1, minWidth: 0 }}><Text style={{ color: colors.navy, fontSize: 15, fontWeight: '700', flexShrink: 1 }}>{demo.label}</Text><Text style={{ color: colors.muted, fontSize: 13, marginTop: 3, flexShrink: 1 }}>{demo.account.name} · sample only</Text></View>
        <AppIcon name="chevronRight" size={20} color={colors.muted} />
      </Card>
    </Pressable>)}
    <Notice>Sample roles, including Super admin, are simulated previews only. Demo actions cannot contact the live API or grant real permissions.</Notice>
    <Text style={{ textAlign: 'center', color: colors.muted, fontSize: 12, lineHeight: 18, marginTop: 10 }}>Demo records reset when you leave the sample workspace.</Text>
  </Page>;
}
