import { useRouter } from 'expo-router';
import { useEffect, useState } from 'react';
import { Text, View } from 'react-native';
import { ApiRecord, FeatureId, useSession } from '../context/SessionContext';
import { ActionButton, Brand, Card, Heading, Notice, Page, SectionTitle, colors } from './ui';
import Constants from 'expo-constants';
import * as Updates from 'expo-updates';
import { AppHeader } from './AppHeader';

export default function ProfileScreen({ inTab = false }: { inTab?: boolean }) {
  const router = useRouter();
  const session = useSession();
  const [record, setRecord] = useState<ApiRecord | null>(null);
  const [error, setError] = useState('');
  const [updateBusy, setUpdateBusy] = useState(false);
  const [updateMessage, setUpdateMessage] = useState('');
  const profileFeature: FeatureId | null = session.audience === 'customer' ? 'customerProfile' : session.audience === 'practitioner' ? 'practitionerProfile' : null;
  useEffect(() => {
    if (!profileFeature) return;
    session.rowsFor(profileFeature).then(rows => setRecord(rows[0] ?? null)).catch(e => setError(e instanceof Error ? e.message : 'Could not load profile details.'));
  }, [profileFeature, session]);

  const checkForUpdates = async () => {
    setUpdateBusy(true); setUpdateMessage('');
    try {
      if (!Updates.isEnabled || __DEV__) {
        setUpdateMessage('Over-the-air updates are available in an installed OPES preview or store build.');
        return;
      }
      const result = await Updates.checkForUpdateAsync();
      if (!result.isAvailable) { setUpdateMessage('You are using the latest available app update.'); return; }
      await Updates.fetchUpdateAsync();
      setUpdateMessage('Update downloaded. Restarting the app…');
      await Updates.reloadAsync();
    } catch (e) {
      setUpdateMessage(e instanceof Error ? `Could not check for updates: ${e.message}` : 'Could not check for updates. Try again later.');
    } finally { setUpdateBusy(false); }
  };

  if (!session.account) return <Page><Brand /><Heading title="Not signed in" subtitle="Sign in to view your account." /><ActionButton label="Sign in" onPress={() => router.replace('/')} /></Page>;
  return <Page fixedHeader={<AppHeader />}>
    {!!session.demo && <Notice>DEMO ACCOUNT · Fictional data. Changes stay local to this sample workspace.</Notice>}
    <Heading eyebrow="Account & profile" title={session.account.name} subtitle={session.account.email} />
    <Card><Text style={{ color: colors.muted, fontSize: 12, fontWeight: '800', letterSpacing: 1 }}>ROLE AND ACCESS</Text><Text style={{ color: colors.navy, fontSize: 14, fontWeight: '700', marginTop: 8 }}>{session.account.roles.join(' · ') || 'Account'}</Text><Text style={{ color: colors.muted, fontSize: 12, lineHeight: 18, marginTop: 7 }}>Your workspace and available actions are determined from the current account roles and API permissions.</Text></Card>
    {!!error && <Notice tone="error">{error}</Notice>}
    <ActionButton label="Notifications" secondary onPress={() => router.push('/notifications' as never)} />
    {profileFeature && record && <>
      <SectionTitle title="Profile details" />
      <Card>{Object.entries(record).filter(([key]) => key !== 'id').map(([key, value]) => <View key={key} style={{ paddingVertical: 6, borderBottomWidth: 1, borderBottomColor: '#EEF2F5' }}><Text style={{ color: colors.muted, fontSize: 12, textTransform: 'uppercase' }}>{key.replaceAll('_', ' ')}</Text><Text style={{ color: colors.text, fontSize: 13, marginTop: 3 }}>{value == null || value === '' ? 'Not provided' : String(value)}</Text></View>)}</Card>
      <ActionButton label="Edit profile" onPress={() => router.push({ pathname: '/action/[type]', params: { type: profileFeature } } as never)} />
    </>}
    {session.demo && <Card><Text style={{ color: colors.navy, fontWeight: '700', fontSize: 13 }}>Sample workspace reference</Text><Text style={{ color: colors.muted, fontSize: 12, marginTop: 6 }}>{session.demo.reference}</Text><Text style={{ color: colors.text, fontSize: 12, marginTop: 6 }}>{session.demo.organization}</Text></Card>}
    <SectionTitle title="App & security" />
    <Card><Text style={{ color: colors.navy, fontSize: 14, fontWeight: '800' }}>OPES Health mobile</Text><Text style={{ color: colors.muted, fontSize: 12, marginTop: 5 }}>Version {Constants.expoConfig?.version ?? '—'}{Updates.channel ? ` · ${Updates.channel} channel` : ''}</Text><Text style={{ color: colors.muted, fontSize: 12, lineHeight: 18, marginTop: 7 }}>The app checks for compatible updates when it opens. You can also check manually here.</Text></Card>
    {!!updateMessage && <Notice>{updateMessage}</Notice>}
    <ActionButton label={updateBusy ? 'Checking for updates…' : 'Check for app updates'} secondary onPress={() => void checkForUpdates()} busy={updateBusy} />
    {!inTab && <ActionButton label="Home" secondary onPress={() => router.replace('/workspace')} />}
    <ActionButton label={session.demo ? 'Exit demo workspace' : 'Sign out'} secondary onPress={async () => { await session.clearSession(); router.replace('/'); }} />
  </Page>;
}
