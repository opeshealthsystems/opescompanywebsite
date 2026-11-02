import { Tabs } from 'expo-router';
import { AppIcon, AppIconName, getFeatureIconName } from '../../../components/icons';
import { adaptiveTabFeatures, adaptiveTabLabels } from '../../../components/AdaptiveFeatureTab';
import { useSession } from '../../../context/SessionContext';
import { colors } from '../../../components/ui';

export default function AppTabsLayout() {
  const { audience, demo } = useSession();
  const role = audience ?? 'customer';
  const labels = demo?.tabLabels ?? adaptiveTabLabels[role];
  const features = demo?.tabs ?? adaptiveTabFeatures[role];
  const icons: Record<'work' | 'focus', AppIconName> = {
    work: getFeatureIconName(features.work),
    focus: getFeatureIconName(features.focus),
  };
  return <Tabs screenOptions={{
    headerShown: false,
    tabBarActiveTintColor: colors.navy,
    tabBarInactiveTintColor: colors.muted,
    tabBarLabelStyle: { fontSize: 11, fontWeight: '700', marginTop: 1 },
    tabBarItemStyle: { minHeight: 56, paddingTop: 4, paddingBottom: 4 },
    tabBarStyle: { height: 62, paddingTop: 2, paddingBottom: 2, backgroundColor: colors.white, borderTopColor: colors.border, borderTopWidth: 1, elevation: 0 },
    tabBarHideOnKeyboard: true,
    sceneStyle: { backgroundColor: colors.bg },
  }}>
    <Tabs.Screen name="workspace" options={{ title: 'Home', tabBarIcon: ({ focused }) => <AppIcon name="home" color={focused ? colors.navy : colors.muted} size={20} /> }} />
    <Tabs.Screen name="work" options={{ title: labels.work, tabBarIcon: ({ focused }) => <AppIcon name={icons.work} color={focused ? colors.navy : colors.muted} size={20} /> }} />
    <Tabs.Screen name="focus" options={{ title: labels.focus, tabBarIcon: ({ focused }) => <AppIcon name={icons.focus} color={focused ? colors.navy : colors.muted} size={20} /> }} />
    <Tabs.Screen name="profile" options={{ title: 'Account', tabBarIcon: ({ focused }) => <AppIcon name="profile" color={focused ? colors.navy : colors.muted} size={20} /> }} />
  </Tabs>;
}
