import { useLocalSearchParams } from 'expo-router';
import LearningCatalogScreen from '../../components/LearningCatalogScreen';
import PractitionerCertificatesScreen from '../../components/PractitionerCertificatesScreen';
import FeatureScreen from '../../components/FeatureScreen';

export default function FeatureRoute() {
  const { id } = useLocalSearchParams<{ id: string }>();
  if (id === 'courses') return <LearningCatalogScreen />;
  if (id === 'certificates') return <PractitionerCertificatesScreen />;
  return <FeatureScreen />;
}
