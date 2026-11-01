import { Stack } from 'expo-router';
import { SessionProvider } from '../context/SessionContext';
import { SafeAreaProvider } from 'react-native-safe-area-context';

export default function RootLayout() {
  return <SafeAreaProvider><SessionProvider><Stack screenOptions={{ headerShown: false, contentStyle: { backgroundColor: '#F5F8FC' }, animation: 'slide_from_right' }}>
    <Stack.Screen name="index" />
    <Stack.Screen name="signup" />
    <Stack.Screen name="forgot-password" />
    <Stack.Screen name="(app)" options={{ headerShown: false }} />
  </Stack></SessionProvider></SafeAreaProvider>;
}
