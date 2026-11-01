import { useLocalSearchParams, useRouter } from 'expo-router';
import { useCallback, useEffect, useState } from 'react';
import { ActivityIndicator, Pressable, Text, View } from 'react-native';
import { ApiRecord, featureCatalog, FeatureId, useSession } from '../context/SessionContext';
import { ActionButton, Brand, Card, Heading, Notice, Page, SectionTitle, colors } from './ui';

function display(value: unknown): string {
  if (value === null || value === undefined || value === '') return '—';
  if (typeof value === 'object') return Array.isArray(value) ? `${value.length} items` : JSON.stringify(value);
  return String(value);
}

export default function FeatureScreen() {
  const router = useRouter();
  const { id } = useLocalSearchParams<{ id: string }>();
  const feature = featureCatalog[id as FeatureId];
  const session = useSession();
  const [rows, setRows] = useState<ApiRecord[]>([]);
  const [busy, setBusy] = useState(true);
  const [acting, setActing] = useState<string | null>(null);
  const [error, setError] = useState('');
  const [message, setMessage] = useState('');
  const [expanded, setExpanded] = useState<string | null>(null);
  const mayWrite = feature ? session.isAllowed(feature, true) : false;

  const load = useCallback(async () => {
    if (!feature) { setBusy(false); setError('This app section is not available.'); return; }
    setBusy(true); setError('');
    try { setRows(await session.rowsFor(feature.id)); }
    catch (e) { setError(e instanceof Error ? e.message : 'Could not load these records.'); }
    finally { setBusy(false); }
  }, [feature, session]);

  useEffect(() => { void Promise.resolve().then(load); }, [load]);

  const action = async (row: ApiRecord, kind: 'complete' | 'start' | 'enroll' | 'sent' | 'contactStatus' | 'partnerReview' | 'partnerApprove' | 'partnerReject') => {
    if (!feature || !mayWrite) return;
    const rowId = String(row.id ?? row.slug ?? '');
    setActing(rowId); setError(''); setMessage('');
    try {
      if (session.demo) {
        const change = kind === 'contactStatus' ? String(row.status) === 'new' ? 'contacted' : String(row.status) === 'contacted' ? 'qualified' : 'closed' : kind === 'partnerReview' ? 'reviewing' : kind === 'partnerApprove' ? 'approved' : kind === 'partnerReject' ? 'rejected' : kind === 'complete' ? 'completed' : kind === 'start' ? 'in_progress' : kind === 'sent' ? 'sent' : 'enrolled';
        if (kind === 'enroll') session.mutateDemo(feature.id, 'enroll', { status: change }, rowId);
        else session.mutateDemo(feature.id, 'update-status', { status: change }, rowId);
        setMessage(`Demo updated: ${feature.title.toLowerCase()} now ${change}.`);
      } else if (feature.id === 'assignments') {
        await session.request(`/testing/assignments/${encodeURIComponent(rowId)}/status`, 'PATCH', { status: kind === 'complete' ? 'completed' : 'in_progress' });
        setMessage('Assignment status updated.');
      } else if (feature.id === 'contacts' && kind === 'contactStatus') {
        const current = String(row.status ?? 'new'); const next = current === 'new' ? 'contacted' : current === 'contacted' ? 'qualified' : 'closed';
        await session.request(`/admin/enquiries/leads/${encodeURIComponent(rowId)}`, 'PUT', {
          name: String(row.name ?? ''), email: String(row.email ?? ''), phone: row.phone ?? null, facility_type: row.facility_type ?? null,
          products: row.products ?? null, message: row.message ?? null, source: row.source ?? 'contact', product_slug: row.product_slug ?? null, locale: row.locale ?? 'en', status: next,
        });
        setMessage(`Contact marked ${next}.`);
      } else if (feature.id === 'partnerApps' && ['partnerReview','partnerApprove','partnerReject'].includes(kind)) {
        const next = kind === 'partnerReview' ? 'reviewing' : kind === 'partnerApprove' ? 'approved' : 'rejected';
        await session.request(`/crm/partner-applications/${encodeURIComponent(rowId)}`, 'PATCH', { status: next });
        setMessage(`Partner application marked ${next}.`);
      } else if (feature.id === 'courses') {
        const slug = String(row.slug ?? rowId);
        await session.request(`/practitioner/courses/${encodeURIComponent(slug)}/enroll`, 'POST', {});
        setMessage('Course enrollment saved.');
      } else if (feature.id === 'quotes' && kind === 'sent') {
        const quoteId = encodeURIComponent(String(row.id ?? row.quote_id));
        await session.request(`/finance/quotes/${quoteId}/mark-sent`, 'POST', {});
        setMessage('Quote marked as sent.');
      }
      await load();
    } catch (e) { setError(e instanceof Error ? e.message : 'The action could not be completed.'); }
    finally { setActing(null); }
  };

  const startCreate = () => {
    if (!feature?.createAction || !mayWrite) return;
    router.push(`/action/${feature.createAction}` as never);
  };
  const title = feature?.title ?? 'Workspace';

  return <Page>
    <View style={{ flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' }}><Brand compact /><Pressable onPress={() => router.push('/profile')}><Text style={{ color: colors.blue, fontWeight: '700', padding: 8 }}>Profile</Text></Pressable></View>
    <Pressable onPress={() => router.back()}><Text style={{ color: colors.blue, fontSize: 13, fontWeight: '700', marginTop: 3 }}>‹ Back to workspace</Text></Pressable>
    {!!session.demo && <Notice>DEMO MODE · All records and actions on this screen are fictional and local to this app.</Notice>}
    <Heading eyebrow="Workspace" title={title} subtitle={feature?.description ?? 'Manage account information and work items.'} />
    {feature?.createAction && mayWrite && <ActionButton label={feature.id === 'partnerApplication' ? 'Start an application' : feature.id === 'contacts' ? 'Add a contact' : `Create ${feature.title.toLowerCase().replace(/s$/, '')}`} onPress={startCreate} />}
    {!!error && <Notice tone="error">{error}</Notice>}{!!message && <Notice tone="success">{message}</Notice>}
    <SectionTitle title={feature?.id === 'partnerApplication' ? 'Application information' : `Records · ${rows.length}`} trailing={<Pressable onPress={() => void load()}><Text style={{ color: colors.blue, fontWeight: '700' }}>Refresh</Text></Pressable>} />
    {busy ? <ActivityIndicator color={colors.blue} style={{ marginVertical: 24 }} /> : null}
    {!busy && rows.length === 0 && !error && <Card><Text style={{ color: colors.text, fontSize: 14, fontWeight: '700' }}>Nothing here yet</Text><Text style={{ color: colors.muted, fontSize: 12, lineHeight: 18, marginTop: 5 }}>Use the action above to create your first item, or pull down Refresh to check again.</Text></Card>}
    {rows.map((row, index) => {
      const key = String(row.id ?? row.slug ?? index); const isOpen = expanded === key;
      const titleValue = String(row.title ?? row.subject ?? row.name ?? row.organization_name ?? row.reference ?? row.product_name ?? `Record ${index + 1}`);
      const status = String(row.status ?? row.state ?? '');
      return <Card key={key}>
        <Pressable accessibilityRole="button" onPress={() => setExpanded(isOpen ? null : key)}>
          <View style={{ flexDirection: 'row', justifyContent: 'space-between', gap: 10 }}><Text style={{ color: colors.navy, fontSize: 14, fontWeight: '700', flex: 1 }}>{titleValue}</Text>{!!status && <Text style={{ color: colors.blue, fontSize: 10, fontWeight: '800', textTransform: 'uppercase' }}>{status.replaceAll('_', ' ')}</Text>}</View>
          <Text style={{ color: colors.muted, fontSize: 12, lineHeight: 18, marginTop: 6 }}>{[row.reference, row.description, row.organization_name, row.product, row.due_date].filter(Boolean).map(display).join(' · ') || feature?.description}</Text>
          <Text style={{ color: colors.blue, fontSize: 11, fontWeight: '700', marginTop: 8 }}>{isOpen ? 'Hide details' : 'View details'}</Text>
        </Pressable>
        {isOpen && <View style={{ borderTopWidth: 1, borderTopColor: colors.border, marginTop: 12, paddingTop: 10 }}>
          {Object.entries(row).filter(([field]) => !['id','items','replies'].includes(field)).map(([field, value]) => <View key={field} style={{ flexDirection: 'row', paddingVertical: 4, gap: 8 }}><Text style={{ color: colors.muted, fontSize: 11, flex: 0.8 }}>{field.replaceAll('_', ' ')}</Text><Text selectable style={{ color: colors.text, fontSize: 12, flex: 1.2 }}>{display(value)}</Text></View>)}
          {Array.isArray(row.items) && <View style={{ marginTop: 6 }}>{(row.items as ApiRecord[]).map((item, i) => <Text key={i} style={{ color: colors.text, fontSize: 12, marginTop: 4 }}>{String(item.product_name ?? item.description ?? 'Item')} · {display(item.quantity)} × {display(item.unit_price)}</Text>)}</View>}
          {Array.isArray(row.replies) && <View style={{ marginTop: 8 }}>{(row.replies as ApiRecord[]).map((reply, i) => <Text key={i} style={{ color: colors.muted, fontSize: 12, marginTop: 4 }}>{String(reply.author ?? 'Reply')}: {String(reply.body ?? '')}</Text>)}</View>}
        </View>}
        {!!mayWrite && feature?.id === 'assignments' && status !== 'completed' && <View style={{ flexDirection: 'row', gap: 8, marginTop: 12 }}>
          {status === 'pending' && <View style={{ flex: 1 }}><ActionButton label="Start" secondary onPress={() => void action(row, 'start')} busy={acting === key} /></View>}
          <View style={{ flex: 1 }}><ActionButton label="Mark complete" onPress={() => void action(row, 'complete')} busy={acting === key} /></View>
          <View style={{ flex: 1 }}><ActionButton label="Report bug" secondary onPress={() => router.push({ pathname: '/action/bugReport', params: { assignmentId: key } } as never)} /></View>
        </View>}
        {!!mayWrite && feature?.id === 'contacts' && status !== 'closed' && <ActionButton label={status === 'new' ? 'Mark contacted' : status === 'contacted' ? 'Qualify contact' : 'Close contact'} secondary onPress={() => void action(row, 'contactStatus')} busy={acting === key} />}
        {!!mayWrite && feature?.id === 'partnerApps' && status === 'pending' && <ActionButton label="Start review" secondary onPress={() => void action(row, 'partnerReview')} busy={acting === key} />}
        {!!mayWrite && feature?.id === 'partnerApps' && ['pending','reviewing'].includes(status) && <View style={{ flexDirection: 'row', gap: 8 }}><View style={{ flex: 1 }}><ActionButton label="Approve" onPress={() => void action(row, 'partnerApprove')} busy={acting === key} /></View><View style={{ flex: 1 }}><ActionButton label="Reject" secondary onPress={() => void action(row, 'partnerReject')} busy={acting === key} /></View></View>}
        {!!mayWrite && feature?.id === 'courses' && !['enrolled','in_progress','completed'].includes(status) && <ActionButton label="Enroll in course" onPress={() => void action(row, 'enroll')} busy={acting === key} />}
        {!!mayWrite && feature?.id === 'quotes' && status === 'draft' && <ActionButton label="Mark as sent" onPress={() => void action(row, 'sent')} busy={acting === key} />}
        {!!mayWrite && feature?.id === 'tickets' && <ActionButton label="Reply to ticket" secondary onPress={() => router.push({ pathname: '/action/reply', params: { recordId: key } } as never)} />}
      </Card>;
    })}
    {!session.demo && feature?.writeScope && !mayWrite && <Notice>This account has read access only. Ask your administrator for the {feature.writeScope} permission to submit changes.</Notice>}
    {!!feature?.demoOnly && <Notice>Live API actions for this feature have not been published yet.</Notice>}
    <ActionButton label="Home" secondary onPress={() => router.replace('/workspace')} />
  </Page>;
}
