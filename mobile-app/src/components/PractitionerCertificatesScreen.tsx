import { useCallback, useEffect, useState } from 'react';
import { ActivityIndicator, Pressable, Text, View } from 'react-native';
import { useRouter } from 'expo-router';
import { ApiRecord, useSession } from '../context/SessionContext';
import { Brand, Card, Heading, Notice, Page, SectionTitle, colors } from './ui';
import { AppIcon } from './icons';

function records(value: unknown): ApiRecord[] {
  return Array.isArray(value) ? value.filter((item): item is ApiRecord => !!item && typeof item === 'object') : [];
}

function issuedDate(value: unknown): string {
  if (typeof value !== 'string' || !value) return 'Date not provided';
  const date = new Date(value);
  return Number.isNaN(date.getTime()) ? value : date.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
}

export default function PractitionerCertificatesScreen() {
  const router = useRouter();
  const session = useSession();
  const [courseCertificates, setCourseCertificates] = useState<ApiRecord[]>([]);
  const [validationCertificates, setValidationCertificates] = useState<ApiRecord[]>([]);
  const [courseTitles, setCourseTitles] = useState<Record<string, string>>({});
  const [busy, setBusy] = useState(true);
  const [error, setError] = useState('');

  const load = useCallback(async () => {
    setBusy(true); setError('');
    try {
      if (session.demo) {
        const [certificates, courses] = await Promise.all([session.rowsFor('certificates'), session.rowsFor('courses')]);
        setCourseCertificates(certificates);
        setValidationCertificates([]);
        setCourseTitles(Object.fromEntries(courses.map(course => [String(course.id), String(course.title ?? 'Course certificate')])));
        return;
      }
      const [certificateResult, courseResult] = await Promise.all([
        session.request<Record<string, unknown>>('/practitioner/certificates'),
        session.request<Record<string, unknown>>('/practitioner/courses'),
      ]);
      const courses = records(courseResult.courses);
      setCourseTitles(Object.fromEntries(courses.map(course => [String(course.id), String(course.title ?? 'Course certificate')])));
      setCourseCertificates(records(certificateResult.certificates));
      setValidationCertificates(records(certificateResult.validation_certificates));
    } catch (e) { setError(e instanceof Error ? e.message : 'Certificates could not be loaded.'); }
    finally { setBusy(false); }
  }, [session]);

  useEffect(() => { void Promise.resolve().then(load); }, [load]);
  return <Page>
    <View style={{ flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' }}><Brand compact /><Pressable accessibilityRole="button" onPress={() => router.push('/profile')}><Text style={{ color: colors.blue, fontWeight: '700', padding: 8 }}>Profile</Text></Pressable></View>
    <Pressable accessibilityRole="button" onPress={() => router.back()} style={{ minHeight: 44, flexDirection: 'row', alignItems: 'center', gap: 4 }}><AppIcon name="chevronLeft" size={18} /><Text style={{ color: colors.blue, fontSize: 14, fontWeight: '700' }}>Back to learning</Text></Pressable>
    {session.demo && <Notice>DEMO MODE · Sample certificate details are fictional and stay in this app.</Notice>}
    <Heading eyebrow="Learning record" title="Certificates" subtitle="Review course awards and professional validation certificates associated with your account." />
    {!!error && <Notice tone="error">{error}</Notice>}
    {busy && <ActivityIndicator color={colors.blue} style={{ marginVertical: 24 }} />}
    {!busy && <>
      <SectionTitle title="Course certificates" trailing={<Pressable accessibilityRole="button" onPress={() => void load()}><Text style={{ color: colors.blue, fontWeight: '700' }}>Refresh</Text></Pressable>} />
      {courseCertificates.length === 0 && <Card><Text style={{ color: colors.text, fontWeight: '700' }}>No course certificates yet</Text><Text style={{ color: colors.muted, fontSize: 12, lineHeight: 18, marginTop: 5 }}>Complete all study lessons in an eligible course. Your certificate will appear here when it is issued.</Text><Pressable accessibilityRole="button" onPress={() => router.replace('/feature/courses' as never)} style={{ minHeight: 44, flexDirection: 'row', alignItems: 'center', gap: 4 }}><Text style={{ color: colors.blue, fontWeight: '800', marginTop: 11 }}>Browse courses</Text><AppIcon name="chevronRight" size={18} /></Pressable></Card>}
      {courseCertificates.map((certificate, index) => {
        const title = String(certificate.title ?? courseTitles[String(certificate.course_id)] ?? 'Course completion certificate');
        return <Card key={String(certificate.id ?? index)} style={{ borderLeftWidth: 4, borderLeftColor: colors.success }}>
          <Text style={{ color: colors.success, fontSize: 12, fontWeight: '800', letterSpacing: 0.7 }}>COURSE COMPLETED</Text>
          <Text style={{ color: colors.navy, fontSize: 15, lineHeight: 21, fontWeight: '800', marginTop: 6 }}>{title}</Text>
          <Text style={{ color: colors.muted, fontSize: 12, marginTop: 7 }}>Issued {issuedDate(certificate.issued_at)}</Text>
          {!!(certificate.certificate_number ?? certificate.reference) && <Text selectable style={{ color: colors.text, fontSize: 12, marginTop: 5 }}>Certificate no. {String(certificate.certificate_number ?? certificate.reference)}</Text>}
          {!!certificate.tier && <Text style={{ color: colors.text, fontSize: 12, marginTop: 5 }}>Award level: {String(certificate.tier)}</Text>}
        </Card>;
      })}
      <SectionTitle title="Professional validation" />
      {validationCertificates.length === 0 && <Card><Text style={{ color: colors.muted, fontSize: 12, lineHeight: 18 }}>No professional validation certificate is linked to this account.</Text></Card>}
      {validationCertificates.map((certificate, index) => <Card key={String(certificate.id ?? index)} style={{ borderLeftWidth: 4, borderLeftColor: colors.blue }}>
        <Text style={{ color: colors.blue, fontSize: 12, fontWeight: '800', letterSpacing: 0.7 }}>PROFESSIONAL VALIDATION</Text>
        <Text style={{ color: colors.navy, fontSize: 15, fontWeight: '800', marginTop: 6 }}>{String(certificate.certificate_number ?? 'Validation certificate')}</Text>
        <Text style={{ color: colors.muted, fontSize: 12, marginTop: 6 }}>Issued {issuedDate(certificate.issued_at)}</Text>
        {!!certificate.tier && <Text style={{ color: colors.text, fontSize: 12, marginTop: 4 }}>Level: {String(certificate.tier)}</Text>}
        {certificate.score !== null && certificate.score !== undefined && <Text style={{ color: colors.text, fontSize: 12, marginTop: 4 }}>Score: {String(certificate.score)}%</Text>}
      </Card>)}
    </>}
  </Page>;
}
