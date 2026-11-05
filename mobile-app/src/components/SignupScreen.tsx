import { useRef, useState } from 'react';
import { Pressable, Text, View } from 'react-native';
import { useRouter } from 'expo-router';
import { useSession } from '../context/SessionContext';
import { ActionButton, Brand, Card, Field, Heading, Notice, Page, colors, radius } from './ui';
import { getLang, t } from '../i18n';

type AccountType = 'individual' | 'facility' | 'practitioner';
type SignupValues = {
  name: string; email: string; phone: string; password: string; password_confirmation: string;
  country: string; city: string; facility_name: string; facility_type: string;
  profession: string; specialty: string; workplace_name: string; workplace_country: string;
};

const professions = [
  ['doctor', 'Doctor / Physician'], ['nurse', 'Nurse'], ['radiologist', 'Radiologist'],
  ['cardiologist', 'Cardiologist'], ['pharmacist', 'Pharmacist'], ['lab_tech', 'Laboratory Technician'],
  ['health_admin', 'Health Administrator'], ['researcher', 'Researcher'], ['other', 'Other'],
] as const;

export default function SignupScreen() {
  const router = useRouter();
  const session = useSession();
  const [accountType, setAccountType] = useState<AccountType>('individual');
  const [values, setValues] = useState<SignupValues>({
    name: '', email: '', phone: '', password: '', password_confirmation: '', country: 'Cameroon', city: '',
    facility_name: '', facility_type: 'clinic', profession: 'doctor', specialty: '', workplace_name: '', workplace_country: 'Cameroon',
  });
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState('');
  const idempotency = useRef({ fingerprint: '', key: '' });

  const set = (key: keyof SignupValues, value: string) => setValues(current => ({ ...current, [key]: value }));
  const validate = () => {
    if (!values.name.trim() || !values.email.trim()) return t('Enter your name and email address.');
    if (values.password.length < 8) return t('Choose a password with at least 8 characters.');
    if (values.password !== values.password_confirmation) return t('The passwords do not match.');
    if (accountType === 'facility' && (!values.facility_name.trim() || !values.facility_type.trim() || !values.country.trim())) return t('Enter your facility name, facility type and country.');
    if (accountType === 'individual' && !values.country.trim()) return t('Enter your country.');
    return '';
  };

  const createAccount = async () => {
    const validation = validate();
    if (validation) { setError(validation); return; }
    setBusy(true); setError('');
    try {
      const payload: Record<string, string> = {
        account_type: accountType, name: values.name.trim(), email: values.email.trim(),
        password: values.password, password_confirmation: values.password_confirmation,
        phone: values.phone.trim(), locale: getLang(),
      };
      if (accountType === 'facility') Object.assign(payload, {
        facility_name: values.facility_name.trim(), facility_type: values.facility_type.trim(),
        country: values.country.trim(), city: values.city.trim(),
      });
      if (accountType === 'individual') Object.assign(payload, { country: values.country.trim(), city: values.city.trim() });
      if (accountType === 'practitioner') Object.assign(payload, {
        profession: values.profession, specialty: values.specialty.trim(), workplace_name: values.workplace_name.trim(),
        workplace_country: values.workplace_country.trim(),
      });
      const fingerprint = JSON.stringify(payload);
      if (idempotency.current.fingerprint !== fingerprint) idempotency.current = {
        fingerprint,
        key: `signup-${Date.now()}-${Math.random().toString(36).slice(2)}`,
      };
      await session.request('/public/accounts', 'POST', payload, idempotency.current.key);
      router.replace({ pathname: '/', params: { registered: '1' } });
    } catch (e) {
      setError(e instanceof Error ? e.message : t('Your account could not be created. Please try again.'));
    } finally { setBusy(false); }
  };

  return <Page>
    <View style={{ flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' }}>
      <Brand compact />
      <Pressable accessibilityRole="button" onPress={() => router.replace('/')}><Text style={{ color: colors.blue, fontWeight: '700', padding: 8 }}>{t('Sign in')}</Text></Pressable>
    </View>
    <Heading eyebrow={t('Create an account')} title={t('Join OPES Health')} subtitle={t('Create your account in the app. Your account type and permissions are assigned by OPES based on this registration.')} />
    <Text style={{ color: colors.text, fontSize: 13, fontWeight: '700', marginBottom: 8 }}>{t('Account type')}</Text>
    <View style={{ flexDirection: 'row', flexWrap: 'wrap', gap: 8, marginBottom: 14 }}>
      {([['individual', t('Individual')], ['facility', t('Health facility')], ['practitioner', t('Practitioner')]] as const).map(([value, label]) => {
        const selected = accountType === value;
        return <Pressable key={value} accessibilityRole="button" accessibilityState={{ selected }} onPress={() => setAccountType(value)} style={{ borderRadius: radius.sm, paddingHorizontal: 12, paddingVertical: 10, borderWidth: 1, borderColor: selected ? colors.navy : colors.border, backgroundColor: selected ? colors.navy : colors.white }}>
          <Text style={{ color: selected ? colors.white : colors.text, fontSize: 12, fontWeight: '700' }}>{label}</Text>
        </Pressable>;
      })}
    </View>
    <Field label={t('Full name')} value={values.name} onChangeText={value => set('name', value)} autoCapitalize="words" />
    <Field label={t('Email address')} value={values.email} onChangeText={value => set('email', value)} keyboardType="email-address" autoCapitalize="none" />
    <Field label={t('Phone (optional)')} value={values.phone} onChangeText={value => set('phone', value)} keyboardType="phone-pad" />
    {(accountType === 'individual' || accountType === 'facility') && <>
      {accountType === 'facility' && <>
        <Field label={t('Facility name')} value={values.facility_name} onChangeText={value => set('facility_name', value)} />
        <Field label={t('Facility type')} value={values.facility_type} onChangeText={value => set('facility_type', value)} placeholder={t('Clinic, hospital, laboratory…')} />
      </>}
      <Field label={t('Country')} value={values.country} onChangeText={value => set('country', value)} />
      <Field label={t('City (optional)')} value={values.city} onChangeText={value => set('city', value)} />
    </>}
    {accountType === 'practitioner' && <>
      <Text style={{ color: colors.text, fontSize: 12, fontWeight: '700', marginBottom: 7 }}>{t('Profession')}</Text>
      <View style={{ flexDirection: 'row', flexWrap: 'wrap', gap: 7, marginBottom: 14 }}>
        {professions.map(([value, label]) => {
          const selected = values.profession === value;
          return <Pressable key={value} accessibilityRole="button" accessibilityState={{ selected }} onPress={() => set('profession', value)} style={{ borderRadius: radius.sm, paddingHorizontal: 10, paddingVertical: 8, borderWidth: 1, borderColor: selected ? colors.navy : colors.border, backgroundColor: selected ? colors.tile : colors.white }}>
            <Text style={{ color: colors.navy, fontSize: 12, fontWeight: selected ? '800' : '600' }}>{t(label)}</Text>
          </Pressable>;
        })}
      </View>
      <Field label={t('Specialty (optional)')} value={values.specialty} onChangeText={value => set('specialty', value)} />
      <Field label={t('Workplace (optional)')} value={values.workplace_name} onChangeText={value => set('workplace_name', value)} />
      <Field label={t('Workplace country')} value={values.workplace_country} onChangeText={value => set('workplace_country', value)} />
    </>}
    <Field label={t('Password (minimum 8 characters)')} value={values.password} onChangeText={value => set('password', value)} secureTextEntry autoCapitalize="none" />
    <Field label={t('Confirm password')} value={values.password_confirmation} onChangeText={value => set('password_confirmation', value)} secureTextEntry autoCapitalize="none" />
    {accountType === 'practitioner' && <Card style={{ backgroundColor: colors.surfaceAlt }}><Text style={{ color: colors.muted, fontSize: 12, lineHeight: 18 }}>{t('Practitioner accounts can access learning, study progress and certificates after you sign in. Public registration does not grant staff or administrator permissions.')}</Text></Card>}
    {!!error && <Notice tone="error">{error}</Notice>}
    <ActionButton label={busy ? t('Creating account…') : t('Create account')} onPress={() => void createAccount()} busy={busy} />
    <Text style={{ color: colors.muted, fontSize: 12, lineHeight: 17, textAlign: 'center', marginTop: 4 }}>Already have an account? Use secure sign-in. Your role and app access come from your OPES account.</Text>
  </Page>;
}
