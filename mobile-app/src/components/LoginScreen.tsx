import * as AuthSession from 'expo-auth-session';
import * as SecureStore from 'expo-secure-store';
import * as WebBrowser from 'expo-web-browser';
import { Redirect, useLocalSearchParams, useRouter } from 'expo-router';
import { useEffect, useRef, useState } from 'react';
import { Platform, Pressable, Text, View } from 'react-native';
import { Account, CLIENT_ID, demoAccounts, discovery, TOKEN_KEY, Token, useSession } from '../context/SessionContext';
import { ActionButton, Brand, Card, Heading, Notice, Page, colors, radius } from './ui';
import { AppIcon, getRoleIconName } from './icons';
import { t } from '../i18n';

WebBrowser.maybeCompleteAuthSession();

export default function LoginScreen() {
  const router = useRouter();
  const { registered } = useLocalSearchParams<{ registered?: string }>();
  const session = useSession();
  const [busy, setBusy] = useState(false);
  // Native launches start by restoring a saved session; keep sign-in and demo entry locked until that finishes.
  const [restoring, setRestoring] = useState(Platform.OS !== 'web');
  // Sample workspaces are secondary: hidden until asked for so real users see one clear sign-in path.
  const [showDemos, setShowDemos] = useState(false);
  const [message, setMessage] = useState('');
  const autoLoginStarted = useRef(false);
  const handledResponse = useRef(false);
  const redirectUri = AuthSession.makeRedirectUri({ scheme: 'opeshealth', path: 'oauth/callback' });
  const expoGo = redirectUri.startsWith('exp://');
  const [request, response, promptAsync] = AuthSession.useAuthRequest({
    clientId: CLIENT_ID || 'app-client-not-configured', redirectUri,
    responseType: AuthSession.ResponseType.Code, scopes: ['identity:read','identity:write','finance:read','finance:write','administration:read','administration:write','crm:read','crm:write','enquiries:read','enquiries:write','customer:read','customer:write','practitioner:read','practitioner:write','testing:read','testing:write','partner:read','partner:write'],
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
      if (!user || !Array.isArray(user.roles) || !Array.isArray(user.scopes)) throw new Error(t('Your account could not be loaded. Please sign in again.'));
      session.setAccount(user);
      router.replace('/workspace');
    }).catch(async error => {
      await session.clearSession();
      if (error instanceof Error) setMessage(t('Saved sign-in expired. Sign in again. {reason}', { reason: error.message }));
    }).finally(() => { setBusy(false); setRestoring(false); });
  }, [router, session]);

  useEffect(() => {
    if (response?.type === 'error') {
      if (handledResponse.current) return;
      handledResponse.current = true;
      const text = response.params?.error_description ?? response.error?.message ?? t('Sign-in could not be completed. Try again.');
      void Promise.resolve().then(() => { setBusy(false); setMessage(text); }); return;
    }
    if (response?.type !== 'success' || !request?.codeVerifier) return;
    if (handledResponse.current) return;
    handledResponse.current = true;
    AuthSession.exchangeCodeAsync({ clientId: CLIENT_ID, code: response.params.code, redirectUri, extraParams: { code_verifier: request.codeVerifier } }, discovery)
      .then(async result => {
        setBusy(true); setMessage(t('Completing secure sign-in…'));
        const token: Token = { access_token: result.accessToken, refresh_token: result.refreshToken, expires_at: Date.now() + (result.expiresIn ?? 900) * 1000, scope: result.scope };
        await session.setAccessToken(token);
        const user = await session.request<Account>('/me');
        if (!user || !Array.isArray(user.roles) || !Array.isArray(user.scopes)) throw new Error(t('The signed-in account response is incomplete.'));
        session.setAccount(user); setMessage(''); router.replace('/workspace');
      })
      .catch(error => setMessage(error instanceof Error ? error.message : t('Sign-in could not be completed.')))
      .finally(() => setBusy(false));
  }, [response, request, redirectUri, router, session]);

  const signIn = async () => {
    setMessage('');
    if (Platform.OS === 'web') { setMessage(t('Secure account sign-in opens in the installed OPES Health app. This web preview stays on this screen.')); return; }
    if (expoGo) { setMessage(t('Expo Go cannot receive the OPES Health sign-in callback. Install the OPES Health app build to sign in to your account.')); return; }
    if (!CLIENT_ID) { setMessage(t('Secure sign-in is not provisioned in this app build. Please contact OPES Health support.')); return; }
    handledResponse.current = false;
    setBusy(true);
    try { await promptAsync(); } catch (error) { setMessage(error instanceof Error ? error.message : t('Could not open secure sign-in.')); setBusy(false); }
  };

  const chooseDemo = (id: string) => { session.startDemo(id); router.replace('/workspace'); };

  // Already signed in (e.g. after a language switch remounts navigation): go straight to the workspace.
  if (session.account) return <Redirect href="/workspace" />;

  return <Page>
    <Brand />
    <Heading eyebrow={t('Secure account access')} title={t('OPES Health')} subtitle={t('Sign in to your OPES account. Your role and permissions open the right workspace automatically.')} />
    {registered === '1' && <Notice tone="success">{t('Your account was created on OPES Health. Continue with secure sign-in to open your live workspace.')}</Notice>}
    <ActionButton label={t('Sign in securely')} onPress={signIn} disabled={busy || restoring || (!request && Platform.OS !== 'web')} busy={busy || restoring} />
    <Text style={{ color: colors.muted, fontSize: 12, lineHeight: 18, textAlign: 'center', marginBottom: 18 }}>
      {t('Sign-in uses OAuth 2.0 with PKCE. The secure OPES sign-in sheet returns you to this app after authentication.')}
    </Text>
    <Pressable accessibilityRole="button" onPress={() => router.push('/signup' as never)} style={{ alignSelf: 'center', padding: 10, marginBottom: 14 }}>
      <Text style={{ color: colors.blue, fontSize: 14, fontWeight: '800' }}>{t('Create an OPES account')}</Text>
    </Pressable>
    <Pressable accessibilityRole="button" onPress={() => router.push('/forgot-password' as never)} style={{ alignSelf: 'center', padding: 10, marginBottom: 14 }}>
      <Text style={{ color: colors.blue, fontSize: 14, fontWeight: '700' }}>Forgot your password?</Text>
    </Pressable>
    {!!message && <Notice tone={[t('Completing secure sign-in…'), t('Secure account sign-in opens in the installed OPES Health app. This web preview stays on this screen.')].includes(message) ? 'info' : 'error'}>{message}</Notice>}
    <View style={{ borderTopWidth: 1, borderTopColor: colors.border, marginTop: 18, paddingTop: 8 }}>
      <Pressable accessibilityRole="button" accessibilityState={{ expanded: showDemos }} onPress={() => setShowDemos(!showDemos)} style={{ minHeight: 48, flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: 6 }}>
        <Text style={{ color: colors.blue, fontSize: 14, fontWeight: '700' }}>{showDemos ? t('Hide sample workspaces') : t('Just looking? Try a sample workspace')}</Text>
        <AppIcon name="chevronRight" size={16} color={colors.blue} />
      </Pressable>
    </View>
    {showDemos && <>
    <Text style={{ color: colors.muted, fontSize: 13, lineHeight: 19, textAlign: 'center', marginBottom: 12 }}>{t('Fictional accounts for trying the app. Nothing is sent to OPES, and changes reset when you leave.')}</Text>
    {demoAccounts.map(demo => <Pressable key={demo.id} disabled={busy || restoring} onPress={() => chooseDemo(demo.id)} accessibilityRole="button">
      <Card style={{ flexDirection: 'row', alignItems: 'center', gap: 12 }}>
        <View style={{ width: 44, height: 44, borderRadius: radius.md, backgroundColor: colors.tile, alignItems: 'center', justifyContent: 'center' }}><AppIcon name={getRoleIconName(demo.id)} size={22} color={colors.navy} /></View>
        <View style={{ flex: 1, minWidth: 0 }}><Text style={{ color: colors.navy, fontSize: 15, fontWeight: '700', flexShrink: 1 }}>{t(demo.label)}</Text><Text style={{ color: colors.muted, fontSize: 13, marginTop: 3, flexShrink: 1 }}>{t('{name} · tap to explore', { name: demo.account.name })}</Text></View>
        <AppIcon name="chevronRight" size={20} color={colors.muted} />
      </Card>
    </Pressable>)}
    </>}
  </Page>;
}
