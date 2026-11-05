import { useCallback, useEffect, useState } from 'react';
import { ActivityIndicator, Pressable, Text, View } from 'react-native';
import { useLocalSearchParams, useRouter } from 'expo-router';
import { ApiRecord, featureCatalog, useSession } from '../context/SessionContext';
import { ActionButton, Card, Heading, Notice, Page, SectionTitle, colors, radius } from './ui';
import { AppIcon } from './icons';
import { AppHeader } from './AppHeader';
import { t } from '../i18n';

type Course = ApiRecord & { id: number | string; slug: string; title: string; lessons?: ApiRecord[] };
type CourseDetail = { course: Course; enrollment: ApiRecord | null; completed_lesson_ids: (number | string)[] };
type CourseAssessment = ApiRecord & { id: number | string; title: string; attempts_used?: number; attempts_remaining?: number | null; latest_attempt?: ApiRecord | null };

function Progress({ value }: { value: number }) {
  const width = `${Math.max(0, Math.min(100, value))}%` as `${number}%`;
  return <View accessibilityRole="progressbar" accessibilityValue={{ min: 0, max: 100, now: value }} style={{ height: 8, borderRadius: radius.md, backgroundColor: colors.divider, overflow: 'hidden', marginTop: 9 }}><View style={{ width, height: '100%', backgroundColor: colors.blue }} /></View>;
}

export default function CourseDetailScreen() {
  const { slug } = useLocalSearchParams<{ slug: string }>();
  const router = useRouter();
  const session = useSession();
  const [detail, setDetail] = useState<CourseDetail | null>(null);
  const [assessments, setAssessments] = useState<CourseAssessment[]>([]);
  const [busy, setBusy] = useState(true);
  const [acting, setActing] = useState(false);
  const [error, setError] = useState('');
  const [message, setMessage] = useState('');
  const mayLearn = session.isAllowed(featureCatalog.courses, true);

  const load = useCallback(async () => {
    if (!slug) return;
    setBusy(true); setError('');
    try {
      if (session.demo) {
        const courses = await session.rowsFor('courses');
        const course = courses.find(row => String(row.slug) === String(slug)) as Course | undefined;
        if (!course) throw new Error(t('This sample course could not be found.'));
        const completed = Array.isArray(course.completed_lesson_ids) ? course.completed_lesson_ids as (number | string)[] : [];
        setDetail({ course, enrollment: ['enrolled', 'in_progress', 'completed'].includes(String(course.status)) ? { status: course.status } : null, completed_lesson_ids: completed });
        setAssessments([]);
      } else {
        const response = await session.request<CourseDetail>(`/practitioner/courses/${encodeURIComponent(String(slug))}`);
        setDetail(response);
        if (response.enrollment && response.enrollment.status !== 'dropped') {
          try {
            const assessmentResponse = await session.request<{ assessments?: CourseAssessment[] }>(`/practitioner/courses/${encodeURIComponent(String(slug))}/assessments`);
            setAssessments(Array.isArray(assessmentResponse.assessments) ? assessmentResponse.assessments : []);
          } catch { setAssessments([]); }
        } else setAssessments([]);
      }
    } catch (e) { setError(e instanceof Error ? e.message : t('Course details could not be loaded.')); }
    finally { setBusy(false); }
  }, [session, slug]);

  useEffect(() => { void Promise.resolve().then(load); }, [load]);

  const enroll = async () => {
    if (!detail) return;
    setActing(true); setError(''); setMessage('');
    try {
      if (session.demo) session.mutateDemo('courses', 'enroll', { status: 'enrolled' }, String(slug));
      else await session.request(`/practitioner/courses/${encodeURIComponent(String(slug))}/enroll`, 'POST', {});
      setMessage(t('You are enrolled. Your lessons and progress are ready.'));
      await load();
    } catch (e) { setError(e instanceof Error ? e.message : t('Enrollment could not be saved.')); }
    finally { setActing(false); }
  };

  const course = detail?.course;
  const lessons = Array.isArray(course?.lessons) ? course.lessons : [];
  const done = new Set((detail?.completed_lesson_ids ?? []).map(String));
  const progress = lessons.length ? Math.round(done.size / lessons.length * 100) : 0;
  const enrolled = !!detail?.enrollment;
  const status = String(detail?.enrollment?.status ?? 'available');

  return <Page fixedHeader={<AppHeader />}>
    <Pressable accessibilityRole="button" onPress={() => router.back()} style={{ minHeight: 44, flexDirection: 'row', alignItems: 'center', gap: 4 }}><AppIcon name="chevronLeft" size={18} /><Text style={{ color: colors.blue, fontSize: 14, fontWeight: '700' }}>{t('Back to courses')}</Text></Pressable>
    {session.demo && <Notice>{t('DEMO MODE · Progress is sample data stored only in this app.')}</Notice>}
    {busy && <ActivityIndicator color={colors.blue} style={{ marginVertical: 28 }} />}
    {!!error && <Notice tone="error">{error}</Notice>}
    {!!message && <Notice tone="success">{message}</Notice>}
    {!busy && course && <>
      <Heading eyebrow={t('Self-paced course')} title={String(course.title)} subtitle={String(course.description ?? t('Study the lessons below, then mark each one complete to save your progress.'))} />
      <Card>
        <View style={{ flexDirection: 'row', justifyContent: 'space-between', gap: 8 }}><Text style={{ color: colors.navy, fontSize: 14, fontWeight: '800' }}>{t('Course progress')}</Text><Text style={{ color: status === 'completed' ? colors.success : colors.blue, fontSize: 12, fontWeight: '800', textTransform: 'uppercase' }}>{status.replaceAll('_', ' ')}</Text></View>
        <Progress value={enrolled ? progress : 0} />
        <Text style={{ color: colors.muted, fontSize: 12, lineHeight: 18, marginTop: 7 }}>{enrolled ? t('{done} of {total} lessons complete · {progress}%', { done: done.size, total: lessons.length, progress }) : t('{count} study lessons · {duration}', { count: lessons.length, duration: course.duration_hours ? t('{hours} hour estimated', { hours: String(course.duration_hours) }) : t('Self-paced') })}</Text>
      </Card>
      {!enrolled && mayLearn && <ActionButton label={t('Enroll and start learning')} onPress={() => void enroll()} busy={acting} />}
      {!enrolled && !mayLearn && <Notice>{t('Ask your administrator for practitioner:write access to enroll in courses.')}</Notice>}
      <SectionTitle title={t('Study lessons')} />
      <Text style={{ color: colors.muted, fontSize: 12, lineHeight: 18, marginBottom: 12 }}>{t('Read or watch each lesson in order. Course assessments are separate and are shown only when provided for the course.')}</Text>
      {lessons.length === 0 && <Card><Text style={{ color: colors.text, fontWeight: '700' }}>{t('Lessons are being prepared')}</Text><Text style={{ color: colors.muted, fontSize: 12, lineHeight: 18, marginTop: 5 }}>{t('This course has no published study lessons yet. Refresh later or choose another course.')}</Text></Card>}
      {lessons.map((lesson, index) => {
        const isDone = done.has(String(lesson.id));
        const locked = !enrolled;
        return <Pressable key={String(lesson.id)} accessibilityRole="button" accessibilityState={{ disabled: locked }} disabled={locked} onPress={() => router.push({ pathname: '/course/[slug]/lesson/[id]', params: { slug: String(slug), id: String(lesson.id) } } as never)}>
          <Card style={{ flexDirection: 'row', alignItems: 'center', gap: 12, padding: 14, opacity: locked ? 0.62 : 1 }}>
            <View style={{ width: 38, height: 38, borderRadius: 19, alignItems: 'center', justifyContent: 'center', backgroundColor: isDone ? colors.successBg : colors.pale }}>{isDone ? <AppIcon name="check" size={18} color={colors.success} /> : <Text style={{ color: colors.blue, fontSize: 14, fontWeight: '800' }}>{String(index + 1)}</Text>}</View>
            <View style={{ flex: 1 }}><Text style={{ color: colors.navy, fontSize: 13, lineHeight: 18, fontWeight: '700' }}>{String(lesson.title ?? t('Lesson {number}', { number: index + 1 }))}</Text><Text style={{ color: colors.muted, fontSize: 12, marginTop: 4 }}>{[lesson.duration_minutes ? t('{count} min', { count: String(lesson.duration_minutes) }) : null, isDone ? t('Completed') : t('Study lesson')].filter(Boolean).join(' · ')}</Text></View>
            <AppIcon name={locked ? 'lock' : 'chevronRight'} size={19} color={locked ? colors.muted : colors.blue} />
          </Card>
        </Pressable>;
      })}
      <SectionTitle title={t('Exams & assessments')} />
      {session.demo && <Notice>{t('Demo course records do not include assessments. Live practitioner accounts can take exams published for their enrolled courses.')}</Notice>}
      {!session.demo && enrolled && assessments.length === 0 && <Card><Text style={{ color: colors.text, fontWeight: '700' }}>{t('No exams are published for this course')}</Text><Text style={{ color: colors.muted, fontSize: 12, lineHeight: 18, marginTop: 5 }}>{t('When the learning team publishes an assessment, you can take it here and review your result.')}</Text></Card>}
      {!session.demo && !enrolled && <Card><Text style={{ color: colors.muted, fontSize: 12, lineHeight: 18 }}>{t('Enroll in this course to see its published exams.')}</Text></Card>}
      {assessments.map(assessment => {
        const latest = assessment.latest_attempt;
        const statusText = latest ? String(latest.status) === 'in_progress' ? t('Attempt in progress') : String(latest.passed) === 'true' ? t('Passed · {score}%', { score: String(latest.score ?? 0) }) : t('Last score · {score}%', { score: String(latest.score ?? 0) }) : t('{count} attempts available', { count: String(assessment.attempts_remaining ?? t('Unlimited')) });
        return <Card key={String(assessment.id)}>
          <Text style={{ color: colors.navy, fontSize: 14, fontWeight: '800' }}>{assessment.title}</Text>
          <Text style={{ color: colors.muted, fontSize: 12, lineHeight: 18, marginTop: 5 }}>{[assessment.duration_minutes ? t('{count} min', { count: String(assessment.duration_minutes) }) : null, t('Pass mark {mark}%', { mark: String(assessment.pass_mark) }), statusText].filter(Boolean).join(' · ')}</Text>
          <ActionButton label={latest && String(latest.status) === 'submitted' ? t('Review result') : latest && String(latest.status) === 'in_progress' ? t('Resume exam') : t('Start exam')} secondary onPress={() => router.push({ pathname: '/assessment/[id]', params: { id: String(assessment.id) } } as never)} />
        </Card>;
      })}
      {status === 'completed' && <Card><Text style={{ color: colors.success, fontSize: 14, fontWeight: '800' }}>{t('Course completed')}</Text><Text style={{ color: colors.text, fontSize: 12, lineHeight: 18, marginTop: 5 }}>{t('Your completion is recorded. Open Certificates to see your award.')}</Text><Pressable accessibilityRole="button" onPress={() => router.push('/feature/certificates' as never)} style={{ minHeight: 44, flexDirection: 'row', alignItems: 'center', gap: 5 }}><Text style={{ color: colors.blue, fontSize: 14, fontWeight: '800', marginTop: 10 }}>{t('View certificates')}</Text><AppIcon name="chevronRight" size={18} /></Pressable></Card>}
    </>}
    <Pressable accessibilityRole="button" onPress={() => router.replace('/workspace')}><Text style={{ color: colors.muted, fontSize: 12, textAlign: 'center', paddingVertical: 12 }}>{t('Back to your dashboard')}</Text></Pressable>
  </Page>;
}
