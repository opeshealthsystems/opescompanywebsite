import { useRouter } from 'expo-router';
import { Pressable, Text, View } from 'react-native';
import { featureCatalog, featureIds, useSession, workspaceTitles } from '../context/SessionContext';
import { ActionButton, Brand, Card, Heading, Notice, Page, SectionTitle, colors } from './ui';
import { AppIcon, getFeatureIconName } from './icons';

export default function WorkspaceScreen() {
  const router = useRouter();
  const session = useSession();
  const audience = session.audience;
  const items = audience ? featureIds[audience].map(id => featureCatalog[id]).filter(feature => session.isAllowed(feature)) : [];
  if (!session.account) return <Page><Brand /><Heading title="Your session ended" subtitle="Sign in again to open your account." /><ActionButton label="Return to sign in" onPress={() => router.replace('/')} /></Page>;

  const demo = session.demo;
  return <Page>
    <View style={{ flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 15 }}>
      <Brand compact />
      <Pressable accessibilityRole="button" accessibilityLabel="Open my profile" onPress={() => router.push('/profile')} style={{ minHeight: 44, flexDirection: 'row', alignItems: 'center', gap: 7, paddingHorizontal: 8 }}><AppIcon name="profile" size={18} /><Text style={{ color: colors.blue, fontWeight: '700' }}>My profile</Text></Pressable>
    </View>
    {demo && <Notice>DEMO MODE · Fictional sample account. Changes stay in this app and are reset when you leave this workspace.</Notice>}
    <Heading eyebrow={demo ? 'Interactive demo' : 'Signed in'} title={`Hello, ${session.account.name.split(' ')[0]}`} subtitle={`${audience ? workspaceTitles[audience] : 'Account'} · ${session.account.email}`} />
    {demo && <Card><Text style={{ color: colors.navy, fontSize: 12, fontWeight: '800', letterSpacing: 0.6 }}>SAMPLE ACCOUNT</Text><Text style={{ color: colors.text, fontSize: 14, fontWeight: '700', marginTop: 8 }}>{demo.organization}</Text><Text style={{ color: colors.muted, fontSize: 12, marginTop: 4 }}>Reference: {demo.reference} · Role: {demo.account.roles.join(', ')}</Text></Card>}

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
