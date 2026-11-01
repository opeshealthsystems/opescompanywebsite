import {
  Award,
  BookOpen,
  BriefcaseBusiness,
  ChevronLeft,
  ChevronRight,
  CircleCheck,
  CircleHelp,
  CircleUserRound,
  ClipboardList,
  FileText,
  FlaskConical,
  Handshake,
  House,
  LockKeyhole,
  RefreshCw,
  ShieldCheck,
  Stethoscope,
  UsersRound,
} from 'lucide-react-native';
import type { FeatureId } from '../context/SessionContext';
import { colors } from './ui';

const icons = {
  award: Award,
  book: BookOpen,
  briefcase: BriefcaseBusiness,
  check: CircleCheck,
  chevronLeft: ChevronLeft,
  chevronRight: ChevronRight,
  circleHelp: CircleHelp,
  clipboard: ClipboardList,
  file: FileText,
  flask: FlaskConical,
  handshake: Handshake,
  home: House,
  lock: LockKeyhole,
  profile: CircleUserRound,
  refresh: RefreshCw,
  shield: ShieldCheck,
  stethoscope: Stethoscope,
  users: UsersRound,
};

export type AppIconName = keyof typeof icons;

export function AppIcon({
  name,
  size = 20,
  color = colors.blue,
  strokeWidth = 1.8,
}: {
  name: AppIconName;
  size?: number;
  color?: string;
  strokeWidth?: number;
}) {
  const Icon = icons[name];
  return <Icon size={size} color={color} strokeWidth={strokeWidth} />;
}

export function getFeatureIconName(id: FeatureId): AppIconName {
  switch (id) {
    case 'customers':
    case 'serviceRequests':
    case 'customerProfile':
      return 'home';
    case 'contacts':
      return 'users';
    case 'partnerApps':
    case 'partnerships':
    case 'partnerApplication':
      return 'handshake';
    case 'deals':
      return 'briefcase';
    case 'practitionerProfile':
      return 'stethoscope';
    case 'courses':
      return 'book';
    case 'certificates':
      return 'award';
    case 'assignments':
      return 'flask';
    case 'quotes':
    case 'tickets':
    case 'invoices':
      return 'file';
  }
}

export function getRoleIconName(role: string): AppIconName {
  switch (role.toLowerCase()) {
    case 'admin':
    case 'staff':
      return 'briefcase';
    case 'customer':
      return 'home';
    case 'practitioner':
      return 'stethoscope';
    case 'tester':
      return 'flask';
    case 'partner':
      return 'handshake';
    default:
      return 'profile';
  }
}
