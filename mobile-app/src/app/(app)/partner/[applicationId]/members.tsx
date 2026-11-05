import { useLocalSearchParams } from 'expo-router';
import { useCallback, useEffect, useState } from 'react';
import { Text, View } from 'react-native';
import { ActionButton, Card, Field, Heading, Notice, Page, SectionTitle, colors } from '../../../../components/ui';
import { AppHeader } from '../../../../components/AppHeader';
import { ApiRecord, useSession } from '../../../../context/SessionContext';
import { t } from '../../../../i18n';

export default function PartnerMembersScreen() {
  const { applicationId } = useLocalSearchParams<{ applicationId: string }>();
  const session = useSession();
  const [members, setMembers] = useState<ApiRecord[]>([]);
  const [email, setEmail] = useState('');
  const [role, setRole] = useState<'member' | 'owner'>('member');
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState('');
  const [message, setMessage] = useState('');

  const load = useCallback(async () => {
    if (!applicationId) return;
    setError('');
    try {
      const result = await session.request<{ items?: ApiRecord[] }>(`/partner/accounts/${encodeURIComponent(String(applicationId))}/members`);
      setMembers(Array.isArray(result.items) ? result.items : []);
    } catch (cause) {
      setError(cause instanceof Error ? cause.message : t('Could not load organization members.'));
    }
  }, [applicationId, session]);

  useEffect(() => { void Promise.resolve().then(load); }, [load]);

  const add = async () => {
    setBusy(true); setError(''); setMessage('');
    try {
      await session.request(`/partner/accounts/${encodeURIComponent(String(applicationId))}/members`, 'POST', {
        email: email.trim(), membership_role: role,
      });
      setEmail(''); setMessage(t('Organization member added.'));
      await load();
    } catch (cause) {
      setError(cause instanceof Error ? cause.message : t('Could not add this partner account.'));
    } finally { setBusy(false); }
  };

  const remove = async (member: ApiRecord) => {
    setBusy(true); setError(''); setMessage('');
    try {
      await session.request(`/partner/accounts/${encodeURIComponent(String(applicationId))}/members/${encodeURIComponent(String(member.user_id))}`, 'DELETE');
      setMessage(t('{name} removed from this organization.', { name: String(member.name ?? t('Member')) }));
      await load();
    } catch (cause) {
      setError(cause instanceof Error ? cause.message : t('Could not remove this member.'));
    } finally { setBusy(false); }
  };

  return <Page fixedHeader={<AppHeader />}>
    <Heading eyebrow={t('Organization access')} title={t('Manage members')} subtitle={t('Invite an existing, verified OPES partner account and choose its organization role.')} />
    {!!error && <Notice tone="error">{error}</Notice>}
    {!!message && <Notice tone="success">{message}</Notice>}
    <Card>
      <Field label={t('Partner account email')} value={email} onChangeText={setEmail} keyboardType="email-address" autoCapitalize="none" placeholder={t('name@organization.org')} />
      <Text style={{ color: colors.text, fontSize: 14, fontWeight: '700', marginBottom: 8 }}>{t('Organization role')}</Text>
      <View style={{ flexDirection: 'row', gap: 8, marginBottom: 10 }}>
        <View style={{ flex: 1 }}><ActionButton label={t('Member')} secondary={role !== 'member'} onPress={() => setRole('member')} disabled={busy} /></View>
        <View style={{ flex: 1 }}><ActionButton label={t('Owner')} secondary={role !== 'owner'} onPress={() => setRole('owner')} disabled={busy} /></View>
      </View>
      <ActionButton label={t('Add partner account')} onPress={() => void add()} busy={busy} disabled={!email.trim() || !email.includes('@')} />
      <Text style={{ color: colors.muted, fontSize: 12, lineHeight: 18 }}>{t('Only active, email-verified accounts with the OPES partner role can be added. Assign another owner before removing or changing the last owner.')}</Text>
    </Card>
    <SectionTitle title={t('Organization members · {count}', { count: members.length })} trailing={<Text onPress={() => void load()} accessibilityRole="button" style={{ color: colors.blue, fontWeight: '700', padding: 8 }}>{t('Refresh')}</Text>} />
    {members.length === 0 ? <Card><Text style={{ color: colors.muted, fontSize: 14 }}>{t('No members are linked to this organization.')}</Text></Card> : members.map(member => <Card key={String(member.id)}>
      <Text style={{ color: colors.navy, fontSize: 16, fontWeight: '700' }}>{String(member.name ?? t('Partner account'))}</Text>
      <Text style={{ color: colors.muted, fontSize: 13, marginTop: 4 }}>{String(member.email ?? '')}</Text>
      <Text style={{ color: colors.text, fontSize: 12, fontWeight: '700', marginTop: 8 }}>{String(member.membership_role ?? 'member').toUpperCase()}</Text>
      <View style={{ marginTop: 10 }}><ActionButton label={t('Remove from organization')} secondary onPress={() => void remove(member)} busy={busy} /></View>
    </Card>)}
  </Page>;
}
