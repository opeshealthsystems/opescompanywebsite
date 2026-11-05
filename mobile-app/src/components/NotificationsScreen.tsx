import { useCallback, useEffect, useMemo, useState } from 'react';
import { ActivityIndicator, Pressable, Text, View } from 'react-native';
import { useRouter } from 'expo-router';
import { ApiRecord, featureCatalog, useSession } from '../context/SessionContext';
import { ActionButton, Card, Heading, Notice, Page, SectionTitle, colors } from './ui';
import { AppIcon } from './icons';
import { AppHeader } from './AppHeader';
import { dateLocale, t } from '../i18n';

function notificationRows(value: unknown): ApiRecord[] {
  if (Array.isArray(value)) return value.filter((row): row is ApiRecord => !!row && typeof row === 'object');
  if (!value || typeof value !== 'object') return [];
  const result = value as Record<string, unknown>;
  for (const key of ['notifications', 'items', 'data', 'results']) {
    if (Array.isArray(result[key])) return (result[key] as unknown[]).filter((row): row is ApiRecord => !!row && typeof row === 'object');
  }
  return [];
}

function timeLabel(value: unknown): string {
  if (typeof value !== 'string' || !value) return t('Recently');
  const date = new Date(value);
  return Number.isNaN(date.getTime()) ? value : date.toLocaleString(dateLocale(), { dateStyle: 'medium', timeStyle: 'short' });
}

export default function NotificationsScreen() {
  const router = useRouter();
  const session = useSession();
  const feature = featureCatalog.notifications;
  const [rows, setRows] = useState<ApiRecord[]>([]);
  const [loaded, setLoaded] = useState(false);
  const [acting, setActing] = useState<string | null>(null);
  const [error, setError] = useState('');
  const [message, setMessage] = useState('');
  const [unreadOnly, setUnreadOnly] = useState(false);
  const mayRead = session.isAllowed(feature);
  const mayWrite = session.isAllowed(feature, true);
  const unreadCount = useMemo(() => rows.filter(row => !row.read_at && String(row.status ?? '').toLowerCase() !== 'read').length, [rows]);

  const load = useCallback(async () => {
    setError('');
    try {
      const result = session.demo ? await session.rowsFor('notifications') : await session.request<unknown>('/notifications');
      setRows(notificationRows(result));
    } catch (e) { setError(e instanceof Error ? e.message : t('Notifications could not be loaded.')); }
    finally { setLoaded(true); }
  }, [session]);

  useEffect(() => {
    if (!mayRead) return;
    let active = true;
    void (async () => {
      try {
        const result = session.demo ? await session.rowsFor('notifications') : await session.request<unknown>('/notifications');
        if (active) setRows(notificationRows(result));
      } catch (e) { if (active) setError(e instanceof Error ? e.message : t('Notifications could not be loaded.')); }
      finally { if (active) setLoaded(true); }
    })();
    return () => { active = false; };
  }, [mayRead, session]);

  const markRead = async (row: ApiRecord) => {
    const id = String(row.id ?? '');
    if (!id || !mayWrite) return;
    setActing(id); setError(''); setMessage('');
    try {
      const readAt = new Date().toISOString();
      if (session.demo) session.mutateDemo('notifications', 'update-status', { read_at: readAt, status: 'read' }, id);
      else await session.request(`/notifications/${encodeURIComponent(id)}/read`, 'POST', {});
      setRows(current => current.map(item => String(item.id) === id ? { ...item, read_at: readAt, status: 'read' } : item));
      setMessage(t('Notification marked as read.'));
    } catch (e) { setError(e instanceof Error ? e.message : t('This notification could not be updated.')); }
    finally { setActing(null); }
  };

  const markAllRead = async () => {
    if (!mayWrite || unreadCount === 0) return;
    setActing('all'); setError(''); setMessage('');
    try {
      const readAt = new Date().toISOString();
      if (session.demo) rows.filter(row => !row.read_at).forEach(row => session.mutateDemo('notifications', 'update-status', { read_at: readAt, status: 'read' }, String(row.id)));
      else await session.request('/notifications/read-all', 'POST', {});
      setRows(current => current.map(row => ({ ...row, read_at: row.read_at ?? readAt, status: 'read' })));
      setMessage(t('All notifications marked as read.'));
    } catch (e) { setError(e instanceof Error ? e.message : t('Notifications could not be updated.')); }
    finally { setActing(null); }
  };

  const visible = unreadOnly ? rows.filter(row => !row.read_at && String(row.status ?? '').toLowerCase() !== 'read') : rows;
  return <Page fixedHeader={<AppHeader />} onRefresh={() => void load()}>
    {session.demo && <Notice>{t('DEMO MODE · Sample notifications are fictional and remain on this device.')}</Notice>}
    <Heading eyebrow={t('Account updates')} title={t('Notifications')} subtitle={unreadCount === 1 ? t('1 unread notification') : t('{count} unread notifications', { count: unreadCount })} />
    {!!error && <Notice tone="error">{error}</Notice>}
    {!!message && <Notice tone="success">{message}</Notice>}
    <View style={{ flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', gap: 10 }}>
      <Pressable accessibilityRole="button" accessibilityState={{ selected: unreadOnly }} onPress={() => setUnreadOnly(value => !value)} style={{ minHeight: 44, flexDirection: 'row', alignItems: 'center', gap: 7, paddingHorizontal: 10 }}><AppIcon name="bell" size={18} /><Text style={{ color: colors.blue, fontWeight: '700' }}>{unreadOnly ? t('Showing unread') : t('Show unread only')}</Text></Pressable>
      {mayWrite && unreadCount > 0 && <Pressable accessibilityRole="button" disabled={!!acting} onPress={() => void markAllRead()} style={{ minHeight: 44, justifyContent: 'center', paddingHorizontal: 6 }}><Text style={{ color: colors.blue, fontSize: 12, fontWeight: '800' }}>{acting === 'all' ? t('Saving…') : t('Mark all read')}</Text></Pressable>}
    </View>
    {!mayRead && !session.demo && <Notice tone="error">{t('Your account does not have notification read access.')}</Notice>}
    {mayRead && !loaded && <ActivityIndicator color={colors.blue} style={{ marginVertical: 24 }} />}
    {mayRead && loaded && visible.length === 0 && !error && <Card><Text style={{ color: colors.navy, fontWeight: '800' }}>{unreadOnly ? t('You’re all caught up') : t('No notifications yet')}</Text><Text style={{ color: colors.muted, fontSize: 13, lineHeight: 19, marginTop: 6 }}>{unreadOnly ? t('New unread updates will appear here.') : t('Account updates and service messages will appear here.')}</Text></Card>}
    {mayRead && loaded && visible.map((row, index) => {
      const isRead = !!row.read_at || String(row.status ?? '').toLowerCase() === 'read';
      return <Card key={String(row.id ?? index)} style={{ borderLeftWidth: 3, borderLeftColor: isRead ? colors.border : colors.blue }}>
        <View style={{ flexDirection: 'row', alignItems: 'flex-start', gap: 10 }}><AppIcon name={isRead ? 'check' : 'bell'} size={19} color={isRead ? colors.muted : colors.blue} /><View style={{ flex: 1, minWidth: 0 }}>
          <Text style={{ color: colors.navy, fontSize: 15, lineHeight: 21, fontWeight: '800' }}>{String(row.title ?? row.subject ?? t('Account update'))}</Text>
          {!!(row.body ?? row.message ?? row.description) && <Text selectable style={{ color: colors.text, fontSize: 13, lineHeight: 20, marginTop: 6 }}>{String(row.body ?? row.message ?? row.description)}</Text>}
          <Text style={{ color: colors.muted, fontSize: 12, marginTop: 8 }}>{timeLabel(row.created_at ?? row.sent_at ?? row.updated_at)}{isRead ? ' · Read' : ' · Unread'}</Text>
          {!isRead && mayWrite && <Pressable accessibilityRole="button" disabled={!!acting} onPress={() => void markRead(row)} style={{ minHeight: 44, justifyContent: 'center', alignSelf: 'flex-start', paddingRight: 12 }}><Text style={{ color: colors.blue, fontWeight: '800' }}>{acting === String(row.id) ? t('Saving…') : t('Mark as read')}</Text></Pressable>}
        </View></View>
      </Card>;
    })}
    <SectionTitle title={t('Account')} />
    <ActionButton label={t('Refresh notifications')} secondary onPress={() => { setLoaded(false); void load(); }} disabled={!mayRead || !loaded} busy={mayRead && !loaded} />
    <ActionButton label={t('Back to dashboard')} secondary onPress={() => router.replace('/workspace')} />
  </Page>;
}
