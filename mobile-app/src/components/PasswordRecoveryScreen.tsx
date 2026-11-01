import { useState } from 'react';
import { Pressable, Text, View } from 'react-native';
import { useRouter } from 'expo-router';
import { useSession } from '../context/SessionContext';
import { ActionButton, Brand, Field, Heading, Notice, Page, colors } from './ui';

export default function PasswordRecoveryScreen() {
  const router = useRouter();
  const session = useSession();
  const [email, setEmail] = useState('');
  const [token, setToken] = useState('');
  const [password, setPassword] = useState('');
  const [confirmation, setConfirmation] = useState('');
  const [mode, setMode] = useState<'request' | 'reset'>('request');
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState('');
  const [message, setMessage] = useState('');

  const submit = async () => {
    setBusy(true); setError(''); setMessage('');
    try {
      if (mode === 'request') {
        if (!email.includes('@')) throw new Error('Enter a valid email address.');
        await session.request('/public/password/forgot', 'POST', { email: email.trim() });
        setMessage('If this email is associated with an account, a password reset link has been sent. Follow the link from your email, then return here to sign in.');
      } else {
        if (!email.includes('@') || !token.trim()) throw new Error('Enter the account email and reset token from your email.');
        if (password.length < 8) throw new Error('Choose a password with at least 8 characters.');
        if (password !== confirmation) throw new Error('The passwords do not match.');
        await session.request('/public/password/reset', 'POST', { email: email.trim(), token: token.trim(), password, password_confirmation: confirmation });
        setMessage('Your password has been reset. Sign in with the new password.');
        setMode('request'); setPassword(''); setConfirmation(''); setToken('');
      }
    } catch (e) { setError(e instanceof Error ? e.message : 'The password request could not be completed.'); }
    finally { setBusy(false); }
  };

  return <Page>
    <Brand />
    <Heading eyebrow="Account security" title={mode === 'request' ? 'Reset your password' : 'Choose a new password'} subtitle={mode === 'request' ? 'Request a reset link for your OPES account.' : 'Enter the email and reset token provided by OPES Health.'} />
    <Field label="Email address" value={email} onChangeText={setEmail} keyboardType="email-address" autoCapitalize="none" />
    {mode === 'reset' && <>
      <Field label="Reset token" value={token} onChangeText={setToken} autoCapitalize="none" />
      <Field label="New password (minimum 8 characters)" value={password} onChangeText={setPassword} secureTextEntry autoCapitalize="none" />
      <Field label="Confirm new password" value={confirmation} onChangeText={setConfirmation} secureTextEntry autoCapitalize="none" />
    </>}
    {!!error && <Notice tone="error">{error}</Notice>}
    {!!message && <Notice tone="success">{message}</Notice>}
    <ActionButton label={busy ? 'Please wait…' : mode === 'request' ? 'Email me a reset link' : 'Reset password'} onPress={() => void submit()} busy={busy} />
    <Pressable accessibilityRole="button" onPress={() => { setMode(mode === 'request' ? 'reset' : 'request'); setError(''); setMessage(''); }} style={{ minHeight: 48, justifyContent: 'center', alignItems: 'center', marginBottom: 12 }}>
      <Text style={{ color: colors.blue, fontSize: 14, fontWeight: '700' }}>{mode === 'request' ? 'I already have a reset token' : 'Request another reset link'}</Text>
    </Pressable>
    <View style={{ marginTop: 10 }}><ActionButton label="Back to sign in" secondary onPress={() => router.replace('/')} /></View>
  </Page>;
}
