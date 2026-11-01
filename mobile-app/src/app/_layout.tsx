import { Stack } from 'expo-router';
import { SessionProvider } from '../context/SessionContext';

export default function RootLayout() {
  return <SessionProvider><Stack screenOptions={{ headerShown: false, contentStyle: { backgroundColor: '#F5F8FC' }, animation: 'slide_from_right' }} /></SessionProvider>;
}
