import { useLocalSearchParams, useRouter } from 'expo-router';
import { useCallback, useEffect, useState } from 'react';
import { ActivityIndicator, Pressable, Text, View } from 'react-native';
import { ApiRecord, featureCatalog, FeatureId, useSession } from '../context/SessionContext';
import { ActionButton, Card, Heading, Notice, Page, SectionTitle, colors } from './ui';
import { AppIcon } from './icons';
import { AppHeader } from './AppHeader';

function display(value: unknown): string {
  if (value === null || value === undefined || value === '') return '—';
  if (typeof value === 'object') return JSON.stringify(value, null, 2);
  return String(value);
}

function DetailRow({ label, value }: { label: string; value: unknown }) {
  if (value === null || value === undefined || value === '') return null;
  return <View style={{ flexDirection: 'row', flexWrap: 'wrap', paddingVertical: 6, gap: 6, borderBottomWidth: 1, borderBottomColor: '#EEF2F5' }}>
    <Text style={{ color: colors.muted, fontSize: 12, lineHeight: 18, flex: 0.8, minWidth: 92 }}>{label.replaceAll('_', ' ')}</Text>
    <Text selectable style={{ color: colors.text, fontSize: 13, lineHeight: 19, flex: 1.2, minWidth: 120 }}>{display(value)}</Text>
  </View>;
}

function QuoteDetails({ row }: { row: ApiRecord }) {
  const knownFields = ['id', 'reference', 'title', 'status', 'created_at', 'valid_until', 'currency', 'subtotal', 'tax_rate', 'tax_amount', 'total', 'notes', 'lead', 'creator', 'items'];
  const extras = Object.entries(row).filter(([field]) => !knownFields.includes(field));
  const lead = row.lead && typeof row.lead === 'object' ? row.lead as ApiRecord : null;
  const creator = row.creator && typeof row.creator === 'object' ? row.creator as ApiRecord : null;
  const items = Array.isArray(row.items) ? row.items as ApiRecord[] : [];
  const money = (value: unknown) => value === null || value === undefined || value === '' ? '—' : `${String(row.currency ?? '')} ${Number(value).toLocaleString()}`.trim();

  return <View>
    <Text style={{ color: colors.navy, fontSize: 14, fontWeight: '800', marginBottom: 4 }}>Quote summary</Text>
    <DetailRow label="quote_id" value={row.id} />
    <DetailRow label="reference" value={row.reference} />
    <DetailRow label="title" value={row.title} />
    <DetailRow label="status" value={row.status} />
    <DetailRow label="created_at" value={row.created_at} />
    <DetailRow label="valid_until" value={row.valid_until} />
    <DetailRow label="subtotal" value={money(row.subtotal)} />
    <DetailRow label="tax_rate" value={row.tax_rate == null ? null : `${row.tax_rate}%`} />
    <DetailRow label="tax_amount" value={money(row.tax_amount)} />
    <DetailRow label="total" value={money(row.total)} />
    <DetailRow label="notes" value={row.notes} />
    {lead && <>
      <Text style={{ color: colors.navy, fontSize: 14, fontWeight: '800', marginTop: 14, marginBottom: 4 }}>Customer / enquiry</Text>
      {Object.entries(lead).filter(([field]) => field !== 'id').map(([field, value]) => <DetailRow key={`lead-${field}`} label={field} value={value} />)}
    </>}
    {creator && <>
      <Text style={{ color: colors.navy, fontSize: 14, fontWeight: '800', marginTop: 14, marginBottom: 4 }}>Prepared by</Text>
      {Object.entries(creator).filter(([field]) => ['name', 'email'].includes(field)).map(([field, value]) => <DetailRow key={`creator-${field}`} label={field} value={value} />)}
    </>}
    <Text style={{ color: colors.navy, fontSize: 14, fontWeight: '800', marginTop: 14, marginBottom: 4 }}>Line items · {items.length}</Text>
    {items.length === 0 && <Text style={{ color: colors.muted, fontSize: 13, lineHeight: 19, paddingVertical: 6 }}>No line item detail was included in this response.</Text>}
    {items.map((item, index) => <View key={String(item.id ?? index)} style={{ backgroundColor: '#F5F8FC', borderRadius: 7, padding: 10, marginTop: 6 }}>
      <Text style={{ color: colors.navy, fontSize: 13, fontWeight: '800' }}>{String(item.product_name ?? item.title ?? item.description ?? `Item ${index + 1}`)}</Text>
      {item.description != null && String(item.description).length > 0 && !!item.product_name && <Text style={{ color: colors.muted, fontSize: 12, lineHeight: 17, marginTop: 3 }}>{String(item.description)}</Text>}
      <Text style={{ color: colors.text, fontSize: 12, lineHeight: 18, marginTop: 5 }}>Quantity {display(item.quantity)} · Unit {money(item.unit_price)} · Line total {money(item.total ?? (Number(item.quantity ?? 0) * Number(item.unit_price ?? 0)))}</Text>
      {Object.entries(item).filter(([field]) => !['id', 'product_name', 'title', 'description', 'quantity', 'unit_price', 'total', 'quote_id', 'created_at', 'updated_at'].includes(field)).map(([field, value]) => <DetailRow key={`item-${index}-${field}`} label={field} value={value} />)}
    </View>)}
    {extras.map(([field, value]) => <DetailRow key={`extra-${field}`} label={field} value={value} />)}
  </View>;
}

export function FeatureContent({ featureId, inTab = false }: { featureId?: FeatureId; inTab?: boolean }) {
  const router = useRouter();
  const { id } = useLocalSearchParams<{ id: string }>();
  const selectedId = featureId ?? id as FeatureId;
  const feature = featureCatalog[selectedId];
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
    if (feature.id === 'partnerApplication') { setRows([]); setBusy(false); setError(''); return; }
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

  return <Page fixedHeader={<AppHeader />}>
    {!inTab && <Pressable accessibilityRole="button" onPress={() => router.back()} style={{ minHeight: 44, flexDirection: 'row', alignItems: 'center', gap: 4 }}><AppIcon name="chevronLeft" size={18} /><Text style={{ color: colors.blue, fontSize: 14, fontWeight: '700' }}>Back to workspace</Text></Pressable>}
    {!!session.demo && <Notice>DEMO MODE · All records and actions on this screen are fictional and local to this app.</Notice>}
    <Heading eyebrow="Workspace" title={title} subtitle={feature?.description ?? 'Manage account information and work items.'} />
    {feature?.createAction && mayWrite && <ActionButton label={feature.id === 'partnerApplication' ? 'Start an application' : feature.id === 'contacts' ? 'Add a contact' : `Create ${feature.title.toLowerCase().replace(/s$/, '')}`} onPress={startCreate} />}
    {!!error && <Notice tone="error">{error}</Notice>}{!!message && <Notice tone="success">{message}</Notice>}
    <SectionTitle title={feature?.id === 'partnerApplication' ? 'Application information' : `Records · ${rows.length}`} trailing={<Pressable onPress={() => void load()}><Text style={{ color: colors.blue, fontWeight: '700' }}>Refresh</Text></Pressable>} />
    {busy ? <ActivityIndicator color={colors.blue} style={{ marginVertical: 24 }} /> : null}
    {!busy && rows.length === 0 && !error && feature?.id !== 'partnerApplication' && <Card><Text style={{ color: colors.text, fontSize: 14, fontWeight: '700' }}>Nothing here yet</Text><Text style={{ color: colors.muted, fontSize: 12, lineHeight: 18, marginTop: 5 }}>Use the action above to create your first item, or pull down Refresh to check again.</Text></Card>}
    {!busy && feature?.id === 'partnerApplication' && <Card><Text style={{ color: colors.navy, fontSize: 15, fontWeight: '800' }}>Build a partnership with OPES</Text><Text style={{ color: colors.muted, fontSize: 13, lineHeight: 20, marginTop: 7 }}>Tell us about your organization, the communities you serve and the kind of collaboration you have in mind. The partnerships team will review your application and follow up using your contact details.</Text><Text style={{ color: colors.text, fontSize: 12, lineHeight: 19, marginTop: 10 }}>Have your organization details, country, contact email and a short partnership proposal ready.</Text></Card>}
    {rows.map((row, index) => {
      const key = String(row.id ?? row.slug ?? index); const isOpen = expanded === key;
      const titleValue = String(row.title ?? row.subject ?? row.name ?? row.organization_name ?? row.reference ?? row.product_name ?? `Record ${index + 1}`);
      const status = String(row.status ?? row.state ?? row.stage ?? '');
      return <Card key={key}>
        <Pressable accessibilityRole="button" onPress={() => setExpanded(isOpen ? null : key)}>
          <View style={{ flexDirection: 'row', flexWrap: 'wrap', justifyContent: 'space-between', alignItems: 'flex-start', gap: 8 }}><Text style={{ color: colors.navy, fontSize: 15, fontWeight: '700', flex: 1, minWidth: 0, flexShrink: 1 }}>{titleValue}</Text>{!!status && <Text style={{ color: colors.blue, fontSize: 12, fontWeight: '800', textTransform: 'uppercase', flexShrink: 1, textAlign: 'right' }}>{status.replaceAll('_', ' ')}</Text>}</View>
          <Text style={{ color: colors.muted, fontSize: 12, lineHeight: 18, marginTop: 6 }}>{[row.reference, row.description, row.organization_name, row.product, row.due_date].filter(Boolean).map(display).join(' · ') || feature?.description}</Text>
          <View style={{ flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', marginTop: 8 }}><Text style={{ color: colors.blue, fontSize: 13, fontWeight: '700' }}>{isOpen ? (feature?.id === 'quotes' ? 'Hide quote details' : feature?.id === 'customers' ? 'Hide customer details' : 'Hide details') : (feature?.id === 'quotes' ? 'View quote' : feature?.id === 'customers' ? 'View customer details' : 'View details')}</Text><AppIcon name="chevronRight" size={18} /></View>
        </Pressable>
        {feature?.id === 'quotes' && <View style={{ flexDirection: 'row', gap: 8, marginTop: 12 }}>
          <View style={{ flex: 1, minWidth: 0 }}><ActionButton label={isOpen ? 'Hide quote' : 'View quote'} secondary onPress={() => setExpanded(isOpen ? null : key)} /></View>
          <View style={{ flex: 1, minWidth: 0 }}><ActionButton label={status === 'draft' || !status ? 'Mark as sent' : status.replaceAll('_', ' ')} secondary={status !== 'draft' && !!status} onPress={() => { if (status === 'draft' && mayWrite) void action(row, 'sent'); }} disabled={status !== 'draft' || !mayWrite} busy={acting === key && status === 'draft'} /></View>
        </View>}
        {isOpen && <View style={{ borderTopWidth: 1, borderTopColor: colors.border, marginTop: 12, paddingTop: 10 }}>
          {feature?.id === 'quotes' ? <QuoteDetails row={row} /> : <>
            {Object.entries(row).filter(([field]) => !(feature?.id !== 'customers' && field === 'id') && !['items','replies'].includes(field)).map(([field, value]) => <DetailRow key={field} label={field} value={value} />)}
            {Array.isArray(row.items) && <View style={{ marginTop: 6 }}>{(row.items as ApiRecord[]).map((item, i) => <Text key={i} style={{ color: colors.text, fontSize: 12, marginTop: 4 }}>{String(item.product_name ?? item.description ?? 'Item')} · {display(item.quantity)} × {display(item.unit_price)}</Text>)}</View>}
            {Array.isArray(row.replies) && <View style={{ marginTop: 8 }}>{(row.replies as ApiRecord[]).map((reply, i) => <Text key={i} style={{ color: colors.muted, fontSize: 12, marginTop: 4 }}>{String(reply.author ?? 'Reply')}: {String(reply.body ?? '')}</Text>)}</View>}
          </>}
        </View>}
        {!!mayWrite && feature?.id === 'assignments' && status !== 'completed' && <View style={{ flexDirection: 'row', flexWrap: 'wrap', gap: 8, marginTop: 12 }}>
          {status === 'pending' && <View style={{ flexGrow: 1, flexBasis: 130, minWidth: 130 }}><ActionButton label="Start" secondary onPress={() => void action(row, 'start')} busy={acting === key} /></View>}
          <View style={{ flexGrow: 1, flexBasis: 130, minWidth: 130 }}><ActionButton label="Mark complete" onPress={() => void action(row, 'complete')} busy={acting === key} /></View>
          <View style={{ flexGrow: 1, flexBasis: 130, minWidth: 130 }}><ActionButton label="Report bug" secondary onPress={() => router.push({ pathname: '/action/bugReport', params: { assignmentId: key } } as never)} /></View>
        </View>}
        {!!mayWrite && feature?.id === 'contacts' && status !== 'closed' && <ActionButton label={status === 'new' ? 'Mark contacted' : status === 'contacted' ? 'Qualify contact' : 'Close contact'} secondary onPress={() => void action(row, 'contactStatus')} busy={acting === key} />}
        {!!mayWrite && feature?.id === 'partnerApps' && status === 'pending' && <ActionButton label="Start review" secondary onPress={() => void action(row, 'partnerReview')} busy={acting === key} />}
        {!!mayWrite && feature?.id === 'partnerApps' && ['pending','reviewing'].includes(status) && <View style={{ flexDirection: 'row', gap: 8 }}><View style={{ flex: 1 }}><ActionButton label="Approve" onPress={() => void action(row, 'partnerApprove')} busy={acting === key} /></View><View style={{ flex: 1 }}><ActionButton label="Reject" secondary onPress={() => void action(row, 'partnerReject')} busy={acting === key} /></View></View>}
        {!!mayWrite && feature?.id === 'courses' && !['enrolled','in_progress','completed'].includes(status) && <ActionButton label="Enroll in course" onPress={() => void action(row, 'enroll')} busy={acting === key} />}
        {!!mayWrite && feature?.id === 'tickets' && <ActionButton label="Reply to ticket" secondary onPress={() => router.push({ pathname: '/action/reply', params: { recordId: key } } as never)} />}
      </Card>;
    })}
    {!session.demo && feature?.writeScope && !mayWrite && <Notice>This account has read access only. Ask your administrator for the {feature.writeScope} permission to submit changes.</Notice>}
    {!!feature?.demoOnly && <Notice>Live API actions for this feature have not been published yet.</Notice>}
    {!inTab && <ActionButton label="Home" secondary onPress={() => router.replace('/workspace')} />}
  </Page>;
}

export default function FeatureScreen() {
  return <FeatureContent />;
}
