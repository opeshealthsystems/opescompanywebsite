import { useCallback, useEffect, useMemo, useState } from 'react';
import { ActivityIndicator, Linking, Pressable, Text, View } from 'react-native';
import { useLocalSearchParams, useRouter } from 'expo-router';
import { ApiRecord, featureCatalog, useSession } from '../context/SessionContext';
import { ActionButton, Card, Heading, Notice, Page, SectionTitle, colors } from './ui';
import { AppIcon } from './icons';
import { AppHeader } from './AppHeader';

type Course = ApiRecord & { slug: string; title: string; lessons?: ApiRecord[] };
type CourseDetail = { course: Course; enrollment: ApiRecord | null; completed_lesson_ids: (number | string)[] };

export default function StudyLessonScreen() {
  const { slug, id } = useLocalSearchParams<{ slug: string; id: string }>();
  const router = useRouter();
  const session = useSession();
  const [detail, setDetail] = useState<CourseDetail | null>(null);
  const [busy, setBusy] = useState(true);
  const [acting, setActing] = useState(false);
  const [error, setError] = useState('');
  const [message, setMessage] = useState('');
  const mayLearn = session.isAllowed(featureCatalog.courses, true);

  const load = useCallback(async () => {
    setBusy(true); setError('');
    try {
      if (session.demo) {
        const courses = await session.rowsFor('courses');
        const course = courses.find(row => String(row.slug) === String(slug)) as Course | undefined;
        if (!course) throw new Error('This sample course could not be found.');
        setDetail({ course, enrollment: { status: course.status }, completed_lesson_ids: Array.isArray(course.completed_lesson_ids) ? course.completed_lesson_ids as (number | string)[] : [] });
      } else {
        setDetail(await session.request<CourseDetail>(`/practitioner/courses/${encodeURIComponent(String(slug))}`));
      }
    } catch (e) { setError(e instanceof Error ? e.message : 'This study lesson could not be loaded.'); }
    finally { setBusy(false); }
  }, [session, slug]);

  useEffect(() => { void Promise.resolve().then(load); }, [load]);

  const lessons = detail?.course.lessons ?? [];
  const index = lessons.findIndex(lesson => String(lesson.id) === String(id));
  const lesson = index >= 0 ? lessons[index] : null;
  const completedIds = useMemo(() => new Set((detail?.completed_lesson_ids ?? []).map(String)), [detail?.completed_lesson_ids]);
  const completed = !!lesson && completedIds.has(String(lesson.id));
  const mediaUrl = typeof lesson?.video_url === 'string' ? lesson.video_url : '';
  const safeMediaUrl = /^https:\/\//i.test(mediaUrl) ? mediaUrl : '';
  const content = typeof lesson?.content === 'string' ? lesson.content : '';
  const paragraphs = content.split(/\n\s*\n/).map(part => part.trim()).filter(Boolean);

  const markComplete = async () => {
    if (!lesson || !detail) return;
    setActing(true); setError(''); setMessage('');
    try {
      if (session.demo) {
        session.mutateDemo('courses', 'complete-lesson', { lesson_id: lesson.id }, String(slug));
        setDetail(current => current ? { ...current, completed_lesson_ids: [...new Set([...current.completed_lesson_ids.map(String), String(lesson.id)])] } : current);
        setMessage('Lesson complete. Your sample progress is saved.');
      } else {
        await session.request(`/practitioner/courses/${encodeURIComponent(String(slug))}/lessons/${encodeURIComponent(String(lesson.id))}/done`, 'POST', {});
        setMessage('Lesson complete. Your course progress has been updated.');
        await load();
      }
    } catch (e) { setError(e instanceof Error ? e.message : 'Lesson progress could not be saved.'); }
    finally { setActing(false); }
  };

  const goToLesson = (target: ApiRecord) => router.replace({ pathname: '/course/[slug]/lesson/[id]', params: { slug: String(slug), id: String(target.id) } } as never);

  return <Page fixedHeader={<AppHeader />}>
    <Pressable accessibilityRole="button" onPress={() => router.replace({ pathname: '/course/[slug]', params: { slug: String(slug) } } as never)} style={{ minHeight: 44, flexDirection: 'row', alignItems: 'center', gap: 4 }}><AppIcon name="chevronLeft" size={18} /><Text style={{ color: colors.blue, fontSize: 14, fontWeight: '700' }}>Back to course</Text></Pressable>
    {session.demo && <Notice>DEMO MODE · Completing this lesson changes only the sample account’s progress.</Notice>}
    {busy && <ActivityIndicator color={colors.blue} style={{ marginVertical: 28 }} />}
    {!!error && <Notice tone="error">{error}</Notice>}
    {!!message && <Notice tone="success">{message}</Notice>}
    {!busy && lesson && <>
      <Heading eyebrow={`Study lesson ${index + 1} of ${lessons.length}`} title={String(lesson.title ?? 'Study lesson')} subtitle={`${String(detail?.course.title ?? 'Course')}${lesson.duration_minutes ? ` · ${lesson.duration_minutes} minutes` : ''}`} />
      {safeMediaUrl && <ActionButton label="Open lesson video" secondary onPress={() => void Linking.openURL(safeMediaUrl).catch(() => setError('This video link could not be opened.'))} />}
      <SectionTitle title="Study notes" />
      {paragraphs.length ? paragraphs.map((paragraph, i) => <Text key={i} selectable style={{ color: colors.text, fontSize: 15, lineHeight: 25, marginBottom: 16 }}>{paragraph}</Text>) : <Card><Text style={{ color: colors.text, fontWeight: '700' }}>Lesson notes are not available yet</Text><Text style={{ color: colors.muted, fontSize: 12, lineHeight: 18, marginTop: 5 }}>Ask your course administrator to add the lesson content.</Text></Card>}
      <Card style={{ backgroundColor: '#F1F6FA' }}><Text style={{ color: colors.navy, fontSize: 13, fontWeight: '800' }}>Learning check</Text><Text style={{ color: colors.muted, fontSize: 12, lineHeight: 18, marginTop: 5 }}>Review the notes and video, then mark the lesson complete. Any formal course assessment will be presented separately when the course provides one.</Text></Card>
      {completed ? <Notice tone="success">This lesson is complete and included in your course progress.</Notice> : mayLearn ? <ActionButton label="Mark lesson complete" onPress={() => void markComplete()} busy={acting} /> : <Notice>Ask your administrator for practitioner:write access to save lesson progress.</Notice>}
      <View style={{ flexDirection: 'row', gap: 10, marginTop: 8 }}>
        <View style={{ flex: 1 }}>{index > 0 && <ActionButton label="Previous lesson" secondary onPress={() => goToLesson(lessons[index - 1])} />}</View>
        <View style={{ flex: 1 }}>{index >= 0 && index < lessons.length - 1 && <ActionButton label="Next lesson" secondary onPress={() => goToLesson(lessons[index + 1])} />}</View>
      </View>
      {completed && index === lessons.length - 1 && <ActionButton label="View course progress & certificate" onPress={() => router.replace({ pathname: '/course/[slug]', params: { slug: String(slug) } } as never)} />}
    </>}
    {!busy && !lesson && !error && <Notice tone="error">This lesson is not part of the selected course.</Notice>}
  </Page>;
}
