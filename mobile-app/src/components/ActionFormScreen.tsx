import { useLocalSearchParams, useRouter } from 'expo-router';
import { useEffect, useMemo, useRef, useState } from 'react';
import { Pressable, Text, View } from 'react-native';
import { ApiRecord, FeatureId, featureCatalog, useSession } from '../context/SessionContext';
import { ActionButton, DateField, Field, Heading, humanize, localDate, Notice, Page, colors, radius } from './ui';
import { AppHeader } from './AppHeader';
import { dateLocale, t } from '../i18n';

type Values = Record<string, string>;
const tomorrow = () => { const date = new Date(); date.setDate(date.getDate() + 1); return localDate(date); };
const initial: Record<string, Values> = {
  quote: { title: '', product: '', quantity: '1', price: '', currency: 'XAF', notes: '' },
  customer: { userId: '', facility: '', facilityType: 'clinic', country: 'Cameroon', city: '', address: '' },
  contact: { name: '', email: '', phone: '', facility_type: 'clinic', products: '', message: '', source: 'contact' },
  deal: { title: '', stage: 'qualification', value: '', currency: 'XAF', probability: '25', expectedClose: '', notes: '' },
  serviceRequest: { type: 'installation', product: '', description: '', date: tomorrow(), time: '', location: '' },
  ticket: { subject: '', description: '', type: 'support', priority: 'medium' },
  customerProfile: { name: '', facility_name: '', facility_type: '', country: '', city: '', phone: '', address: '' },
  practitionerProfile: { name: '', phone: '', specialty: '', workplace_name: '', workplace_city: '', workplace_country: '', years_of_experience: '', bio: '' },
  bugReport: { subject: '', description: '', priority: 'medium' },
  reply: { body: '' },
  partnerApplication: { organization_name: '', contact_name: '', email: '', phone: '', country: '', city: '', partner_type: 'referral', organization_type: '', target_market: '', description: '', website: '' },
  partnerProfile: { phone: '', country: '', city: '', organization_type: '', target_market: '', description: '', website: '' },
};
const titles: Record<string, string> = {
  quote: 'Prepare a quote', customer: 'Create customer facility profile', contact: 'Add a contact or enquiry', deal: 'Add CRM deal', serviceRequest: 'Request a service', ticket: 'Open support ticket',
  customerProfile: 'Edit account profile', practitionerProfile: 'Edit practitioner profile', bugReport: 'Report a bug', reply: 'Reply to support', partnerApplication: 'Partnership application', partnerProfile: 'Edit organization profile',
};

export default function ActionFormScreen() {
  const router = useRouter();
  const params = useLocalSearchParams<{ type: string; recordId?: string; assignmentId?: string; applicationId?: string }>();
  const type = String(params.type ?? '');
  const session = useSession();
  const [values, setValues] = useState<Values>(initial[type] ?? {});
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState('');
  const [saved, setSaved] = useState(false);
  const [serviceTypes, setServiceTypes] = useState(['installation', 'maintenance', 'training', 'other']);
  const idempotency = useRef({ fingerprint: '', key: '' });
  const profileFeature = useMemo(() => type === 'customerProfile' ? 'customerProfile' : type === 'practitionerProfile' ? 'practitionerProfile' : null, [type]);

  useEffect(() => {
    if (!profileFeature) return;
    session.rowsFor(profileFeature).then(rows => { if (rows[0]) setValues(current => ({ ...current, ...Object.fromEntries(Object.entries(rows[0]).map(([k,v]) => [k, v == null ? '' : String(v)])) })); }).catch(e => setError(e instanceof Error ? e.message : t('Could not load the current profile.')));
  }, [profileFeature, session]);

  useEffect(() => {
    if (type !== 'partnerProfile' || !params.applicationId) return;
    session.rowsFor('partnerAccounts').then(rows => {
      const account = rows.find(row => String(row.application_id ?? row.id) === String(params.applicationId));
      if (account) setValues(current => ({ ...current, ...Object.fromEntries(Object.keys(initial.partnerProfile).map(key => [key, account[key] == null ? '' : String(account[key])])) }));
      else setError(t('This organization is not linked to your account.'));
    }).catch(e => setError(e instanceof Error ? e.message : t('Could not load the organization profile.')));
  }, [type, params.applicationId, session]);

  useEffect(() => {
    if (type !== 'serviceRequest' || session.demo || !session.isAllowed(featureCatalog.serviceRequests)) return;
    session.request<Record<string, unknown>>('/customer/service-requests/options').then(result => {
      const options = result.typeOptions;
      if (options && typeof options === 'object' && !Array.isArray(options)) setServiceTypes(Object.keys(options as Record<string, unknown>));
    }).catch(() => undefined);
  }, [session, type]);

  const set = (field: string, value: string) => setValues(current => ({ ...current, [field]: value }));
  const validate = () => {
    if (type === 'quote' && (!values.title.trim() || !values.product.trim() || Number(values.quantity) <= 0 || Number(values.price) < 0)) return t('Enter a quote title, an item, a positive quantity and a valid price.');
    if (type === 'customer' && (!/^\d+$/.test(values.userId.trim()) || Number(values.userId) < 1 || !values.facility.trim())) return t('Enter a valid customer account ID and facility name.');
    if (type === 'contact' && (!values.name.trim() || !values.email.trim())) return t('Enter the contact name and email address.');
    if (type === 'deal' && (!values.title.trim() || !values.value.trim() || Number(values.probability) < 0 || Number(values.probability) > 100)) return t('Enter the deal title, value and a probability from 0 to 100.');
    if (type === 'serviceRequest' && (!serviceTypes.includes(values.type) || !values.description.trim() || !values.date.trim())) return t('Choose a service type, describe what you need and select a preferred date.');
    if (type === 'ticket' && (!values.subject.trim() || !values.description.trim())) return t('Enter both a subject and a description.');
    if ((type === 'customerProfile' || type === 'practitionerProfile') && !values.name?.trim()) return t('Your name is required.');
    if (type === 'bugReport' && (!values.subject.trim() || !values.description.trim())) return t('Enter both a bug title and the steps or details.');
    if (type === 'reply' && !values.body.trim()) return t('Write a reply before sending.');
    if (type === 'partnerApplication' && (!values.organization_name.trim() || !values.contact_name.trim() || !values.email.trim() || !values.country.trim() || !values.description.trim())) return t('Complete the organization, contact, email, country and partnership summary fields.');
    if (type === 'partnerProfile' && (!params.applicationId || !values.country.trim() || !values.description.trim())) return t('Country and organization description are required.');
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
          target = 'deals'; const ownerId = Number(session.account?.id); if (!Number.isInteger(ownerId)) throw new Error(t('The account ID from the API is not a numeric user ID, so the deal cannot be assigned safely.'));
          body = { title: values.title.trim(), stage: values.stage, value: Number(values.value), currency: values.currency, probability: Number(values.probability), expected_close_date: values.expectedClose || null, owner_id: ownerId, notes: values.notes || null }; path = '/crm/deals'; break;
        }
        case 'serviceRequest':
          target = 'serviceRequests'; body = { type: values.type, product_slug: values.product || null, description: values.description.trim(), preferred_date: values.date, preferred_time: values.time || null, location: values.location || null }; path = '/customer/service-requests'; break;
        case 'ticket':
          target = 'tickets'; body = { subject: values.subject.trim(), description: values.description.trim(), type: values.type, priority: values.priority }; path = '/customer/tickets'; break;
        case 'customerProfile':
          target = 'customerProfile'; method = 'PUT'; body = { name: values.name.trim(), phone: values.phone || null, facility_name: values.facility_name || null, facility_type: values.facility_type || null, country: values.country || 'CM', city: values.city || null, address: values.address || null }; path = '/customer/profile'; break;
        case 'practitionerProfile':
          target = 'practitionerProfile'; method = 'PUT'; body = { name: values.name.trim(), phone: values.phone || null, specialty: values.specialty || null, workplace_name: values.workplace_name || null, workplace_city: values.workplace_city || null, workplace_country: values.workplace_country || null, years_of_experience: values.years_of_experience ? Number(values.years_of_experience) : null, bio: values.bio || null }; path = '/practitioner/profile'; break;
        case 'bugReport': {
          target = 'assignments'; let assignmentId = params.assignmentId;
          // Never guess the assignment: a report filed against the wrong test is misleading triage data.
          if (!assignmentId) throw new Error(t('Open the assignment you tested and use Report bug there, so the report is filed against the right test.'));
          body = { subject: values.subject.trim(), description: values.description.trim(), priority: values.priority }; path = `/testing/assignments/${encodeURIComponent(assignmentId)}/bug-reports`; break;
        }
        case 'reply':
          target = 'tickets'; if (!params.recordId) throw new Error(t('Open a ticket before replying.')); body = { body: values.body.trim() }; path = `/customer/tickets/${encodeURIComponent(params.recordId)}/reply`; break;
        case 'partnerApplication':
          target = 'partnerApplication'; body = { organization_name: values.organization_name.trim(), contact_name: values.contact_name.trim(), email: values.email.trim(), phone: values.phone || null, country: values.country.trim(), city: values.city || null, partner_type: values.partner_type, organization_type: values.organization_type || null, target_market: values.target_market || null, description: values.description.trim(), website: values.website || null }; path = '/public/become-a-partner'; break;
        case 'partnerProfile':
          target = 'partnerAccounts'; method = 'PATCH'; body = { phone: values.phone || null, country: values.country.trim(), city: values.city || null, organization_type: values.organization_type || null, target_market: values.target_market || null, description: values.description.trim(), website: values.website || null }; path = `/partner/accounts/${encodeURIComponent(String(params.applicationId))}`; break;
        default: throw new Error(t('This action is not supported.'));
      }
      if (session.demo && type === 'partnerProfile') throw new Error(t('Organization profile editing is available to linked partner owners on a live account.'));
      if (session.demo) {
        const demoAction = type === 'customerProfile' || type === 'practitionerProfile' ? 'update-profile' : type === 'reply' ? 'reply' : type === 'bugReport' ? 'bug-report' : 'create';
        const recordId = type === 'bugReport' ? String(params.assignmentId ?? '') : params.recordId;
        session.mutateDemo(target, demoAction, body, recordId);
      } else {
        const feature = featureCatalog[target];
        const needsWrite = type !== 'partnerApplication';
        if (needsWrite && !session.isAllowed(feature, true)) throw new Error(t('This account does not have {scope} access.', { scope: feature.writeScope ?? 'write' }));
        const fingerprint = `${method}:${path}:${JSON.stringify(body)}`;
        if (idempotency.current.fingerprint !== fingerprint) idempotency.current = { fingerprint, key: `mobile-${Date.now()}-${Math.random().toString(36).slice(2)}` };
        await session.request(path, method, body, idempotency.current.key);
      }
      setSaved(true);
    } catch (e) { setError(e instanceof Error ? e.message : t('Could not save your changes.')); }
    finally { setBusy(false); }
  };

  const targetFeature: FeatureId = type === 'quote' ? 'quotes' : type === 'customer' ? 'customers' : type === 'contact' ? 'contacts' : type === 'deal' ? 'deals' : type === 'serviceRequest' ? 'serviceRequests' : type === 'ticket' || type === 'reply' ? 'tickets' : type === 'customerProfile' ? 'customerProfile' : type === 'practitionerProfile' ? 'practitionerProfile' : type === 'bugReport' ? 'assignments' : type === 'partnerApplication' ? 'partnerships' : type === 'partnerProfile' ? 'partnerAccounts' : 'quotes';

  return <Page fixedHeader={<AppHeader />}>
    <Pressable accessibilityRole="button" onPress={() => router.back()} style={{ minHeight: 44, alignSelf: 'flex-start', justifyContent: 'center', paddingRight: 12 }}><Text style={{ color: colors.blue, fontWeight: '700' }}>{t('Cancel')}</Text></Pressable>
    {!!session.demo && <Notice>{t('DEMO MODE · Submitting here changes only fictional sample data on this device.')}</Notice>}
    <Heading eyebrow={session.demo ? t('Demo action') : t('Account action')} title={t(titles[type] ?? 'Complete action')} subtitle={session.demo ? t('Try this workflow with sample information; nothing is sent to OPES.') : t('This form submits through your account’s authorized OPES API.')} />
    {type === 'quote' && <>
      <Field label={t('Quote title')} value={values.title} onChangeText={v => set('title',v)} />
      <Field label={t('Product or service')} value={values.product} onChangeText={v => set('product',v)} />
      <View style={{ flexDirection: 'row', gap: 10 }}><View style={{ flex: 1 }}><Field label={t('Quantity')} value={values.quantity} onChangeText={v => set('quantity',v)} keyboardType="decimal-pad" /></View><View style={{ flex: 1 }}><Field label={t('Unit price')} value={values.price} onChangeText={v => set('price',v)} keyboardType="decimal-pad" /></View></View>
      <CurrencyPicker value={values.currency} onChange={v => set('currency',v)} /><Field label={t('Notes')} value={values.notes} onChangeText={v => set('notes',v)} multiline />
      <Notice>{t('Approximate subtotal: {amount}', { amount: `${values.currency} ${(Number(values.price || 0) * Number(values.quantity || 0)).toLocaleString(dateLocale())}` })}</Notice>
    </>}
    {type === 'customer' && <><Field label={t('Customer account ID')} value={values.userId} onChangeText={v => set('userId',v)} keyboardType="numeric" /><Field label={t('Facility name')} value={values.facility} onChangeText={v => set('facility',v)} /><Choice label={t('Facility type')} value={values.facilityType} choices={['clinic','hospital','laboratory','pharmacy','specialist_centre','ngos','government','other']} onChange={v => set('facilityType',v)} /><Field label={t('Country')} value={values.country} onChangeText={v => set('country',v)} /><Field label={t('City')} value={values.city} onChangeText={v => set('city',v)} /><Field label={t('Address')} value={values.address} onChangeText={v => set('address',v)} /></>}
    {type === 'contact' && <><Field label={t('Contact name')} value={values.name} onChangeText={v => set('name',v)} /><Field label={t('Email')} value={values.email} onChangeText={v => set('email',v)} keyboardType="email-address" autoCapitalize="none" /><Field label={t('Phone')} value={values.phone} onChangeText={v => set('phone',v)} keyboardType="phone-pad" /><Choice label={t('Facility type')} value={values.facility_type} choices={['clinic','hospital','laboratory','pharmacy','government','ngo','other']} onChange={v => set('facility_type',v)} /><Field label={t('Products of interest')} value={values.products} onChangeText={v => set('products',v)} /><Field label={t('Notes and enquiry')} value={values.message} onChangeText={v => set('message',v)} multiline /></>}
    {type === 'deal' && <><Field label={t('Deal title')} value={values.title} onChangeText={v => set('title',v)} /><Choice label={t('Pipeline stage')} value={values.stage} choices={['qualification','discovery','proposal','negotiation','closed_won','closed_lost']} onChange={v => set('stage',v)} /><View style={{ flexDirection: 'row', gap: 10 }}><View style={{ flex: 1 }}><Field label={t('Value')} value={values.value} onChangeText={v => set('value',v)} keyboardType="decimal-pad" /></View><View style={{ flex: 1 }}><Field label={t('Probability (%)')} value={values.probability} onChangeText={v => set('probability',v)} keyboardType="numeric" /></View></View><CurrencyPicker value={values.currency} onChange={v => set('currency',v)} /><DateField label={t('Expected close date')} value={values.expectedClose} onChange={v => set('expectedClose',v)} minDate={localDate(new Date())} optional /><Field label={t('Notes')} value={values.notes} onChangeText={v => set('notes',v)} multiline /></>}
    {type === 'serviceRequest' && <><Choice label={t('Service type')} value={values.type} choices={serviceTypes} onChange={v => set('type',v)} /><Field label={t('Product slug (optional)')} value={values.product} onChangeText={v => set('product',v)} autoCapitalize="none" /><Field label={t('What service do you need?')} value={values.description} onChangeText={v => set('description',v)} multiline /><DateField label={t('Preferred date')} value={values.date} onChange={v => set('date',v)} minDate={tomorrow()} /><Field label={t('Preferred time')} value={values.time} onChangeText={v => set('time',v)} placeholder="09:00" /><Field label={t('Location')} value={values.location} onChangeText={v => set('location',v)} /></>}
    {type === 'ticket' && <><Field label={t('Subject')} value={values.subject} onChangeText={v => set('subject',v)} /><Field label={t('Describe the issue')} value={values.description} onChangeText={v => set('description',v)} multiline /><Choice label={t('Type')} value={values.type} choices={['support','billing','technical','bug_report','other']} onChange={v => set('type',v)} /><Choice label={t('Priority')} value={values.priority} choices={['low','medium','high','urgent']} onChange={v => set('priority',v)} /></>}
    {type === 'customerProfile' && <><Field label={t('Your name')} value={values.name} onChangeText={v => set('name',v)} /><Field label={t('Facility')} value={values.facility_name} onChangeText={v => set('facility_name',v)} /><Field label={t('Facility type')} value={values.facility_type} onChangeText={v => set('facility_type',v)} /><Field label={t('Phone')} value={values.phone} onChangeText={v => set('phone',v)} keyboardType="phone-pad" /><Field label={t('Country')} value={values.country} onChangeText={v => set('country',v)} /><Field label={t('City')} value={values.city} onChangeText={v => set('city',v)} /><Field label={t('Address')} value={values.address} onChangeText={v => set('address',v)} /></>}
    {type === 'practitionerProfile' && <><Field label={t('Full name')} value={values.name} onChangeText={v => set('name',v)} /><Field label={t('Phone')} value={values.phone} onChangeText={v => set('phone',v)} keyboardType="phone-pad" /><Field label={t('Specialty')} value={values.specialty} onChangeText={v => set('specialty',v)} /><Field label={t('Workplace')} value={values.workplace_name} onChangeText={v => set('workplace_name',v)} /><Field label={t('Workplace city')} value={values.workplace_city} onChangeText={v => set('workplace_city',v)} /><Field label={t('Workplace country')} value={values.workplace_country} onChangeText={v => set('workplace_country',v)} /><Field label={t('Years of experience')} value={values.years_of_experience} onChangeText={v => set('years_of_experience',v)} keyboardType="numeric" /><Field label={t('Professional bio')} value={values.bio} onChangeText={v => set('bio',v)} multiline /></>}
    {type === 'bugReport' && <><Field label={t('Bug title')} value={values.subject} onChangeText={v => set('subject',v)} /><Field label={t('Steps and expected result')} value={values.description} onChangeText={v => set('description',v)} multiline /><Choice label={t('Priority')} value={values.priority} choices={['low','medium','high','urgent']} onChange={v => set('priority',v)} /></>}
    {type === 'reply' && <><Field label={t('Reply')} value={values.body} onChangeText={v => set('body',v)} multiline /><Text style={{ color: colors.muted, fontSize: 12 }}>{t('Your reply will be added to ticket {id}.', { id: String(params.recordId ?? '') })}</Text></>}
    {type === 'partnerApplication' && <><Field label={t('Organization name')} value={values.organization_name} onChangeText={v => set('organization_name',v)} /><Field label={t('Contact name')} value={values.contact_name} onChangeText={v => set('contact_name',v)} /><Field label={t('Email')} value={values.email} onChangeText={v => set('email',v)} keyboardType="email-address" autoCapitalize="none" /><Field label={t('Phone (optional)')} value={values.phone} onChangeText={v => set('phone',v)} keyboardType="phone-pad" /><Field label={t('Country')} value={values.country} onChangeText={v => set('country',v)} /><Field label={t('City')} value={values.city} onChangeText={v => set('city',v)} /><Choice label={t('Partnership type')} value={values.partner_type} choices={['referral','technology','distribution','implementation','other']} onChange={v => set('partner_type',v)} /><Field label={t('Organization type (optional)')} value={values.organization_type} onChangeText={v => set('organization_type',v)} /><Field label={t('Target market')} value={values.target_market} onChangeText={v => set('target_market',v)} multiline /><Field label={t('How would you like to partner?')} value={values.description} onChangeText={v => set('description',v)} multiline /><Field label={t('Website (optional)')} value={values.website} onChangeText={v => set('website',v)} autoCapitalize="none" /></>}

    {type === 'partnerProfile' && <><Notice>{t('Organization name, approval and partnership status are managed by OPES. This form edits contact and organization details only.')}</Notice><Field label={t('Country')} value={values.country} onChangeText={v => set('country',v)} /><Field label={t('City')} value={values.city} onChangeText={v => set('city',v)} /><Field label={t('Phone')} value={values.phone} onChangeText={v => set('phone',v)} keyboardType="phone-pad" /><Field label={t('Organization type')} value={values.organization_type} onChangeText={v => set('organization_type',v)} /><Field label={t('Target market')} value={values.target_market} onChangeText={v => set('target_market',v)} multiline /><Field label={t('Organization description')} value={values.description} onChangeText={v => set('description',v)} multiline /><Field label={t('Website')} value={values.website} onChangeText={v => set('website',v)} autoCapitalize="none" /> </>}
    {!!error && <Notice tone="error">{error}</Notice>}
    {saved ? <><Notice tone="success">{session.demo ? t('Demo action saved locally.') : t('Your changes were submitted successfully.')}</Notice><ActionButton label={t('View updated workspace')} onPress={() => router.replace({ pathname: '/feature/[id]', params: { id: targetFeature } } as never)} /><ActionButton label={t('Back to dashboard')} secondary onPress={() => router.replace('/workspace')} /></> : <ActionButton label={busy ? t('Saving…') : type === 'reply' ? t('Send reply') : type === 'partnerApplication' ? t('Submit application') : t('Save and continue')} onPress={() => void submit()} disabled={busy} busy={busy} />}
  </Page>;
}

function CurrencyPicker({ value, onChange }: { value: string; onChange: (value: string) => void }) {
  return <View style={{ marginBottom: 14 }}><Text style={{ color: colors.text, fontSize: 12, fontWeight: '700', marginBottom: 6 }}>{t('Currency')}</Text><View style={{ flexDirection: 'row', gap: 7 }}>{['XAF','USD','EUR'].map(item => <Pressable key={item} onPress={() => onChange(item)} style={{ paddingHorizontal: 14, paddingVertical: 9, borderRadius: radius.sm, borderWidth: 1, borderColor: value === item ? colors.navy : colors.border, backgroundColor: value === item ? colors.tile : colors.white }}><Text style={{ color: colors.navy, fontWeight: '700', fontSize: 12 }}>{item}</Text></Pressable>)}</View></View>;
}

function Choice({ label, value, choices, onChange }: { label: string; value: string; choices: string[]; onChange: (value: string) => void }) {
  return <View style={{ marginBottom: 14 }}><Text style={{ color: colors.text, fontSize: 12, fontWeight: '700', marginBottom: 6 }}>{label}</Text><View style={{ flexDirection: 'row', gap: 7, flexWrap: 'wrap' }}>{choices.map(item => <Pressable key={item} accessibilityRole="button" accessibilityState={{ selected: value === item }} onPress={() => onChange(item)} style={{ paddingHorizontal: 11, paddingVertical: 8, borderRadius: radius.sm, borderWidth: 1, borderColor: value === item ? colors.navy : colors.border, backgroundColor: value === item ? colors.tile : colors.white }}><Text style={{ color: colors.navy, fontSize: 12, fontWeight: '600' }}>{humanize(item)}</Text></Pressable>)}</View></View>;
}
