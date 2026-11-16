import * as SecureStore from 'expo-secure-store';
import { Redirect, useLocalSearchParams, useRouter } from 'expo-router';
import { useEffect, useRef, useState } from 'react';
import { Platform, Pressable, Text, View } from 'react-native';
import { Account, API, CLIENT_ID, TOKEN_KEY, Token, useSession } from '../context/SessionContext';
import { ActionButton, Brand, Field, Heading, Notice, Page, colors } from './ui';
import { getLang, t } from '../i18n';

// The reviewed OPES Health mobile client requests its whole scope ceiling; the API trims each token to the
// scopes the signed-in account's roles allow.
const SCOPES = 'identity:read identity:write finance:read finance:write administration:read administration:write crm:read crm:write enquiries:read enquiries:write customer:read customer:write practitioner:read practitioner:write testing:read testing:write partner:read partner:write';

function form(fields: Record<string, string>): string {
  return Object.entries(fields).map(([k, v]) => `${encodeURIComponent(k)}=${encodeURIComponent(v)}`).join('&');
}

export default function LoginScreen() {
  const router = useRouter();
  const { registered } = useLocalSearchParams<{ registered?: string }>();
  const session = useSession();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [busy, setBusy] = useState(false);
  const [restoring, setRestoring] = useState(Platform.OS !== 'web');
  const [error, setError] = useState('');
  const restoreStarted = useRef(false);

  // Native launches restore a saved session before showing the form, so returning users skip sign-in.
  useEffect(() => {
    if (Platform.OS === 'web' || restoreStarted.current) return;
    restoreStarted.current = true;
    SecureStore.getItemAsync(TOKEN_KEY).then(async value => {
      if (!value) return;
      const stored = JSON.parse(value) as Token;
      await session.setAccessToken(stored);
      const user = await session.request<Account>('/me');
      if (!user || !Array.isArray(user.roles) || !Array.isArray(user.scopes)) throw new Error(t('Your account could not be loaded. Please sign in again.'));
      session.setAccount(user);
      router.replace('/workspace');
    }).catch(async () => { await session.clearSession(); }).finally(() => setRestoring(false));
  }, [router, session]);

  const signIn = async () => {
    setError('');
    const mail = email.trim();
    if (!mail || !password) { setError(t('Enter your email and password.')); return; }
    if (!CLIENT_ID) { setError(t('Sign-in is not configured in this app build. Please contact OPES Health support.')); return; }
    setBusy(true);
    try {
      const response = await fetch(`${API}/oauth/user-token`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', Accept: 'application/json', 'Accept-Language': getLang() },
        body: form({ grant_type: 'password', client_id: CLIENT_ID, username: mail, password, scope: SCOPES }),
      });
      const data = await response.json().catch(() => ({}));
      if (!response.ok) {
        setError(response.status === 403 ? t('This account is not active. Contact your administrator.')
          : response.status === 429 ? t('Too many attempts. Please wait a moment and try again.')
          : t('The email or password is incorrect.'));
        return;
      }
      const token: Token = { access_token: data.access_token, refresh_token: data.refresh_token, expires_at: Date.now() + (Number(data.expires_in) || 900) * 1000, scope: data.scope };
      await session.setAccessToken(token);
      const user = await session.request<Account>('/me');
      if (!user || !Array.isArray(user.roles) || !Array.isArray(user.scopes)) throw new Error(t('Your account could not be loaded. Please sign in again.'));
      session.setAccount(user);
      router.replace('/workspace');
    } catch (e) {
      await session.clearSession();
      setError(e instanceof Error ? e.message : t('Sign-in could not be completed. Try again.'));
    } finally { setBusy(false); }
  };

  if (session.account) return <Redirect href="/workspace" />;

  return <Page>
    <View style={{ alignItems: 'center', marginTop: 10, marginBottom: 2 }}><Brand /></View>
    <Heading title={t('Sign in')} subtitle={t('Open your OPES Health workspace. Your role and permissions set up the right tools automatically.')} />
    {registered === '1' && <Notice tone="success">{t('Your account was created on OPES Health. Sign in to open your live workspace.')}</Notice>}

    <Field label={t('Email')} value={email} onChangeText={setEmail} keyboardType="email-address" autoCapitalize="none" placeholder="you@organization.com" />
    <Field label={t('Password')} value={password} onChangeText={setPassword} secureTextEntry={!showPassword} autoCapitalize="none" />
    <Pressable accessibilityRole="button" onPress={() => setShowPassword(!showPassword)} style={{ alignSelf: 'flex-end', marginTop: -6, marginBottom: 10, paddingVertical: 6, paddingHorizontal: 4 }}>
      <Text style={{ color: colors.blue, fontSize: 12, fontWeight: '700' }}>{showPassword ? t('Hide password') : t('Show password')}</Text>
    </Pressable>

    {!!error && <Notice tone="error">{error}</Notice>}

    <ActionButton label={t('Sign in')} onPress={() => void signIn()} disabled={busy || restoring} busy={busy || restoring} />

    <Pressable accessibilityRole="button" onPress={() => router.push('/forgot-password' as never)} style={{ alignSelf: 'center', paddingVertical: 10, paddingHorizontal: 12 }}>
      <Text style={{ color: colors.blue, fontSize: 14, fontWeight: '700' }}>{t('Forgot your password?')}</Text>
    </Pressable>

    <View style={{ borderTopWidth: 1, borderTopColor: colors.border, marginTop: 18, paddingTop: 18, flexDirection: 'row', justifyContent: 'center', alignItems: 'center', gap: 6, flexWrap: 'wrap' }}>
      <Text style={{ color: colors.muted, fontSize: 14 }}>{t('New to OPES Health?')}</Text>
      <Pressable accessibilityRole="button" hitSlop={6} onPress={() => router.push('/signup' as never)}>
        <Text style={{ color: colors.blue, fontSize: 14, fontWeight: '800' }}>{t('Create an account')}</Text>
      </Pressable>
    </View>
  </Page>;
}
