import { useRouter } from 'expo-router';
import { useEffect, useState } from 'react';
import { Pressable, Text, View } from 'react-native';
import { ApiRecord, FeatureId, featureCatalog, featureIds, useSession, workspaceTitles } from '../context/SessionContext';
import { ActionButton, Brand, Card, Heading, Notice, Page, SectionTitle, colors, radius, space } from './ui';
import { AppHeader } from './AppHeader';
import { AppIcon, AppIconName, getFeatureIconName } from './icons';
import { dateLocale, t } from '../i18n';

const overviewByAudience: Record<string, { id: FeatureId; label: string }[]> = {
  staff: [{ id: 'quotes', label: 'Quotes' }, { id: 'contacts', label: 'Leads' }, { id: 'customers', label: 'Customers' }],
  customer: [{ id: 'serviceRequests', label: 'Services' }, { id: 'tickets', label: 'Support' }, { id: 'invoices', label: 'Invoices' }],
  practitioner: [{ id: 'courses', label: 'Courses' }, { id: 'certificates', label: 'Certificates' }],
  tester: [{ id: 'assignments', label: 'Assignments' }, { id: 'bugReports', label: 'Bug reports' }],
  partner: [{ id: 'partnerAccounts', label: 'My organizations' }],
};

function rowCount(value: unknown): number {
  if (Array.isArray(value)) return value.length;
  if (!value || typeof value !== 'object') return 0;
  const record = value as Record<string, unknown>;
  for (const key of ['items','data','results','records','accounts','quotes','profiles','customerProfiles','leads','applications','partnerApplications','deals','serviceRequests','tickets','reports','assignments','invoices','courses','certificates','notifications']) {
    if (Array.isArray(record[key])) return (record[key] as ApiRecord[]).length;
  }
  return 0;
}

export default function WorkspaceScreen() {
  const router = useRouter();
  const session = useSession();
  const audience = session.audience;
  const items = audience ? featureIds[audience].map(id => featureCatalog[id]).filter(feature => session.isAllowed(feature)) : [];
  const [overviewSnapshot, setOverviewSnapshot] = useState<{ audience: string; accountId: string; values: Record<string, number | null> } | null>(null);
  const overviewMatches = !!audience && !!session.account && overviewSnapshot?.audience === audience && overviewSnapshot.accountId === session.account.id;
  const overview = overviewMatches ? overviewSnapshot.values : {};
  const overviewLoading = !!session.account && !overviewMatches;
  useEffect(() => {
    if (!audience || !session.account) return;
    let active = true;
    const accountId = session.account.id;
    const features = (overviewByAudience[audience] ?? []).filter(item => session.isAllowed(featureCatalog[item.id]));
    void Promise.allSettled(features.map(async item => [item.id, rowCount(await session.rowsFor(item.id))] as const)).then(results => {
      if (!active) return;
      const values: Record<string, number | null> = {};
      results.forEach((result, index) => { values[features[index].id] = result.status === 'fulfilled' ? result.value[1] : null; });
      setOverviewSnapshot({ audience, accountId, values });
    });
    return () => { active = false; };
  }, [audience, session]);
  if (!session.account) return <Page><Brand /><Heading title={t('Your session ended')} subtitle={t('Sign in again to open your account.')} /><ActionButton label={t('Return to sign in')} onPress={() => router.replace('/')} /></Page>;

  const demo = session.demo;
  const today = new Date().toLocaleDateString(dateLocale(), { weekday: 'long', day: 'numeric', month: 'long' });
  const quickActions: { key: string; label: string; icon: AppIconName; onPress: () => void }[] = [
    ...(audience === 'staff' && session.isAllowed(featureCatalog.quotes, true) ? [{ key: 'quote', label: t('Create a quote'), icon: 'file' as AppIconName, onPress: () => router.push({ pathname: '/action/[type]', params: { type: 'quote' } }) }] : []),
    ...(audience === 'staff' && session.isAllowed(featureCatalog.contacts, true) ? [{ key: 'contact', label: t('Add a contact'), icon: 'users' as AppIconName, onPress: () => router.push({ pathname: '/action/[type]', params: { type: 'contact' } }) }] : []),
    ...(audience === 'customer' && session.isAllowed(featureCatalog.serviceRequests, true) ? [{ key: 'service', label: t('Request a service'), icon: 'briefcase' as AppIconName, onPress: () => router.push({ pathname: '/action/[type]', params: { type: 'serviceRequest' } }) }] : []),
    ...(audience === 'customer' && session.isAllowed(featureCatalog.tickets, true) ? [{ key: 'ticket', label: t('Open a support ticket'), icon: 'message' as AppIconName, onPress: () => router.push({ pathname: '/action/[type]', params: { type: 'ticket' } }) }] : []),
    ...(audience === 'practitioner' && session.isAllowed(featureCatalog.courses) ? [{ key: 'learn', label: t('Continue learning'), icon: 'book' as AppIconName, onPress: () => router.push('/work' as never) }] : []),
    ...(audience === 'tester' && session.isAllowed(featureCatalog.assignments, true) ? [{ key: 'bug', label: t('Report a bug on an assignment'), icon: 'flask' as AppIconName, onPress: () => router.push({ pathname: '/feature/[id]', params: { id: 'assignments' } }) }] : []),
    ...(audience === 'partner' && session.isAllowed(featureCatalog.partnerships) ? [{ key: 'directory', label: t('Browse partner directory'), icon: 'handshake' as AppIconName, onPress: () => router.push({ pathname: '/feature/[id]', params: { id: 'partnerships' } }) }] : []),
  ];
  return <Page fixedHeader={<AppHeader />}>
    {demo && <Notice>{t('Demo mode · {organization} ({reference}). Fictional data; changes stay on this device and reset when you leave.', { organization: demo.organization, reference: demo.reference })}</Notice>}
    <View style={{ marginTop: space.sm, marginBottom: space.lg }}>
      <Text style={{ color: colors.muted, fontSize: 13, fontWeight: '600' }}>{today}</Text>
      <Text style={{ color: colors.navy, fontSize: 28, lineHeight: 34, fontWeight: '800', marginTop: 2 }}>{t('Hello, {name}', { name: session.account.name.split(' ')[0] })}</Text>
      <View style={{ flexDirection: 'row', flexWrap: 'wrap', alignItems: 'center', gap: space.sm, marginTop: space.sm }}>
        <View style={{ backgroundColor: colors.tile, borderRadius: radius.pill, paddingHorizontal: 10, paddingVertical: 4 }}><Text style={{ color: colors.navy, fontSize: 12, fontWeight: '800' }}>{audience ? t(workspaceTitles[audience]) : t('Account')}</Text></View>
        <Text numberOfLines={1} style={{ color: colors.muted, fontSize: 13, flexShrink: 1 }}>{session.account.email}</Text>
      </View>
    </View>

    {!!audience && (overviewByAudience[audience] ?? []).some(item => session.isAllowed(featureCatalog[item.id])) && <>
      <SectionTitle title={t('At a glance')} trailing={overviewLoading ? <Text style={{ color: colors.muted, fontSize: 12 }}>{t('Updating…')}</Text> : undefined} />
      <View style={{ flexDirection: 'row', flexWrap: 'wrap', gap: space.sm }}>
        {(overviewByAudience[audience] ?? []).filter(item => session.isAllowed(featureCatalog[item.id])).map(item => <Pressable key={item.id} accessibilityRole="button" accessibilityLabel={t('{label}: {count}. Open', { label: t(item.label), count: overview[item.id] ?? '—' })} onPress={() => router.push({ pathname: '/feature/[id]', params: { id: item.id } } as never)} style={{ flexGrow: 1, flexBasis: 96, minWidth: 92, backgroundColor: colors.white, borderWidth: 1, borderColor: colors.border, borderLeftWidth: 3, borderLeftColor: colors.navy, borderRadius: radius.md, padding: space.md }}>
          <AppIcon name={getFeatureIconName(item.id)} size={18} color={colors.blue} />
          <Text style={{ color: colors.navy, fontSize: 24, lineHeight: 29, fontWeight: '800', marginTop: space.sm }}>{overview[item.id] ?? (overviewLoading ? '—' : '0')}</Text>
          <Text numberOfLines={1} style={{ color: colors.muted, fontSize: 13, fontWeight: '600', marginTop: 2 }}>{t(item.label)}</Text>
        </Pressable>)}
      </View>
    </>}

    {quickActions.length > 0 && <>
      <SectionTitle title={t('Quick actions')} />
      <View style={{ gap: space.sm }}>
        {quickActions.map((item, index) => <Pressable key={item.key} accessibilityRole="button" onPress={item.onPress} style={{ flexDirection: 'row', alignItems: 'center', gap: space.md, minHeight: 52, paddingHorizontal: space.md, borderRadius: radius.md, backgroundColor: index === 0 ? colors.navy : colors.white, borderWidth: 1, borderColor: index === 0 ? colors.navy : colors.borderStrong }}>
          <AppIcon name={item.icon} size={20} color={index === 0 ? colors.white : colors.navy} />
          <Text style={{ flex: 1, color: index === 0 ? colors.white : colors.navy, fontSize: 15, fontWeight: '700' }}>{item.label}</Text>
          <AppIcon name="chevronRight" size={18} color={index === 0 ? colors.onNavyMuted : colors.muted} />
        </Pressable>)}
      </View>
    </>}

    <SectionTitle title={t('Your workspaces')} />
    {!audience && <Card><Text style={{ color: colors.navy, fontSize: 15, fontWeight: '800' }}>{t('No mobile workspace for this account yet')}</Text><Text style={{ color: colors.muted, fontSize: 13, lineHeight: 19, marginTop: 6 }}>Your account roles ({session.account.roles.join(', ') || 'none'}) do not match a staff, customer, practitioner, tester or partner workspace in this app. Ask your OPES administrator to review your role, or use the OPES website.</Text></Card>}
    {!audience ? null : items.length === 0 ? <Notice>{t('Your account has no mobile workspace scopes. Contact your administrator to review role access.')}</Notice> : items.map(feature => <Pressable key={feature.id} accessibilityRole="button" accessibilityLabel={t('Open {name}', { name: t(feature.title) })} onPress={() => router.push({ pathname: '/feature/[id]', params: { id: feature.id } })}>
      <Card style={{ flexDirection: 'row', alignItems: 'center', gap: 12 }}>
        <View style={{ width: 42, height: 42, borderRadius: radius.md, backgroundColor: colors.tile, alignItems: 'center', justifyContent: 'center' }}><AppIcon name={getFeatureIconName(feature.id)} size={21} /></View>
        <View style={{ flex: 1, minWidth: 0 }}><Text style={{ color: colors.navy, fontSize: 15, fontWeight: '700', flexShrink: 1 }}>{t(feature.title)}</Text><Text style={{ color: colors.muted, fontSize: 13, lineHeight: 18, marginTop: 3, flexShrink: 1 }}>{t(feature.description)}</Text></View>
        <AppIcon name="chevronRight" size={20} color={colors.muted} />
      </Card>
    </Pressable>)}
    <Text style={{ color: colors.muted, fontSize: 12, textAlign: 'center', marginTop: 4 }}>{t('Account access and write actions follow your live API permissions.')}</Text>
  </Page>;
}
