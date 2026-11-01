import { ReactNode } from 'react';
import { ActivityIndicator, Image, Pressable, ScrollView, StyleSheet, Text, TextInput, View } from 'react-native';

export const colors = { navy: '#082D55', blue: '#2369A6', text: '#183853', muted: '#62778B', border: '#D9E2EC', bg: '#F5F8FC', white: '#FFFFFF', pale: '#EDF3F8', success: '#176644', warning: '#77520B', danger: '#8E3540' };

export function Page({ children, scroll = true }: { children: ReactNode; scroll?: boolean }) {
  const content = <View style={styles.content}>{children}</View>;
  return <View style={styles.safe}>{scroll ? <ScrollView keyboardShouldPersistTaps="handled" contentContainerStyle={styles.scroll}>{content}</ScrollView> : content}</View>;
}

export function Brand({ compact = false }: { compact?: boolean }) {
  return <View style={styles.brand}>
    <Image source={require('../../assets/icon.png')} style={compact ? styles.compactIcon : styles.icon} accessibilityLabel="OPES Health app icon" />
    <View><Text style={styles.brandText}>OPES <Text style={styles.brandAccent}>HEALTH</Text></Text><Text style={styles.brandSub}>HEALTH SYSTEMS</Text></View>
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
  safe: { flex: 1, backgroundColor: colors.bg }, scroll: { flexGrow: 1 }, content: { width: '100%', maxWidth: 620, alignSelf: 'center', paddingHorizontal: 20, paddingTop: 18, paddingBottom: 34 },
  brand: { flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: 12, marginBottom: 22 }, icon: { width: 64, height: 64, resizeMode: 'contain' }, compactIcon: { width: 42, height: 42, resizeMode: 'contain' }, brandText: { color: colors.navy, fontSize: 17, fontWeight: '800', letterSpacing: 0.5 }, brandAccent: { color: colors.blue, fontWeight: '600' }, brandSub: { color: colors.muted, fontSize: 9, fontWeight: '700', letterSpacing: 1.7, marginTop: 2 },
  heading: { marginTop: 12, marginBottom: 20 }, eyebrow: { color: colors.blue, fontSize: 10, fontWeight: '800', letterSpacing: 1.2, marginBottom: 6 }, title: { color: colors.navy, fontSize: 27, lineHeight: 34, fontWeight: '700' }, subtitle: { color: colors.muted, fontSize: 14, lineHeight: 21, marginTop: 5 },
  button: { minHeight: 46, borderRadius: 7, alignItems: 'center', justifyContent: 'center', paddingHorizontal: 16, marginBottom: 10, borderWidth: 1 }, primaryButton: { backgroundColor: colors.navy, borderColor: colors.navy }, secondaryButton: { backgroundColor: colors.white, borderColor: '#BBC9D6' }, buttonText: { color: colors.white, fontSize: 14, fontWeight: '700' }, secondaryButtonText: { color: colors.navy }, buttonDisabled: { opacity: 0.55 },
  card: { backgroundColor: colors.white, borderWidth: 1, borderColor: colors.border, borderRadius: 9, padding: 15, marginBottom: 11 }, sectionRow: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', marginTop: 18, marginBottom: 10 }, sectionTitle: { color: colors.navy, fontSize: 16, fontWeight: '700' }, fieldWrap: { marginBottom: 14 }, fieldLabel: { color: colors.text, fontSize: 12, fontWeight: '700', marginBottom: 6 }, input: { minHeight: 46, borderWidth: 1, borderColor: '#BBC9D6', borderRadius: 7, paddingHorizontal: 12, paddingVertical: 10, backgroundColor: colors.white, color: colors.text, fontSize: 14 }, textarea: { minHeight: 102, textAlignVertical: 'top' },
  notice: { borderRadius: 7, borderWidth: 1, padding: 12, marginVertical: 10 }, noticeInfo: { backgroundColor: colors.pale, borderColor: '#C9D9E7' }, noticeSuccess: { backgroundColor: '#EFF8F3', borderColor: '#C4E4D1' }, noticeError: { backgroundColor: '#FFF0F0', borderColor: '#EBC9C9' }, noticeText: { color: colors.text, fontSize: 12, lineHeight: 18 },
});
