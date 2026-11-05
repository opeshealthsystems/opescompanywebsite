import { Tabs } from 'expo-router';
import { AppIcon, AppIconName, getFeatureIconName } from '../../../components/icons';
import { adaptiveTabFeatures, adaptiveTabLabels } from '../../../components/AdaptiveFeatureTab';
import { useSession } from '../../../context/SessionContext';
import { colors } from '../../../components/ui';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { t } from '../../../i18n';

export default function AppTabsLayout() {
  const { audience } = useSession();
  const insets = useSafeAreaInsets();
  // Accounts without a recognised workspace get Home and Account only, never another role's tabs.
  const role = audience ?? 'customer';
  const labels = adaptiveTabLabels[role];
  const features = adaptiveTabFeatures[role];
  const icons: Record<'work' | 'focus', AppIconName> = {
    work: getFeatureIconName(features.work),
    focus: getFeatureIconName(features.focus),
  };
  return <Tabs screenOptions={{
    headerShown: false,
    tabBarActiveTintColor: colors.navy,
    tabBarInactiveTintColor: colors.muted,
    tabBarLabelStyle: { fontSize: 11, fontWeight: '700', marginTop: 1 },
    tabBarItemStyle: { minHeight: 54, paddingTop: 3, paddingBottom: 3 },
    tabBarStyle: { height: 58 + insets.bottom, paddingTop: 3, paddingBottom: Math.max(insets.bottom, 4), backgroundColor: colors.white, borderTopColor: colors.border, borderTopWidth: 1, elevation: 0 },
    tabBarHideOnKeyboard: true,
    sceneStyle: { backgroundColor: colors.bg },
  }}>
    <Tabs.Screen name="workspace" options={{ title: t('Home'), tabBarIcon: ({ focused }) => <AppIcon name="home" color={focused ? colors.navy : colors.muted} size={20} /> }} />
    <Tabs.Screen name="work" options={{ href: audience ? undefined : null, title: t(labels.work), tabBarIcon: ({ focused }) => <AppIcon name={icons.work} color={focused ? colors.navy : colors.muted} size={20} /> }} />
    <Tabs.Screen name="focus" options={{ href: audience ? undefined : null, title: t(labels.focus), tabBarIcon: ({ focused }) => <AppIcon name={icons.focus} color={focused ? colors.navy : colors.muted} size={20} /> }} />
    <Tabs.Screen name="profile" options={{ title: t('Account'), tabBarIcon: ({ focused }) => <AppIcon name="profile" color={focused ? colors.navy : colors.muted} size={20} /> }} />
  </Tabs>;
}
