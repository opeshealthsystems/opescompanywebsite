import { useLocalSearchParams, useRouter } from 'expo-router';
import { useEffect, useMemo, useState } from 'react';
import { Pressable, Text, View } from 'react-native';
import { ApiRecord, FeatureId, featureCatalog, useSession } from '../context/SessionContext';
import { ActionButton, Brand, Field, Heading, Notice, Page, colors } from './ui';

type Values = Record<string, string>;
const tomorrow = () => { const date = new Date(); date.setDate(date.getDate() + 1); return date.toISOString().slice(0, 10); };
const initial: Record<string, Values> = {
  quote: { title: '', product: '', quantity: '1', price: '', currency: 'XAF', notes: '' },
  customer: { userId: '', facility: '', facilityType: 'clinic', country: 'Cameroon', city: '', address: '' },
  contact: { name: '', email: '', phone: '', facility_type: 'clinic', products: '', message: '', source: 'contact' },
  deal: { title: '', stage: 'qualification', value: '', currency: 'XAF', probability: '25', expectedClose: '', notes: '' },
  serviceRequest: { product: '', description: '', date: tomorrow(), time: '', location: '' },
  ticket: { subject: '', description: '', type: 'support', priority: 'medium' },
  customerProfile: { name: '', facility_name: '', facility_type: '', country: '', city: '', phone: '', address: '' },
  practitionerProfile: { name: '', phone: '', specialty: '', workplace_name: '', workplace_city: '', workplace_country: '', years_of_experience: '', bio: '' },
  bugReport: { subject: '', description: '', priority: 'medium' },
  reply: { body: '' },
  partnerApplication: { organization_name: '', contact_name: '', email: '', phone: '', country: '', city: '', partner_type: 'referral', organization_type: '', target_market: '', description: '', website: '' },
};
const titles: Record<string, string> = {
  quote: 'Prepare a quote', customer: 'Create customer facility profile', contact: 'Add a contact or enquiry', deal: 'Add CRM deal', serviceRequest: 'Request a service', ticket: 'Open support ticket',
  customerProfile: 'Edit account profile', practitionerProfile: 'Edit practitioner profile', bugReport: 'Report a bug', reply: 'Reply to support', partnerApplication: 'Partnership application',
};

export default function ActionFormScreen() {
  const router = useRouter();
  const params = useLocalSearchParams<{ type: string; recordId?: string; assignmentId?: string }>();
  const type = String(params.type ?? '');
  const session = useSession();
  const [values, setValues] = useState<Values>(initial[type] ?? {});
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState('');
  const [saved, setSaved] = useState(false);
  const profileFeature = useMemo(() => type === 'customerProfile' ? 'customerProfile' : type === 'practitionerProfile' ? 'practitionerProfile' : null, [type]);

  useEffect(() => {
    if (!profileFeature) return;
    session.rowsFor(profileFeature).then(rows => { if (rows[0]) setValues(current => ({ ...current, ...Object.fromEntries(Object.entries(rows[0]).map(([k,v]) => [k, v == null ? '' : String(v)])) })); }).catch(e => setError(e instanceof Error ? e.message : 'Could not load the current profile.'));
  }, [profileFeature, session]);

  const set = (field: string, value: string) => setValues(current => ({ ...current, [field]: value }));
  const validate = () => {
    if (type === 'quote' && (!values.title.trim() || !values.product.trim() || Number(values.quantity) <= 0 || Number(values.price) < 0)) return 'Enter a quote title, an item, a positive quantity and a valid price.';
    if (type === 'customer' && (!values.userId.trim() || !values.facility.trim())) return 'Enter the customer account ID and facility name.';
    if (type === 'contact' && (!values.name.trim() || !values.email.trim())) return 'Enter the contact name and email address.';
    if (type === 'deal' && (!values.title.trim() || !values.value.trim() || Number(values.probability) < 0 || Number(values.probability) > 100)) return 'Enter the deal title, value and a probability from 0 to 100.';
    if (type === 'serviceRequest' && !values.date.trim()) return 'Choose a preferred service date.';
    if (type === 'ticket' && (!values.subject.trim() || !values.description.trim())) return 'Enter both a subject and a description.';
    if ((type === 'customerProfile' || type === 'practitionerProfile') && !values.name?.trim()) return 'Your name is required.';
    if (type === 'bugReport' && (!values.subject.trim() || !values.description.trim())) return 'Enter both a bug title and the steps or details.';
    if (type === 'reply' && !values.body.trim()) return 'Write a reply before sending.';
    if (type === 'partnerApplication' && (!values.organization_name.trim() || !values.contact_name.trim() || !values.email.trim() || !values.country.trim() || !values.description.trim())) return 'Complete the organization, contact, email, country and partnership summary fields.';
    return '';
  };

  const submit = async () => {
    const validation = validate(); if (validation) { setError(validation); return; }
    setBusy(true); setError('');
    try {
      let target: FeatureId; let body: ApiRecord; let method = 'POST'; let path = '';
      switch (type) {
        case 'quote':
          target = 'quotes'; body = { title: values.title.trim(), currency: values.currency, notes: values.notes || null, items: [{ product_name: values.product.trim(), quantity: Number(values.quantity), unit_price: Number(values.price) }] }; path = '/finance/quotes'; break;
        case 'customer':
          target = 'customers'; body = { user_id: Number(values.userId), facility_name: values.facility.trim(), facility_type: values.facilityType, country: values.country || 'CM', city: values.city || null, address: values.address || null }; path = '/admin/customer-profiles'; break;
        case 'contact':
          target = 'contacts'; body = { name: values.name.trim(), email: values.email.trim(), phone: values.phone || null, facility_type: values.facility_type || null, products: values.products || null, message: values.message || null, source: values.source || 'contact' }; path = '/admin/enquiries/leads'; break;
        case 'deal': {
          target = 'deals'; const ownerId = Number(session.account?.id); if (!Number.isInteger(ownerId)) throw new Error('The account ID from the API is not a numeric user ID, so the deal cannot be assigned safely.');
          body = { title: values.title.trim(), value: Number(values.value), currency: values.currency, probability: Number(values.probability), expected_close_date: values.expectedClose || null, owner_id: ownerId, notes: values.notes || null }; path = '/crm/deals'; break;
        }
        case 'serviceRequest':
          target = 'serviceRequests'; body = { product_slug: values.product || null, description: values.description || null, preferred_date: values.date, preferred_time: values.time || null, location: values.location || null }; path = '/customer/service-requests'; break;
        case 'ticket':
          target = 'tickets'; body = { subject: values.subject.trim(), description: values.description.trim(), type: values.type, priority: values.priority }; path = '/customer/tickets'; break;
        case 'customerProfile':
          target = 'customerProfile'; method = 'PUT'; body = { name: values.name.trim(), phone: values.phone || null, facility_name: values.facility_name || null, facility_type: values.facility_type || null, country: values.country || 'CM', city: values.city || null, address: values.address || null }; path = '/customer/profile'; break;
        case 'practitionerProfile':
          target = 'practitionerProfile'; method = 'PUT'; body = { name: values.name.trim(), phone: values.phone || null, specialty: values.specialty || null, workplace_name: values.workplace_name || null, workplace_city: values.workplace_city || null, workplace_country: values.workplace_country || null, years_of_experience: values.years_of_experience ? Number(values.years_of_experience) : null, bio: values.bio || null }; path = '/practitioner/profile'; break;
        case 'bugReport': {
          target = 'assignments'; let assignmentId = params.assignmentId;
          if (!assignmentId) { const assignments = await session.rowsFor('assignments'); assignmentId = String(assignments[0]?.id ?? ''); }
          if (!assignmentId) throw new Error('No testing assignment is available yet. Open an assignment before filing a bug report.');
          body = { subject: values.subject.trim(), description: values.description.trim(), priority: values.priority }; path = `/testing/assignments/${encodeURIComponent(assignmentId)}/bug-reports`; break;
        }
        case 'reply':
          target = 'tickets'; if (!params.recordId) throw new Error('Open a ticket before replying.'); body = { body: values.body.trim() }; path = `/customer/tickets/${encodeURIComponent(params.recordId)}/reply`; break;
        case 'partnerApplication':
          target = 'partnerApplication'; body = { organization_name: values.organization_name.trim(), contact_name: values.contact_name.trim(), email: values.email.trim(), phone: values.phone || null, country: values.country.trim(), city: values.city || null, partner_type: values.partner_type, organization_type: values.organization_type || null, target_market: values.target_market || null, description: values.description.trim(), website: values.website || null }; path = '/public/become-a-partner'; break;
        default: throw new Error('This action is not supported.');
      }
      if (session.demo) {
        const demoAction = type === 'customerProfile' || type === 'practitionerProfile' ? 'update-profile' : type === 'reply' ? 'reply' : type === 'bugReport' ? 'bug-report' : 'create';
        const recordId = type === 'bugReport' ? String(params.assignmentId ?? 'DEMO-TA-01') : params.recordId;
        session.mutateDemo(target, demoAction, body, recordId);
      } else {
        const feature = featureCatalog[target];
        const needsWrite = type !== 'partnerApplication';
        if (needsWrite && !session.isAllowed(feature, true)) throw new Error(`This account does not have ${feature.writeScope ?? 'write'} access.`);
        await session.request(path, method, body);
      }
      setSaved(true);
    } catch (e) { setError(e instanceof Error ? e.message : 'Could not save your changes.'); }
    finally { setBusy(false); }
  };

  const targetFeature: FeatureId = type === 'quote' ? 'quotes' : type === 'customer' ? 'customers' : type === 'contact' ? 'contacts' : type === 'deal' ? 'deals' : type === 'serviceRequest' ? 'serviceRequests' : type === 'ticket' || type === 'reply' ? 'tickets' : type === 'customerProfile' ? 'customerProfile' : type === 'practitionerProfile' ? 'practitionerProfile' : type === 'bugReport' ? 'assignments' : type === 'partnerApplication' ? 'partnerships' : 'quotes';

  return <Page>
    <View style={{ flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' }}><Brand compact /><Pressable onPress={() => router.back()}><Text style={{ color: colors.blue, fontWeight: '700', padding: 8 }}>Cancel</Text></Pressable></View>
    {!!session.demo && <Notice>DEMO MODE · Submitting here changes only fictional sample data on this device.</Notice>}
    <Heading eyebrow={session.demo ? 'Demo action' : 'Account action'} title={titles[type] ?? 'Complete action'} subtitle={session.demo ? 'Try this workflow with sample information; nothing is sent to OPES.' : 'This form submits through your account’s authorized OPES API.'} />
    {type === 'quote' && <>
      <Field label="Quote title" value={values.title} onChangeText={v => set('title',v)} />
      <Field label="Product or service" value={values.product} onChangeText={v => set('product',v)} />
      <View style={{ flexDirection: 'row', gap: 10 }}><View style={{ flex: 1 }}><Field label="Quantity" value={values.quantity} onChangeText={v => set('quantity',v)} keyboardType="decimal-pad" /></View><View style={{ flex: 1 }}><Field label="Unit price" value={values.price} onChangeText={v => set('price',v)} keyboardType="decimal-pad" /></View></View>
      <CurrencyPicker value={values.currency} onChange={v => set('currency',v)} /><Field label="Notes" value={values.notes} onChangeText={v => set('notes',v)} multiline />
      <Notice>Approximate subtotal: {values.currency} {(Number(values.price || 0) * Number(values.quantity || 0)).toLocaleString()}</Notice>
    </>}
    {type === 'customer' && <><Field label="Customer account ID" value={values.userId} onChangeText={v => set('userId',v)} keyboardType="numeric" /><Field label="Facility name" value={values.facility} onChangeText={v => set('facility',v)} /><Choice label="Facility type" value={values.facilityType} choices={['clinic','hospital','laboratory','pharmacy','specialist_centre','ngos','government','other']} onChange={v => set('facilityType',v)} /><Field label="Country" value={values.country} onChangeText={v => set('country',v)} /><Field label="City" value={values.city} onChangeText={v => set('city',v)} /><Field label="Address" value={values.address} onChangeText={v => set('address',v)} /></>}
    {type === 'contact' && <><Field label="Contact name" value={values.name} onChangeText={v => set('name',v)} /><Field label="Email" value={values.email} onChangeText={v => set('email',v)} keyboardType="email-address" autoCapitalize="none" /><Field label="Phone" value={values.phone} onChangeText={v => set('phone',v)} keyboardType="phone-pad" /><Choice label="Facility type" value={values.facility_type} choices={['clinic','hospital','laboratory','pharmacy','government','ngo','other']} onChange={v => set('facility_type',v)} /><Field label="Products of interest" value={values.products} onChangeText={v => set('products',v)} /><Field label="Notes and enquiry" value={values.message} onChangeText={v => set('message',v)} multiline /></>}
    {type === 'deal' && <><Field label="Deal title" value={values.title} onChangeText={v => set('title',v)} /><Choice label="Pipeline stage" value={values.stage} choices={['qualification','discovery','proposal','negotiation','closed_won','closed_lost']} onChange={v => set('stage',v)} /><View style={{ flexDirection: 'row', gap: 10 }}><View style={{ flex: 1 }}><Field label="Value" value={values.value} onChangeText={v => set('value',v)} keyboardType="decimal-pad" /></View><View style={{ flex: 1 }}><Field label="Probability (%)" value={values.probability} onChangeText={v => set('probability',v)} keyboardType="numeric" /></View></View><CurrencyPicker value={values.currency} onChange={v => set('currency',v)} /><Field label="Expected close date (YYYY-MM-DD)" value={values.expectedClose} onChangeText={v => set('expectedClose',v)} /><Field label="Notes" value={values.notes} onChangeText={v => set('notes',v)} multiline /></>}
    {type === 'serviceRequest' && <><Field label="Product slug (optional)" value={values.product} onChangeText={v => set('product',v)} autoCapitalize="none" /><Field label="What service do you need?" value={values.description} onChangeText={v => set('description',v)} multiline /><Field label="Preferred date (YYYY-MM-DD)" value={values.date} onChangeText={v => set('date',v)} /><Field label="Preferred time" value={values.time} onChangeText={v => set('time',v)} placeholder="09:00" /><Field label="Location" value={values.location} onChangeText={v => set('location',v)} /></>}
    {type === 'ticket' && <><Field label="Subject" value={values.subject} onChangeText={v => set('subject',v)} /><Field label="Describe the issue" value={values.description} onChangeText={v => set('description',v)} multiline /><Choice label="Type" value={values.type} choices={['support','billing','technical','bug_report','other']} onChange={v => set('type',v)} /><Choice label="Priority" value={values.priority} choices={['low','medium','high','urgent']} onChange={v => set('priority',v)} /></>}
    {type === 'customerProfile' && <><Field label="Your name" value={values.name} onChangeText={v => set('name',v)} /><Field label="Facility" value={values.facility_name} onChangeText={v => set('facility_name',v)} /><Field label="Facility type" value={values.facility_type} onChangeText={v => set('facility_type',v)} /><Field label="Phone" value={values.phone} onChangeText={v => set('phone',v)} keyboardType="phone-pad" /><Field label="Country" value={values.country} onChangeText={v => set('country',v)} /><Field label="City" value={values.city} onChangeText={v => set('city',v)} /><Field label="Address" value={values.address} onChangeText={v => set('address',v)} /></>}
    {type === 'practitionerProfile' && <><Field label="Full name" value={values.name} onChangeText={v => set('name',v)} /><Field label="Phone" value={values.phone} onChangeText={v => set('phone',v)} keyboardType="phone-pad" /><Field label="Specialty" value={values.specialty} onChangeText={v => set('specialty',v)} /><Field label="Workplace" value={values.workplace_name} onChangeText={v => set('workplace_name',v)} /><Field label="Workplace city" value={values.workplace_city} onChangeText={v => set('workplace_city',v)} /><Field label="Workplace country" value={values.workplace_country} onChangeText={v => set('workplace_country',v)} /><Field label="Years of experience" value={values.years_of_experience} onChangeText={v => set('years_of_experience',v)} keyboardType="numeric" /><Field label="Professional bio" value={values.bio} onChangeText={v => set('bio',v)} multiline /></>}
    {type === 'bugReport' && <><Field label="Bug title" value={values.subject} onChangeText={v => set('subject',v)} /><Field label="Steps and expected result" value={values.description} onChangeText={v => set('description',v)} multiline /><Choice label="Priority" value={values.priority} choices={['low','medium','high','urgent']} onChange={v => set('priority',v)} /></>}
    {type === 'reply' && <><Field label="Reply" value={values.body} onChangeText={v => set('body',v)} multiline /><Text style={{ color: colors.muted, fontSize: 12 }}>Your reply will be added to ticket {params.recordId}.</Text></>}
    {type === 'partnerApplication' && <><Field label="Organization name" value={values.organization_name} onChangeText={v => set('organization_name',v)} /><Field label="Contact name" value={values.contact_name} onChangeText={v => set('contact_name',v)} /><Field label="Email" value={values.email} onChangeText={v => set('email',v)} keyboardType="email-address" autoCapitalize="none" /><Field label="Phone (optional)" value={values.phone} onChangeText={v => set('phone',v)} keyboardType="phone-pad" /><Field label="Country" value={values.country} onChangeText={v => set('country',v)} /><Field label="City" value={values.city} onChangeText={v => set('city',v)} /><Choice label="Partnership type" value={values.partner_type} choices={['referral','technology','distribution','implementation','other']} onChange={v => set('partner_type',v)} /><Field label="Organization type (optional)" value={values.organization_type} onChangeText={v => set('organization_type',v)} /><Field label="Target market" value={values.target_market} onChangeText={v => set('target_market',v)} multiline /><Field label="How would you like to partner?" value={values.description} onChangeText={v => set('description',v)} multiline /><Field label="Website (optional)" value={values.website} onChangeText={v => set('website',v)} autoCapitalize="none" /></>}

    {!!error && <Notice tone="error">{error}</Notice>}
    {saved ? <><Notice tone="success">{session.demo ? 'Demo action saved locally.' : 'Your changes were submitted successfully.'}</Notice><ActionButton label="View updated workspace" onPress={() => router.replace({ pathname: '/feature/[id]', params: { id: targetFeature } } as never)} /><ActionButton label="Back to dashboard" secondary onPress={() => router.replace('/workspace')} /></> : <ActionButton label={busy ? 'Saving…' : type === 'reply' ? 'Send reply' : type === 'partnerApplication' ? 'Submit application' : 'Save and continue'} onPress={() => void submit()} disabled={busy} busy={busy} />}
  </Page>;
}

function CurrencyPicker({ value, onChange }: { value: string; onChange: (value: string) => void }) {
  return <View style={{ marginBottom: 14 }}><Text style={{ color: colors.text, fontSize: 12, fontWeight: '700', marginBottom: 6 }}>Currency</Text><View style={{ flexDirection: 'row', gap: 7 }}>{['XAF','USD','EUR'].map(item => <Pressable key={item} onPress={() => onChange(item)} style={{ paddingHorizontal: 14, paddingVertical: 9, borderRadius: 6, borderWidth: 1, borderColor: value === item ? colors.navy : colors.border, backgroundColor: value === item ? '#E8F1F8' : colors.white }}><Text style={{ color: colors.navy, fontWeight: '700', fontSize: 12 }}>{item}</Text></Pressable>)}</View></View>;
}

function Choice({ label, value, choices, onChange }: { label: string; value: string; choices: string[]; onChange: (value: string) => void }) {
  return <View style={{ marginBottom: 14 }}><Text style={{ color: colors.text, fontSize: 12, fontWeight: '700', marginBottom: 6 }}>{label}</Text><View style={{ flexDirection: 'row', gap: 7, flexWrap: 'wrap' }}>{choices.map(item => <Pressable key={item} onPress={() => onChange(item)} style={{ paddingHorizontal: 11, paddingVertical: 8, borderRadius: 6, borderWidth: 1, borderColor: value === item ? colors.navy : colors.border, backgroundColor: value === item ? '#E8F1F8' : colors.white }}><Text style={{ color: colors.navy, fontSize: 12, fontWeight: '600' }}>{item.replaceAll('_',' ')}</Text></Pressable>)}</View></View>;
}
