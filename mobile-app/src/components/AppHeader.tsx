import { useRouter } from 'expo-router';
import { Pressable, View } from 'react-native';
import { FeatureId, featureCatalog, useSession } from '../context/SessionContext';
import { AppIcon } from './icons';
import { Brand, colors } from './ui';

export function AppHeader() {
  const router = useRouter();
  const session = useSession();
  const messageDestination: FeatureId | null = session.audience === 'staff'
    ? 'contacts'
    : session.audience === 'customer'
      ? 'tickets'
      : null;
  const canOpenMessages = !!messageDestination && session.isAllowed(featureCatalog[messageDestination]);
  const canOpenNotifications = session.isAllowed(featureCatalog.notifications);

  return <View style={{ minHeight: 48, flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', gap: 12 }}>
    <Brand compact />
    <View style={{ flexDirection: 'row', alignItems: 'center', gap: 2 }}>
      {canOpenMessages && messageDestination && <Pressable accessibilityRole="button" accessibilityLabel="Open messages" hitSlop={4} onPress={() => router.push({ pathname: '/feature/[id]', params: { id: messageDestination } } as never)} style={{ width: 44, height: 44, alignItems: 'center', justifyContent: 'center', borderRadius: 8 }}>
        <AppIcon name="message" size={20} color={colors.navy} />
      </Pressable>}
      {canOpenNotifications && <Pressable accessibilityRole="button" accessibilityLabel="Open notifications" hitSlop={4} onPress={() => router.push('/notifications' as never)} style={{ width: 44, height: 44, alignItems: 'center', justifyContent: 'center', borderRadius: 8 }}>
        <AppIcon name="bell" size={19} color={colors.navy} />
      </Pressable>}
      <Pressable accessibilityRole="button" accessibilityLabel="Open my account" hitSlop={4} onPress={() => router.push('/profile' as never)} style={{ width: 44, height: 44, alignItems: 'center', justifyContent: 'center', borderRadius: 8 }}>
        <AppIcon name="profile" size={19} color={colors.navy} />
      </Pressable>
    </View>
  </View>;
}
