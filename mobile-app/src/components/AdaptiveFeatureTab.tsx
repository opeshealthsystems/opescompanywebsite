import { useSession, FeatureId } from '../context/SessionContext';
import { FeatureContent } from './FeatureScreen';
import LearningCatalogScreen from './LearningCatalogScreen';
import PractitionerCertificatesScreen from './PractitionerCertificatesScreen';

const destinations: Record<string, { work: FeatureId; focus: FeatureId }> = {
  staff: { work: 'quotes', focus: 'contacts' },
  customer: { work: 'serviceRequests', focus: 'tickets' },
  practitioner: { work: 'courses', focus: 'certificates' },
  tester: { work: 'assignments', focus: 'bugReports' },
  partner: { work: 'partnerships', focus: 'partnerApplication' },
};

export function AdaptiveFeatureTab({ slot }: { slot: 'work' | 'focus' }) {
  const { audience } = useSession();
  const feature = destinations[audience ?? '']?.[slot];
  if (feature === 'courses') return <LearningCatalogScreen inTab />;
  if (feature === 'certificates') return <PractitionerCertificatesScreen inTab />;
  return <FeatureContent featureId={feature} inTab />;
}

export const adaptiveTabLabels = {
  staff: { work: 'Quotes', focus: 'Contacts' },
  customer: { work: 'Services', focus: 'Support' },
  practitioner: { work: 'Courses', focus: 'Awards' },
  tester: { work: 'Assignments', focus: 'Bugs' },
  partner: { work: 'Directory', focus: 'Apply' },
} as const;

export const adaptiveTabFeatures: Record<string, { work: FeatureId; focus: FeatureId }> = destinations;
