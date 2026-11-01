import { useCallback, useEffect, useState } from 'react';
import { ActivityIndicator, Pressable, Text, View } from 'react-native';
import { useRouter } from 'expo-router';
import { ApiRecord, useSession } from '../context/SessionContext';
import { Brand, Card, Heading, Notice, Page, SectionTitle, colors } from './ui';
import { AppIcon } from './icons';

type CourseCard = ApiRecord & { slug: string; title: string; status: string; progress_percent: number; total_lessons?: number };
type Enrollment = ApiRecord & { course_id: number | string; status: string; progress_percent?: number; total_lessons?: number };

function records(value: unknown): ApiRecord[] {
  return Array.isArray(value) ? value.filter((item): item is ApiRecord => !!item && typeof item === 'object') : [];
}

function ProgressBar({ value }: { value: number }) {
  const progress = Math.max(0, Math.min(100, value));
  return <View accessibilityRole="progressbar" accessibilityValue={{ min: 0, max: 100, now: progress }} style={{ height: 7, borderRadius: 8, backgroundColor: '#E5ECF2', overflow: 'hidden', marginTop: 10 }}>
    <View style={{ width: `${progress}%`, height: '100%', backgroundColor: colors.blue, borderRadius: 8 }} />
  </View>;
}

export default function LearningCatalogScreen() {
  const router = useRouter();
  const session = useSession();
  const [courses, setCourses] = useState<CourseCard[]>([]);
  const [filter, setFilter] = useState<'all' | 'in_progress' | 'completed'>('all');
  const [busy, setBusy] = useState(true);
  const [error, setError] = useState('');

  const load = useCallback(async () => {
    setBusy(true); setError('');
    try {
      if (session.demo) {
        const samples = await session.rowsFor('courses');
        setCourses(samples as CourseCard[]);
        return;
      }
      const result = await session.request<Record<string, unknown>>('/practitioner/courses');
      const enrollments = records(result.my_enrollments) as Enrollment[];
      const mapped = records(result.courses).map(course => {
        const enrollment = enrollments.find(item => String(item.course_id) === String(course.id));
        return {
          ...course,
          slug: String(course.slug ?? ''),
          title: String(course.title ?? 'Untitled course'),
          status: String(enrollment?.status ?? 'available'),
          progress_percent: Number(enrollment?.progress_percent ?? 0),
          total_lessons: Number(enrollment?.total_lessons ?? 0),
          completed_lesson_ids: Array.isArray(enrollment?.completed_lesson_ids) ? enrollment.completed_lesson_ids : [],
          certificate_id: enrollment?.certificate_id ?? null,
        };
      });
      setCourses(mapped);
    } catch (e) { setError(e instanceof Error ? e.message : 'Courses could not be loaded.'); }
    finally { setBusy(false); }
  }, [session]);

  useEffect(() => { void Promise.resolve().then(load); }, [load]);

  const visible = courses.filter(course => filter === 'all' || course.status === filter || (filter === 'in_progress' && course.status === 'enrolled'));
  const activeCount = courses.filter(course => ['enrolled', 'in_progress'].includes(course.status)).length;
  const completedCount = courses.filter(course => course.status === 'completed').length;

  return <Page>
    <View style={{ flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' }}>
      <Brand compact />
      <Pressable accessibilityRole="button" accessibilityLabel="Open profile" onPress={() => router.push('/profile')}><Text style={{ color: colors.blue, fontWeight: '700', padding: 8 }}>Profile</Text></Pressable>
    </View>
    <Pressable accessibilityRole="button" onPress={() => router.back()} style={{ minHeight: 44, flexDirection: 'row', alignItems: 'center', gap: 4 }}><AppIcon name="chevronLeft" size={18} /><Text style={{ color: colors.blue, fontSize: 14, fontWeight: '700' }}>Back to workspace</Text></Pressable>
    {session.demo && <Notice>DEMO MODE · Study progress and course actions are saved locally in this sample workspace.</Notice>}
    <Heading eyebrow="Learning" title="Courses & study" subtitle="Study practical lessons at your pace. Mark each lesson complete to keep your progress and earn course certificates." />
    <Card>
      <Text style={{ color: colors.navy, fontSize: 15, fontWeight: '800' }}>Your learning</Text>
      <View style={{ flexDirection: 'row', gap: 12, marginTop: 12 }}>
        <View style={{ flex: 1, padding: 12, backgroundColor: '#F2F6FA', borderRadius: 8 }}><Text style={{ color: colors.muted, fontSize: 12 }}>IN PROGRESS</Text><Text style={{ color: colors.navy, fontSize: 23, fontWeight: '800', marginTop: 3 }}>{activeCount}</Text></View>
        <View style={{ flex: 1, padding: 12, backgroundColor: '#F2F6FA', borderRadius: 8 }}><Text style={{ color: colors.muted, fontSize: 12 }}>COMPLETED</Text><Text style={{ color: colors.navy, fontSize: 23, fontWeight: '800', marginTop: 3 }}>{completedCount}</Text></View>
      </View>
    </Card>
    <SectionTitle title="Course library" trailing={<Pressable accessibilityRole="button" onPress={() => void load()}><Text style={{ color: colors.blue, fontWeight: '700' }}>Refresh</Text></Pressable>} />
    <View style={{ flexDirection: 'row', flexWrap: 'wrap', gap: 8, marginBottom: 12 }}>
      {(['all', 'in_progress', 'completed'] as const).map(value => {
        const label = value === 'all' ? 'All courses' : value === 'in_progress' ? 'In progress' : 'Completed';
        const selected = filter === value;
        return <Pressable key={value} accessibilityRole="button" accessibilityState={{ selected }} onPress={() => setFilter(value)} style={{ borderRadius: 20, paddingHorizontal: 12, paddingVertical: 8, borderWidth: 1, borderColor: selected ? colors.navy : colors.border, backgroundColor: selected ? colors.navy : colors.white }}><Text style={{ color: selected ? colors.white : colors.text, fontSize: 12, fontWeight: '700' }}>{label}</Text></Pressable>;
      })}
    </View>
    {!!error && <Notice tone="error">{error}</Notice>}
    {busy && <ActivityIndicator color={colors.blue} style={{ marginVertical: 22 }} />}
    {!busy && !error && visible.length === 0 && <Card><Text style={{ color: colors.text, fontWeight: '700' }}>No courses in this view</Text><Text style={{ color: colors.muted, fontSize: 12, lineHeight: 18, marginTop: 5 }}>Choose All courses or refresh to check the course library.</Text></Card>}
    {!busy && visible.map(course => {
      const progress = Number(course.progress_percent ?? 0);
      const status = course.status === 'available' ? 'Available' : course.status.replaceAll('_', ' ');
      const lessons = Array.isArray(course.lessons) ? course.lessons.length : Number(course.total_lessons ?? 0);
      return <Pressable key={String(course.id ?? course.slug)} accessibilityRole="button" onPress={() => router.push({ pathname: '/course/[slug]', params: { slug: course.slug } } as never)}>
        <Card style={{ padding: 16 }}>
          <View style={{ flexDirection: 'row', justifyContent: 'space-between', alignItems: 'flex-start', gap: 8 }}>
            <Text style={{ color: colors.navy, fontSize: 15, lineHeight: 21, fontWeight: '800', flex: 1 }}>{course.title}</Text>
            <Text style={{ color: course.status === 'completed' ? colors.success : colors.blue, fontSize: 12, fontWeight: '800', textTransform: 'uppercase' }}>{status}</Text>
          </View>
          {!!course.description && <Text numberOfLines={3} style={{ color: colors.muted, fontSize: 12, lineHeight: 18, marginTop: 7 }}>{String(course.description)}</Text>}
          <Text style={{ color: colors.muted, fontSize: 12, marginTop: 10 }}>{[course.level, course.duration_hours ? `${course.duration_hours} hr` : null, lessons ? `${lessons} lessons` : null].filter(Boolean).join(' · ') || 'Self-paced learning'}</Text>
          {['enrolled', 'in_progress', 'completed'].includes(course.status) && <><ProgressBar value={progress} /><Text style={{ color: colors.text, fontSize: 12, fontWeight: '700', marginTop: 5 }}>{progress}% complete</Text></>}
          <View style={{ flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', marginTop: 12 }}><Text style={{ color: colors.blue, fontSize: 14, fontWeight: '800' }}>{course.status === 'available' ? 'View course details' : course.status === 'completed' ? 'Review course' : 'Continue studying'}</Text><AppIcon name="chevronRight" size={19} /></View>
        </Card>
      </Pressable>;
    })}
    <Pressable accessibilityRole="button" onPress={() => router.push('/feature/certificates' as never)} style={{ minHeight: 48, flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: 6 }}><Text style={{ color: colors.blue, fontSize: 14, fontWeight: '800' }}>View certificates and awards</Text><AppIcon name="chevronRight" size={18} /></Pressable>
    <Pressable accessibilityRole="button" onPress={() => router.replace('/workspace')}><Text style={{ color: colors.muted, fontSize: 12, textAlign: 'center', paddingVertical: 8 }}>Back to your dashboard</Text></Pressable>
  </Page>;
}
