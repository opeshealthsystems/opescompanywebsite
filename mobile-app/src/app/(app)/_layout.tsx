import { Redirect, Stack } from 'expo-router';
import { colors } from '../../components/ui';
import { useSession } from '../../context/SessionContext';

export default function AuthenticatedLayout() {
  const { account } = useSession();
  // Every signed-in route needs a live or demo account; this also returns users to sign-in when a session expires.
  if (!account) return <Redirect href="/" />;
  return <Stack screenOptions={{ headerShown: false, contentStyle: { backgroundColor: colors.bg }, animation: 'slide_from_right' }}>
    <Stack.Screen name="(tabs)" options={{ headerShown: false }} />
    <Stack.Screen name="feature/[id]" />
    <Stack.Screen name="course/[slug]" />
    <Stack.Screen name="course/[slug]/lesson/[id]" />
    <Stack.Screen name="assessment/[id]" />
    <Stack.Screen name="partner/[applicationId]/members" />
    <Stack.Screen name="action/[type]" />
    <Stack.Screen name="notifications" />
  </Stack>;
}
