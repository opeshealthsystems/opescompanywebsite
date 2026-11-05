import { useRouter } from 'expo-router';
import { useEffect, useState } from 'react';
import { Pressable, Text, View } from 'react-native';
import { ApiRecord, FeatureId, useSession } from '../context/SessionContext';
import { ActionButton, Brand, confirmAction, humanize, Card, Heading, Notice, Page, SectionTitle, colors } from './ui';
import Constants from 'expo-constants';
import * as Updates from 'expo-updates';
import { AppHeader } from './AppHeader';
import { getLang, Lang, setLang, t } from '../i18n';

// Internal identifiers and bookkeeping fields are not useful to the account holder.
const hiddenProfileFields = ['id', 'user_id', 'customer_id', 'practitioner_id', 'created_at', 'updated_at', 'deleted_at', 'demo', 'email_verified_at'];

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
    session.rowsFor(profileFeature).then(rows => setRecord(rows[0] ?? null)).catch(e => setError(e instanceof Error ? e.message : t('Could not load profile details.')));
  }, [profileFeature, session]);

  const checkForUpdates = async () => {
    setUpdateBusy(true); setUpdateMessage('');
    try {
      if (!Updates.isEnabled || __DEV__) {
        setUpdateMessage(t('Over-the-air updates are available in an installed OPES preview or store build.'));
        return;
      }
      const result = await Updates.checkForUpdateAsync();
      if (!result.isAvailable) { setUpdateMessage(t('You are using the latest available app update.')); return; }
      await Updates.fetchUpdateAsync();
      setUpdateMessage(t('Update downloaded. Restarting the app…'));
      await Updates.reloadAsync();
    } catch (e) {
      setUpdateMessage(e instanceof Error ? t('Could not check for updates: {reason}', { reason: e.message }) : t('Could not check for updates. Try again later.'));
    } finally { setUpdateBusy(false); }
  };

  if (!session.account) return <Page><Brand /><Heading title={t('Not signed in')} subtitle={t('Sign in to view your account.')} /><ActionButton label={t('Sign in')} onPress={() => router.replace('/')} /></Page>;
  return <Page fixedHeader={<AppHeader />}>
    {!!session.demo && <Notice>{t('DEMO ACCOUNT · Fictional data. Changes stay local to this sample workspace.')}</Notice>}
    <Heading eyebrow={t('Account & profile')} title={session.account.name} subtitle={session.account.email} />
    <Card><Text style={{ color: colors.muted, fontSize: 12, fontWeight: '800', letterSpacing: 1 }}>{t('ROLE AND ACCESS')}</Text><Text style={{ color: colors.navy, fontSize: 14, fontWeight: '700', marginTop: 8 }}>{session.account.roles.map(humanize).join(' · ') || t('Account')}</Text><Text style={{ color: colors.muted, fontSize: 12, lineHeight: 18, marginTop: 7 }}>{t('Your workspace and available actions are determined from the current account roles and API permissions.')}</Text></Card>
    {!!error && <Notice tone="error">{error}</Notice>}
    <ActionButton label={t('Notifications')} secondary onPress={() => router.push('/notifications' as never)} />
    {profileFeature && record && <>
      <SectionTitle title={t('Profile details')} />
      <Card>{Object.entries(record).filter(([key, value]) => !hiddenProfileFields.includes(key) && (value === null || typeof value !== 'object')).map(([key, value]) => <View key={key} style={{ paddingVertical: 6, borderBottomWidth: 1, borderBottomColor: colors.divider }}><Text style={{ color: colors.muted, fontSize: 12, fontWeight: '700' }}>{humanize(key)}</Text><Text style={{ color: colors.text, fontSize: 13, marginTop: 3 }}>{value == null || value === '' ? t('Not provided') : String(value)}</Text></View>)}</Card>
      <ActionButton label={t('Edit profile')} onPress={() => router.push({ pathname: '/action/[type]', params: { type: profileFeature } } as never)} />
    </>}
    {session.demo && <Card><Text style={{ color: colors.navy, fontWeight: '700', fontSize: 13 }}>{t('Sample workspace reference')}</Text><Text style={{ color: colors.muted, fontSize: 12, marginTop: 6 }}>{session.demo.reference}</Text><Text style={{ color: colors.text, fontSize: 12, marginTop: 6 }}>{session.demo.organization}</Text></Card>}
    <SectionTitle title={t('Language')} />
    <Card>
      <View accessibilityRole="radiogroup" style={{ flexDirection: 'row', gap: 8 }}>
        {([['en', 'English'], ['fr', 'Français']] as [Lang, string][]).map(([code, label]) => {
          const selected = getLang() === code;
          return <Pressable key={code} accessibilityRole="radio" accessibilityLabel={label} accessibilityState={{ checked: selected }} onPress={() => setLang(code)} style={{ flex: 1, minHeight: 44, alignItems: 'center', justifyContent: 'center', borderRadius: 7, borderWidth: 1, borderColor: selected ? colors.navy : colors.borderStrong, backgroundColor: selected ? colors.navy : colors.white }}>
            <Text style={{ color: selected ? colors.white : colors.navy, fontSize: 14, fontWeight: '700' }}>{t(label)}</Text>
          </Pressable>;
        })}
      </View>
      <Text style={{ color: colors.muted, fontSize: 12, lineHeight: 18, marginTop: 8 }}>{t('The app follows your phone’s language. Choose one here to override it.')}</Text>
    </Card>
    <SectionTitle title={t('App & security')} />
    <Card><Text style={{ color: colors.navy, fontSize: 14, fontWeight: '800' }}>{t('OPES Health mobile')}</Text><Text style={{ color: colors.muted, fontSize: 12, marginTop: 5 }}>{t('Version {version}', { version: Constants.expoConfig?.version ?? '—' })}{Updates.channel ? t(' · {channel} channel', { channel: Updates.channel }) : ''}</Text><Text style={{ color: colors.muted, fontSize: 12, lineHeight: 18, marginTop: 7 }}>{t('The app checks for compatible updates when it opens. You can also check manually here.')}</Text></Card>
    {!!updateMessage && <Notice>{updateMessage}</Notice>}
    <ActionButton label={updateBusy ? t('Checking for updates…') : t('Check for app updates')} secondary onPress={() => void checkForUpdates()} busy={updateBusy} />
    {!inTab && <ActionButton label={t('Home')} secondary onPress={() => router.replace('/workspace')} />}
    <ActionButton label={session.demo ? t('Exit demo workspace') : t('Sign out')} secondary onPress={() => confirmAction(session.demo ? t('Exit demo workspace?') : t('Sign out?'), session.demo ? t('Sample changes will be reset.') : t('You will need to sign in again to open your workspace.'), session.demo ? t('Exit') : t('Sign out'), () => { void session.clearSession().then(() => router.replace('/')); }, true)} />
  </Page>;
}
