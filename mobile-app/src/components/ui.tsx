import { ReactNode, useState } from 'react';
import { ActivityIndicator, Alert, Platform, Pressable, RefreshControl, ScrollView, StyleSheet, Text, TextInput, useWindowDimensions, View } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { dateLocale, getLang, t } from '../i18n';

// Design tokens: every screen takes colour, radius and spacing from here (mirrors the website's AA token set).
export const colors = {
  navy: '#082D55', blue: '#315F7D', text: '#183853', muted: '#62778B', white: '#FFFFFF',
  bg: '#F5F8FC', pale: '#EDF3F8', tile: '#E8F1F8', surfaceAlt: '#F2F6FA',
  border: '#D9E2EC', borderStrong: '#BBC9D6', divider: '#EEF2F5',
  placeholder: '#6B7D8E', disabled: '#9AA8B6', onNavy: '#E0EAF2', onNavyMuted: '#C8DAE9',
  success: '#176644', successBg: '#EFF8F3', successBorder: '#C4E4D1',
  warning: '#77520B', warningBg: '#FBF5E8', danger: '#8E3540', dangerBg: '#FFF0F0', dangerBorder: '#EBC9C9', infoBorder: '#C9D9E7',
};
export const radius = { sm: 7, md: 9, lg: 12, pill: 999 };
export const space = { xs: 4, sm: 8, md: 12, lg: 16, xl: 24 };

// Readable labels for API codes and field names (closed_won -> "Closed – won", ngos -> "NGOs").
const labelOverrides: Record<string, string> = {
  ngo: 'NGO', ngos: 'NGOs', id: 'ID', user_id: 'Account ID', emr: 'EMR', crm: 'CRM', url: 'URL', bio: 'Professional bio',
  closed_won: 'Closed – won', closed_lost: 'Closed – lost', specialist_centre: 'Specialist centre', bug_report: 'Bug report',
  years_of_experience: 'Years of experience', workplace_name: 'Workplace', facility_name: 'Facility', facility_type: 'Facility type',
  created_at: 'Created', updated_at: 'Last updated', issued_at: 'Issued', due_date: 'Due date', valid_until: 'Valid until', preferred_date: 'Preferred date',
};
export function humanize(code: string): string {
  const key = code.trim().toLowerCase();
  if (labelOverrides[key]) return t(labelOverrides[key]);
  const words = key.replace(/[_-]+/g, ' ').replace(/\s+/g, ' ').trim();
  return words ? t(words[0].toUpperCase() + words.slice(1)) : code;
}

const pad = (value: number) => String(value).padStart(2, '0');
export const localDate = (date: Date) => `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
const parseDate = (value: string) => { const m = /^(\d{4})-(\d{2})-(\d{2})$/.exec(value); return m ? new Date(Number(m[1]), Number(m[2]) - 1, Number(m[3])) : null; };

// JavaScript-only calendar so date entry ships in OTA updates without adding a native picker module.
export function DateField({ label, value, onChange, minDate, optional = false }: { label: string; value: string; onChange: (value: string) => void; minDate?: string; optional?: boolean }) {
  const selected = parseDate(value);
  const [open, setOpen] = useState(false);
  const [month, setMonth] = useState(() => { const base = selected ?? new Date(); return new Date(base.getFullYear(), base.getMonth(), 1); });
  const min = minDate ? parseDate(minDate) : null;
  const first = (month.getDay() + 6) % 7; // Monday-first grid
  const days = new Date(month.getFullYear(), month.getMonth() + 1, 0).getDate();
  const cells: (number | null)[] = [...Array(first).fill(null), ...Array.from({ length: days }, (_, i) => i + 1)];
  const shown = selected ? selected.toLocaleDateString(dateLocale(), { weekday: 'short', day: 'numeric', month: 'long', year: 'numeric' }) : optional ? t('Not set') : t('Choose a date');
  const shift = (delta: number) => setMonth(current => new Date(current.getFullYear(), current.getMonth() + delta, 1));
  return <View style={styles.fieldWrap}>
    <Text style={styles.fieldLabel}>{label}</Text>
    <Pressable accessibilityRole="button" accessibilityLabel={`${label}: ${shown}`} accessibilityState={{ expanded: open }} onPress={() => setOpen(!open)} style={[styles.input, { justifyContent: 'center' }]}>
      <Text style={{ color: selected ? colors.text : colors.placeholder, fontSize: 16 }}>{shown}</Text>
    </Pressable>
    {open && <View style={styles.calendar}>
      <View style={styles.calendarHead}>
        <Pressable accessibilityRole="button" accessibilityLabel={t('Previous month')} onPress={() => shift(-1)} style={styles.calendarNav}><Text style={styles.calendarNavText}>‹</Text></Pressable>
        <Text style={styles.calendarMonth}>{month.toLocaleDateString(dateLocale(), { month: 'long', year: 'numeric' })}</Text>
        <Pressable accessibilityRole="button" accessibilityLabel={t('Next month')} onPress={() => shift(1)} style={styles.calendarNav}><Text style={styles.calendarNavText}>›</Text></Pressable>
      </View>
      <View style={styles.calendarGrid}>
        {(getLang() === 'fr' ? ['L', 'M', 'M', 'J', 'V', 'S', 'D'] : ['M', 'T', 'W', 'T', 'F', 'S', 'S']).map((d, i) => <Text key={`h${i}`} style={styles.calendarWeekday}>{d}</Text>)}
        {cells.map((day, i) => {
          if (!day) return <View key={`e${i}`} style={styles.calendarCell} />;
          const date = new Date(month.getFullYear(), month.getMonth(), day);
          const iso = localDate(date); const isSelected = iso === value; const disabled = !!min && date < min;
          return <Pressable key={iso} disabled={disabled} accessibilityRole="button" accessibilityState={{ selected: isSelected, disabled }} accessibilityLabel={date.toDateString()} onPress={() => { onChange(iso); setOpen(false); }} style={[styles.calendarCell, isSelected && styles.calendarSelected]}>
            <Text style={[styles.calendarDay, disabled && styles.calendarDayDisabled, isSelected && styles.calendarDaySelected]}>{day}</Text>
          </Pressable>;
        })}
      </View>
      {optional && !!value && <Pressable accessibilityRole="button" onPress={() => { onChange(''); setOpen(false); }} style={{ alignSelf: 'flex-start', padding: 8 }}><Text style={{ color: colors.blue, fontWeight: '700' }}>{t('Clear date')}</Text></Pressable>}
    </View>}
  </View>;
}

export function confirmAction(title: string, message: string, confirmLabel: string, onConfirm: () => void, destructive = false) {
  if (Platform.OS === 'web') { if (globalThis.confirm?.(`${title}

${message}`)) onConfirm(); return; }
  Alert.alert(title, message, [{ text: t('Cancel'), style: 'cancel' }, { text: confirmLabel, style: destructive ? 'destructive' : 'default', onPress: onConfirm }]);
}

export function Page({ children, scroll = true, fixedHeader, onRefresh, refreshing = false }: { children: ReactNode; scroll?: boolean; fixedHeader?: ReactNode; onRefresh?: () => void; refreshing?: boolean }) {
  const { width } = useWindowDimensions();
  const horizontalPadding = width <= 340 ? 14 : width <= 380 ? 16 : 20;
  const content = <View style={[styles.content, { paddingHorizontal: horizontalPadding }]}>{children}</View>;
  return <SafeAreaView edges={['top', 'right', 'bottom', 'left']} style={styles.safe}>
    {!!fixedHeader && <View style={styles.fixedHeader}><View style={[styles.fixedHeaderContent, { paddingHorizontal: horizontalPadding }]}>{fixedHeader}</View></View>}
    {scroll ? <ScrollView style={styles.body} keyboardShouldPersistTaps="handled" contentContainerStyle={styles.scroll} refreshControl={onRefresh ? <RefreshControl refreshing={refreshing} onRefresh={onRefresh} tintColor={colors.blue} colors={[colors.navy]} /> : undefined}>{content}</ScrollView> : content}
  </SafeAreaView>;
}

export function Brand({ compact = false }: { compact?: boolean }) {
  return <View style={styles.brand}>
    <View accessibilityLabel={t('OPES Health Systems')}><Text style={[styles.brandText, compact && styles.compactBrandText]}>{t('OPES')} <Text style={styles.brandAccent}>{t('HEALTH')}</Text></Text><Text style={[styles.brandSub, compact && styles.compactBrandSub]}>{t('SYSTEMS')}</Text></View>
  </View>;
}

export function Heading({ eyebrow, title, subtitle }: { eyebrow?: string; title: string; subtitle?: string }) {
  return <View style={styles.heading}>{!!eyebrow && <Text style={styles.eyebrow}>{eyebrow.toUpperCase()}</Text>}<Text style={styles.title}>{title}</Text>{!!subtitle && <Text style={styles.subtitle}>{subtitle}</Text>}</View>;
}

export function ActionButton({ label, onPress, secondary = false, disabled = false, busy = false }: { label: string; onPress: () => void; secondary?: boolean; disabled?: boolean; busy?: boolean }) {
  return <Pressable accessibilityRole="button" onPress={onPress} disabled={disabled || busy} style={[styles.button, secondary ? styles.secondaryButton : styles.primaryButton, (disabled || busy) && styles.buttonDisabled]}>
    {busy ? <ActivityIndicator color={secondary ? colors.navy : colors.white} /> : <Text style={[styles.buttonText, secondary && styles.secondaryButtonText]}>{label}</Text>}
  </Pressable>;
}

export function Card({ children, onPress, style }: { children: ReactNode; onPress?: () => void; style?: object }) {
  const body = <View style={[styles.card, style]}>{children}</View>;
  return onPress ? <Pressable accessibilityRole="button" onPress={onPress}>{body}</Pressable> : body;
}

export function Field({ label, value, onChangeText, placeholder, multiline = false, keyboardType, autoCapitalize = 'sentences', secureTextEntry = false }: {
  label: string; value: string; onChangeText: (value: string) => void; placeholder?: string; multiline?: boolean; keyboardType?: 'default' | 'email-address' | 'phone-pad' | 'numeric' | 'decimal-pad'; autoCapitalize?: 'none' | 'sentences' | 'words' | 'characters'; secureTextEntry?: boolean;
}) {
  return <View style={styles.fieldWrap}><Text style={styles.fieldLabel}>{label}</Text><TextInput value={value} onChangeText={onChangeText} placeholder={placeholder ?? label} placeholderTextColor="#8A9AA9" multiline={multiline} numberOfLines={multiline ? 4 : 1} keyboardType={keyboardType ?? 'default'} autoCapitalize={autoCapitalize} secureTextEntry={secureTextEntry} style={[styles.input, multiline && styles.textarea]} /></View>;
}

export function SectionTitle({ title, trailing }: { title: string; trailing?: ReactNode }) {
  return <View style={styles.sectionRow}><Text style={styles.sectionTitle}>{title}</Text>{trailing}</View>;
}

export function Notice({ children, tone = 'info' }: { children: ReactNode; tone?: 'info' | 'success' | 'error' }) {
  const toneStyle = tone === 'success' ? styles.noticeSuccess : tone === 'error' ? styles.noticeError : styles.noticeInfo;
  return <View style={[styles.notice, toneStyle]}><Text style={styles.noticeText}>{children}</Text></View>;
}

const styles = StyleSheet.create({
  safe: { flex: 1, backgroundColor: colors.bg }, body: { flex: 1 }, scroll: { flexGrow: 1 }, content: { width: '100%', maxWidth: 620, alignSelf: 'center', paddingHorizontal: 20, paddingTop: 18, paddingBottom: 34 }, fixedHeader: { width: '100%', backgroundColor: colors.white, borderBottomWidth: 1, borderBottomColor: colors.border }, fixedHeaderContent: { width: '100%', maxWidth: 620, alignSelf: 'center', paddingTop: 4, paddingBottom: 4 },
  brand: { alignItems: 'flex-start', justifyContent: 'center', marginBottom: 0 }, brandText: { color: colors.navy, fontSize: 17, fontWeight: '800', letterSpacing: 0.5 }, compactBrandText: { fontSize: 15 }, brandAccent: { color: colors.blue, fontWeight: '600' }, brandSub: { color: colors.muted, fontSize: 11, fontWeight: '700', letterSpacing: 1.2, marginTop: 1 }, compactBrandSub: { fontSize: 10 },
  heading: { marginTop: 12, marginBottom: 20 }, eyebrow: { color: colors.blue, fontSize: 12, fontWeight: '800', letterSpacing: 1, marginBottom: 6 }, title: { color: colors.navy, fontSize: 27, lineHeight: 34, fontWeight: '700' }, subtitle: { color: colors.muted, fontSize: 14, lineHeight: 21, marginTop: 5 },
  button: { minHeight: 48, borderRadius: radius.sm, alignItems: 'center', justifyContent: 'center', paddingHorizontal: 16, marginBottom: 10, borderWidth: 1 }, primaryButton: { backgroundColor: colors.navy, borderColor: colors.navy }, secondaryButton: { backgroundColor: colors.white, borderColor: colors.borderStrong }, buttonText: { color: colors.white, fontSize: 15, fontWeight: '700' }, secondaryButtonText: { color: colors.navy }, buttonDisabled: { opacity: 0.55 },
  card: { backgroundColor: colors.white, borderWidth: 1, borderColor: colors.border, borderRadius: radius.md, padding: 15, marginBottom: 11 }, sectionRow: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', marginTop: 18, marginBottom: 10 }, sectionTitle: { color: colors.navy, fontSize: 16, fontWeight: '700' }, fieldWrap: { marginBottom: 14 }, fieldLabel: { color: colors.text, fontSize: 14, fontWeight: '700', marginBottom: 6 }, input: { minHeight: 48, borderWidth: 1, borderColor: colors.borderStrong, borderRadius: radius.sm, paddingHorizontal: 12, paddingVertical: 10, backgroundColor: colors.white, color: colors.text, fontSize: 16 }, textarea: { minHeight: 102, textAlignVertical: 'top' },
  calendar: { borderWidth: 1, borderColor: colors.border, borderRadius: radius.md, backgroundColor: colors.white, padding: 10, marginTop: 8 }, calendarHead: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', marginBottom: 6 }, calendarNav: { width: 44, height: 44, alignItems: 'center', justifyContent: 'center' }, calendarNavText: { color: colors.navy, fontSize: 24, fontWeight: '700' }, calendarMonth: { color: colors.navy, fontSize: 15, fontWeight: '800' },
  calendarGrid: { flexDirection: 'row', flexWrap: 'wrap' }, calendarWeekday: { width: '14.2857%', textAlign: 'center', color: colors.muted, fontSize: 12, fontWeight: '700', paddingVertical: 4 }, calendarCell: { width: '14.2857%', height: 42, alignItems: 'center', justifyContent: 'center', borderRadius: radius.sm }, calendarSelected: { backgroundColor: colors.navy }, calendarDay: { color: colors.text, fontSize: 15 }, calendarDayDisabled: { color: colors.disabled }, calendarDaySelected: { color: colors.white, fontWeight: '800' },
  notice: { borderRadius: radius.sm, borderWidth: 1, padding: 12, marginVertical: 10 }, noticeInfo: { backgroundColor: colors.pale, borderColor: colors.infoBorder }, noticeSuccess: { backgroundColor: colors.successBg, borderColor: colors.successBorder }, noticeError: { backgroundColor: colors.dangerBg, borderColor: colors.dangerBorder }, noticeText: { color: colors.text, fontSize: 13, lineHeight: 19 },
});
