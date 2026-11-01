import { useRouter } from 'expo-router';
import { useEffect, useState } from 'react';
import { Pressable, Text, View } from 'react-native';
import { ApiRecord, FeatureId, featureCatalog, featureIds, useSession, workspaceTitles } from '../context/SessionContext';
import { ActionButton, Brand, Card, Heading, Notice, Page, SectionTitle, colors } from './ui';
import { AppIcon, getFeatureIconName } from './icons';

const overviewByAudience: Record<string, { id: FeatureId; label: string }[]> = {
  staff: [{ id: 'quotes', label: 'Quotes' }, { id: 'contacts', label: 'Leads' }, { id: 'customers', label: 'Customers' }],
  customer: [{ id: 'serviceRequests', label: 'Services' }, { id: 'tickets', label: 'Support' }, { id: 'invoices', label: 'Invoices' }],
  practitioner: [{ id: 'courses', label: 'Courses' }, { id: 'certificates', label: 'Certificates' }],
  tester: [{ id: 'assignments', label: 'Assignments' }, { id: 'bugReports', label: 'Bug reports' }],
  partner: [{ id: 'partnerships', label: 'Organizations' }],
};

function rowCount(value: unknown): number {
  if (Array.isArray(value)) return value.length;
  if (!value || typeof value !== 'object') return 0;
  const record = value as Record<string, unknown>;
  for (const key of ['items','data','results','records','quotes','profiles','customerProfiles','leads','applications','partnerApplications','deals','serviceRequests','tickets','reports','assignments','invoices','courses','certificates','notifications']) {
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
  if (!session.account) return <Page><Brand /><Heading title="Your session ended" subtitle="Sign in again to open your account." /><ActionButton label="Return to sign in" onPress={() => router.replace('/')} /></Page>;

  const demo = session.demo;
  return <Page>
    <View style={{ flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 15 }}>
      <Brand compact />
      <View style={{ flexDirection: 'row', alignItems: 'center', gap: 2 }}>
        {session.isAllowed(featureCatalog.notifications) && <Pressable accessibilityRole="button" accessibilityLabel="Open notifications" onPress={() => router.push('/notifications' as never)} style={{ width: 44, height: 44, alignItems: 'center', justifyContent: 'center' }}><AppIcon name="bell" size={19} /></Pressable>}
        <Pressable accessibilityRole="button" accessibilityLabel="Open my profile" onPress={() => router.push('/profile')} style={{ minHeight: 44, flexDirection: 'row', alignItems: 'center', gap: 7, paddingHorizontal: 8 }}><AppIcon name="profile" size={18} /><Text style={{ color: colors.blue, fontWeight: '700' }}>Account</Text></Pressable>
      </View>
    </View>
    {demo && <Notice>DEMO MODE · Fictional sample account. Changes stay in this app and are reset when you leave this workspace.</Notice>}
    <Card style={{ backgroundColor: colors.navy, borderColor: colors.navy, padding: 18, marginBottom: 16 }}>
      <Text style={{ color: '#C8DAE9', fontSize: 11, fontWeight: '800', letterSpacing: 1.1 }}>{demo ? 'INTERACTIVE DEMO' : 'YOUR OPES WORKSPACE'}</Text>
      <Text style={{ color: colors.white, fontSize: 25, lineHeight: 31, fontWeight: '800', marginTop: 7 }}>Hello, {session.account.name.split(' ')[0]}</Text>
      <Text style={{ color: '#E0EAF2', fontSize: 13, lineHeight: 19, marginTop: 5 }}>{audience ? workspaceTitles[audience] : 'Account'} · {session.account.email}</Text>
    </Card>
    {demo && <Card><Text style={{ color: colors.navy, fontSize: 12, fontWeight: '800', letterSpacing: 0.6 }}>SAMPLE ACCOUNT</Text><Text style={{ color: colors.text, fontSize: 14, fontWeight: '700', marginTop: 8 }}>{demo.organization}</Text><Text style={{ color: colors.muted, fontSize: 12, marginTop: 4 }}>Reference: {demo.reference} · Role: {demo.account.roles.join(', ')}</Text></Card>}

    {!!audience && <>
      <SectionTitle title="At a glance" trailing={overviewLoading ? <Text style={{ color: colors.muted, fontSize: 12 }}>Updating…</Text> : undefined} />
      <View style={{ flexDirection: 'row', flexWrap: 'wrap', gap: 8 }}>
        {(overviewByAudience[audience] ?? []).filter(item => session.isAllowed(featureCatalog[item.id])).map(item => <Pressable key={item.id} accessibilityRole="button" accessibilityLabel={`Open ${item.label}`} onPress={() => router.push({ pathname: '/feature/[id]', params: { id: item.id } } as never)} style={{ flexGrow: 1, flexBasis: 92, minWidth: 88, backgroundColor: colors.white, borderWidth: 1, borderColor: colors.border, borderRadius: 8, padding: 12 }}>
          <Text numberOfLines={2} style={{ color: colors.muted, fontSize: 12, lineHeight: 16, fontWeight: '700' }}>{item.label.toUpperCase()}</Text>
          <Text style={{ color: colors.navy, fontSize: 22, lineHeight: 27, fontWeight: '800', marginTop: 5 }}>{overview[item.id] ?? (overviewLoading ? '—' : '0')}</Text>
        </Pressable>)}
      </View>
    </>}

    <SectionTitle title="Quick actions" />
    {audience === 'staff' && session.isAllowed(featureCatalog.quotes, true) && <ActionButton label="Create a quote" onPress={() => router.push({ pathname: '/action/[type]', params: { type: 'quote' } })} />}
    {audience === 'customer' && session.isAllowed(featureCatalog.serviceRequests, true) && <ActionButton label="Request a service" onPress={() => router.push({ pathname: '/action/[type]', params: { type: 'serviceRequest' } })} />}
    {audience === 'customer' && session.isAllowed(featureCatalog.tickets, true) && <ActionButton label="Open a support ticket" onPress={() => router.push({ pathname: '/action/[type]', params: { type: 'ticket' } })} secondary />}
    {audience === 'tester' && session.isAllowed(featureCatalog.assignments, true) && <ActionButton label="Report a bug" onPress={() => router.push({ pathname: '/action/[type]', params: { type: 'bugReport' } })} />}
    {audience === 'partner' && <ActionButton label="Apply to become a partner" onPress={() => router.push({ pathname: '/action/[type]', params: { type: 'partnerApplication' } })} />}

    <SectionTitle title="Your workspaces" />
    {items.length === 0 ? <Notice>Your account has no mobile workspace scopes. Contact your administrator to review role access.</Notice> : items.map(feature => <Pressable key={feature.id} accessibilityRole="button" accessibilityLabel={`Open ${feature.title}`} onPress={() => router.push({ pathname: '/feature/[id]', params: { id: feature.id } })}>
      <Card style={{ flexDirection: 'row', alignItems: 'center', gap: 12 }}>
        <View style={{ width: 42, height: 42, borderRadius: 8, backgroundColor: '#E8F1F8', alignItems: 'center', justifyContent: 'center' }}><AppIcon name={getFeatureIconName(feature.id)} size={21} /></View>
        <View style={{ flex: 1, minWidth: 0 }}><Text style={{ color: colors.navy, fontSize: 15, fontWeight: '700', flexShrink: 1 }}>{feature.title}</Text><Text style={{ color: colors.muted, fontSize: 13, lineHeight: 18, marginTop: 3, flexShrink: 1 }}>{feature.description}</Text></View>
        <AppIcon name="chevronRight" size={20} color={colors.muted} />
      </Card>
    </Pressable>)}
    <ActionButton label={demo ? 'Exit demo workspace' : 'Sign out'} secondary onPress={async () => { await session.clearSession(); router.replace('/'); }} />
    <Text style={{ color: colors.muted, fontSize: 12, textAlign: 'center', marginTop: 4 }}>Account access and write actions follow your live API permissions.</Text>
  </Page>;
}
