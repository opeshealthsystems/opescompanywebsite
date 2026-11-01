import { Stack } from 'expo-router';
import { colors } from '../../components/ui';

export default function AuthenticatedLayout() {
  return <Stack screenOptions={{ headerShown: false, contentStyle: { backgroundColor: colors.bg }, animation: 'slide_from_right' }}>
    <Stack.Screen name="(tabs)" options={{ headerShown: false }} />
    <Stack.Screen name="feature/[id]" />
    <Stack.Screen name="course/[slug]" />
    <Stack.Screen name="course/[slug]/lesson/[id]" />
    <Stack.Screen name="action/[type]" />
    <Stack.Screen name="notifications" />
  </Stack>;
}
