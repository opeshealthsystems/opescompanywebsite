import { Stack } from 'expo-router';
import { SessionProvider } from '../context/SessionContext';
import { SafeAreaProvider } from 'react-native-safe-area-context';

export default function RootLayout() {
  return <SafeAreaProvider><SessionProvider><Stack screenOptions={{ headerShown: false, contentStyle: { backgroundColor: '#F5F8FC' }, animation: 'slide_from_right' }} /></SessionProvider></SafeAreaProvider>;
}
