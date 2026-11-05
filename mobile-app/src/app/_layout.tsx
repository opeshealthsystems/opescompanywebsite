import { colors } from '../components/ui';
import { Stack } from 'expo-router';
import { useEffect, useState } from 'react';
import { SessionProvider } from '../context/SessionContext';
import { SafeAreaProvider } from 'react-native-safe-area-context';
import { getLang, loadSavedLang, onLangChange } from '../i18n';

export default function RootLayout() {
  // Screens read the language through t() at render; remount the navigator (not the session) when it changes.
  const [lang, setLangState] = useState(getLang());
  useEffect(() => {
    const unsubscribe = onLangChange(setLangState);
    void loadSavedLang();
    return unsubscribe;
  }, []);
  return <SafeAreaProvider><SessionProvider><Stack key={lang} screenOptions={{ headerShown: false, contentStyle: { backgroundColor: colors.bg }, animation: 'slide_from_right' }}>
    <Stack.Screen name="index" />
    <Stack.Screen name="signup" />
    <Stack.Screen name="forgot-password" />
    <Stack.Screen name="(app)" options={{ headerShown: false }} />
  </Stack></SessionProvider></SafeAreaProvider>;
}
