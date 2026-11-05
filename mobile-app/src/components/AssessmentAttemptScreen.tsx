import { useCallback, useEffect, useState } from 'react';
import { ActivityIndicator, Pressable, Text, View } from 'react-native';
import { useLocalSearchParams, useRouter } from 'expo-router';
import { ApiRecord, useSession } from '../context/SessionContext';
import { ActionButton, Card, Heading, Notice, Page, SectionTitle, colors, radius } from './ui';
import { AppIcon } from './icons';
import { AppHeader } from './AppHeader';
import { t } from '../i18n';

type Choice = { id: string; label: string };
type Question = { id: string; prompt: string; points: number; choices: Choice[] };
type Assessment = { id: number | string; title: string; instructions?: string | null; pass_mark: number; attempt_limit: number | null; duration_minutes: number | null; questions: Question[] };
type Attempt = ApiRecord & { id: number | string; status: string; score?: number | string | null; passed?: boolean | null; started_at?: string; submitted_at?: string | null };
type Review = { question_id: string; prompt: string; selected_choice_label?: string | null; correct_choice_label?: string | null; correct: boolean; explanation?: string | null };

const records = (value: unknown): ApiRecord[] => Array.isArray(value) ? value as ApiRecord[] : [];
const idempotencyKey = (prefix: string) => `${prefix}-${Date.now()}-${Math.random().toString(36).slice(2, 10)}`;

export default function AssessmentAttemptScreen() {
  const { id } = useLocalSearchParams<{ id: string }>();
  const router = useRouter();
  const session = useSession();
  const [assessment, setAssessment] = useState<Assessment | null>(null);
  const [attempts, setAttempts] = useState<Attempt[]>([]);
  const [attempt, setAttempt] = useState<Attempt | null>(null);
  const [answers, setAnswers] = useState<Record<string, string>>({});
  const [review, setReview] = useState<Review[] | null>(null);
  const [score, setScore] = useState<number | null>(null);
  const [passed, setPassed] = useState<boolean | null>(null);
  const [questionIndex, setQuestionIndex] = useState(0);
  const [secondsLeft, setSecondsLeft] = useState<number | null>(null);
  const [startKey, setStartKey] = useState<string | null>(null);
  const [busy, setBusy] = useState(true);
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState('');

  const load = useCallback(async () => {
    if (!id) return;
    setBusy(true); setError('');
    try {
      const result = await session.request<{ assessment: Assessment; attempts: Attempt[] }>(`/practitioner/assessments/${encodeURIComponent(String(id))}`);
      setAssessment(result.assessment);
      setAttempts(records(result.attempts) as Attempt[]);
    } catch (e) { setError(e instanceof Error ? e.message : t('This assessment could not be loaded.')); }
    finally { setBusy(false); }
  }, [id, session]);

  useEffect(() => { void Promise.resolve().then(load); }, [load]);

  useEffect(() => {
    if (!attempt?.started_at || !assessment?.duration_minutes) { const reset = setTimeout(() => setSecondsLeft(null), 0); return () => clearTimeout(reset); }
    const expiresAt = new Date(attempt.started_at).getTime() + assessment.duration_minutes * 60_000;
    const tick = () => setSecondsLeft(Math.max(0, Math.ceil((expiresAt - Date.now()) / 1000)));
    const first = setTimeout(tick, 0); const timer = setInterval(tick, 1000); return () => { clearTimeout(first); clearInterval(timer); };
  }, [attempt?.started_at, assessment?.duration_minutes]);

  const begin = async () => {
    if (!assessment) return;
    const requestKey = startKey ?? idempotencyKey(`assessment-start-${assessment.id}`);
    setStartKey(requestKey);
    setSaving(true); setError(''); setReview(null); setScore(null); setPassed(null);
    try {
      const result = await session.request<{ attempt: Attempt & { answers?: Record<string, string> }; assessment: Assessment; deadline: string | null }>(
        `/practitioner/assessments/${encodeURIComponent(String(assessment.id))}/attempts`, 'POST', {}, requestKey);
      setAssessment(result.assessment); setAttempt(result.attempt); setAnswers(result.attempt.answers ?? {}); setQuestionIndex(0); setStartKey(null);
    } catch (e) { setError(e instanceof Error ? e.message : t('The exam could not be started.')); }
    finally { setSaving(false); }
  };

  const saveChoice = async (question: Question, choice: Choice) => {
    if (!assessment || !attempt || saving) return;
    const updated = { ...answers, [question.id]: choice.id };
    setAnswers(updated); setSaving(true); setError('');
    try {
      await session.request(`/practitioner/assessments/${encodeURIComponent(String(assessment.id))}/attempts/${encodeURIComponent(String(attempt.id))}`, 'PATCH', { answers: { [question.id]: choice.id } }, idempotencyKey(`assessment-answer-${attempt.id}-${question.id}`));
    } catch (e) { setError(e instanceof Error ? e.message : t('Your answer could not be saved. Retry the selected answer before continuing.')); }
    finally { setSaving(false); }
  };

  const showResult = async (attemptId: string | number) => {
    if (!assessment) return;
    setSaving(true); setError('');
    try {
      const result = await session.request<{ attempt: Attempt; review: Review[] }>(`/practitioner/assessments/${encodeURIComponent(String(assessment.id))}/attempts/${encodeURIComponent(String(attemptId))}`);
      setAttempt(result.attempt); setReview(records(result.review) as Review[]); setScore(Number(result.attempt.score ?? 0)); setPassed(Boolean(result.attempt.passed));
    } catch (e) { setError(e instanceof Error ? e.message : t('The result could not be loaded.')); }
    finally { setSaving(false); }
  };

  const submit = async () => {
    if (!assessment || !attempt) return;
    setSaving(true); setError('');
    try {
      await session.request(`/practitioner/assessments/${encodeURIComponent(String(assessment.id))}/attempts/${encodeURIComponent(String(attempt.id))}/submit`, 'POST', {}, `assessment-submit-${attempt.id}`);
      await showResult(attempt.id);
      await load();
    } catch (e) { setError(e instanceof Error ? e.message : t('The exam could not be submitted. Your saved answers are preserved.')); }
    finally { setSaving(false); }
  };

  const latest = attempts[0];
  const canRetry = assessment?.attempt_limit == null || attempts.length < assessment.attempt_limit;
  const currentQuestion = assessment?.questions[questionIndex];
  const answered = assessment?.questions.filter(question => answers[question.id]).length ?? 0;
  const timeLabel = secondsLeft == null ? '' : `${Math.floor(secondsLeft / 60).toString().padStart(2, '0')}:${(secondsLeft % 60).toString().padStart(2, '0')}`;

  return <Page fixedHeader={<AppHeader />}>
    <Pressable accessibilityRole="button" onPress={() => router.back()} style={{ minHeight: 44, flexDirection: 'row', alignItems: 'center', gap: 4 }}><AppIcon name="chevronLeft" size={18} /><Text style={{ color: colors.blue, fontSize: 14, fontWeight: '700' }}>{t('Back to course')}</Text></Pressable>
    {busy && <ActivityIndicator color={colors.blue} style={{ marginVertical: 28 }} />}
    {!!error && <Notice tone="error">{error}</Notice>}
    {!busy && assessment && !attempt && <>
      <Heading eyebrow={t('Course assessment')} title={assessment.title} subtitle={assessment.instructions ?? t('Choose the best answer for each question. Your progress saves as you go.')} />
      <Card><Text style={{ color: colors.navy, fontSize: 14, fontWeight: '800' }}>{t('Assessment details')}</Text><Text style={{ color: colors.muted, fontSize: 12, lineHeight: 18, marginTop: 7 }}>{t('{count} questions · Pass mark {mark}%', { count: assessment.questions.length, mark: assessment.pass_mark })} · {assessment.duration_minutes ? t('{count} minutes', { count: assessment.duration_minutes }) : t('No time limit')} · {assessment.attempt_limit == null ? t('Unlimited attempts') : t('{count} attempts allowed', { count: assessment.attempt_limit })}</Text></Card>
      {latest?.status === 'submitted' && <Card><Text style={{ color: colors.text, fontSize: 14, fontWeight: '700' }}>{t('Most recent score: {score}%', { score: String(latest.score ?? 0) })}</Text><ActionButton label={t('Review submitted result')} secondary onPress={() => void showResult(latest.id)} busy={saving} /></Card>}
      {latest?.status === 'in_progress' && <Notice>{t('Your last attempt is saved. Resume it to continue where you left off.')}</Notice>}
      {canRetry && <ActionButton label={saving ? t('Opening exam…') : latest?.status === 'in_progress' ? t('Resume exam') : latest?.status === 'submitted' ? t('Start another attempt') : t('Begin exam')} onPress={() => void begin()} busy={saving} />}
      {!canRetry && latest?.status === 'submitted' && <Notice>{t('You have used all allowed attempts. Review your result above.')}</Notice>}
    </>}
    {!busy && assessment && attempt?.status === 'in_progress' && !review && <>
      <Heading eyebrow={t('Assessment in progress')} title={assessment.title} subtitle={t('Question {index} of {total} · {answered} answered', { index: Math.min(questionIndex + 1, assessment.questions.length), total: assessment.questions.length, answered })} />
      {secondsLeft != null && <Notice tone={secondsLeft < 60 ? 'error' : 'info'}>{t('Time remaining: {time}. The server enforces the deadline.', { time: timeLabel })}</Notice>}
      {!!currentQuestion && <Card>
        <Text style={{ color: colors.blue, fontSize: 12, fontWeight: '800' }}>{currentQuestion.points === 1 ? t('QUESTION {number} · 1 POINT', { number: questionIndex + 1 }) : t('QUESTION {number} · {points} POINTS', { number: questionIndex + 1, points: currentQuestion.points })}</Text>
        <Text style={{ color: colors.navy, fontSize: 17, lineHeight: 24, fontWeight: '700', marginTop: 10 }}>{currentQuestion.prompt}</Text>
        <View style={{ gap: 8, marginTop: 16 }}>{currentQuestion.choices.map(choice => {
          const selected = answers[currentQuestion.id] === choice.id;
          return <Pressable key={choice.id} accessibilityRole="radio" accessibilityState={{ checked: selected, disabled: saving }} disabled={saving} onPress={() => void saveChoice(currentQuestion, choice)} style={{ minHeight: 50, borderWidth: 1, borderColor: selected ? colors.navy : colors.border, backgroundColor: selected ? colors.surfaceAlt : colors.white, borderRadius: radius.md, padding: 12, flexDirection: 'row', alignItems: 'center', gap: 10 }}>
            <View style={{ width: 20, height: 20, borderRadius: radius.md, borderWidth: 2, borderColor: selected ? colors.navy : colors.disabled, alignItems: 'center', justifyContent: 'center' }}>{selected && <View style={{ width: 10, height: 10, borderRadius: radius.sm, backgroundColor: colors.navy }} />}</View>
            <Text style={{ flex: 1, color: colors.text, fontSize: 14, lineHeight: 20 }}>{choice.label}</Text>
          </Pressable>;
        })}</View>
        {saving && <Text style={{ color: colors.muted, fontSize: 12, marginTop: 10 }}>{t('Saving your selection…')}</Text>}
      </Card>}
      <View style={{ flexDirection: 'row', gap: 9 }}>
        <View style={{ flex: 1 }}><ActionButton label={t('Previous')} secondary disabled={questionIndex === 0 || saving} onPress={() => setQuestionIndex(index => Math.max(0, index - 1))} /></View>
        {questionIndex < assessment.questions.length - 1 ? <View style={{ flex: 1 }}><ActionButton label={t('Next question')} disabled={saving} onPress={() => setQuestionIndex(index => Math.min(assessment.questions.length - 1, index + 1))} /></View> : <View style={{ flex: 1 }}><ActionButton label={t('Submit exam')} disabled={saving || answered !== assessment.questions.length} busy={saving} onPress={() => void submit()} /></View>}
      </View>
      {answered !== assessment.questions.length && <Text style={{ color: colors.muted, fontSize: 12, textAlign: 'center' }}>{t('Answer every question before submitting.')}</Text>}
    </>}
    {!busy && assessment && review && <>
      <Heading eyebrow={t('Assessment result')} title={passed ? t('Assessment passed') : t('Keep studying')} subtitle={t('You scored {score}%. The pass mark is {mark}%.', { score: score ?? 0, mark: assessment.pass_mark })} />
      <Card style={{ alignItems: 'center' }}><Text style={{ color: passed ? colors.success : colors.danger, fontSize: 30, fontWeight: '800' }}>{score ?? 0}%</Text><Text style={{ color: colors.muted, fontSize: 13, marginTop: 3 }}>{passed ? t('Passed') : t('Not passed')} · {t('{correct} of {total} correct', { correct: review.filter(item => item.correct).length, total: review.length })}</Text></Card>
      <SectionTitle title={t('Answer review')} />
      {review.map((item, index) => <Card key={item.question_id}>
        <Text style={{ color: colors.navy, fontSize: 14, lineHeight: 20, fontWeight: '700' }}>{index + 1}. {item.prompt}</Text>
        <Text style={{ color: item.correct ? colors.success : colors.danger, fontSize: 12, fontWeight: '800', marginTop: 8 }}>{item.correct ? t('Correct') : t('Review this answer')}</Text>
        <Text style={{ color: colors.text, fontSize: 12, lineHeight: 18, marginTop: 4 }}>{t('Your answer: {answer}', { answer: item.selected_choice_label ?? t('No answer') })}</Text>
        {!item.correct && <Text style={{ color: colors.text, fontSize: 12, lineHeight: 18, marginTop: 3 }}>{t('Correct answer: {answer}', { answer: item.correct_choice_label ?? '—' })}</Text>}
        {!!item.explanation && <Text style={{ color: colors.muted, fontSize: 12, lineHeight: 18, marginTop: 7 }}>{item.explanation}</Text>}
      </Card>)}
      {canRetry && <ActionButton label={t('Try again')} onPress={() => { setReview(null); setAttempt(null); setAnswers({}); setScore(null); setPassed(null); }} />}
    </>}
    {!!assessment && attempt?.status === 'expired' && <Notice tone="error">{t('The attempt expired. Start a new attempt if you still have attempts remaining.')}</Notice>}
  </Page>;
}
