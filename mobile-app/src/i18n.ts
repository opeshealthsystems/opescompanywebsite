import * as SecureStore from 'expo-secure-store';
import { Platform } from 'react-native';
import { fr } from './i18n.fr';

// English strings are the keys; the French dictionary maps each key to its translation.
// t() reads the current language at call time; the root layout remounts the app when it changes.
export type Lang = 'en' | 'fr';
const LANG_KEY = 'opes.health.lang.v1';

function deviceLang(): Lang {
  try {
    const locale = Intl.DateTimeFormat().resolvedOptions().locale ?? '';
    return locale.toLowerCase().startsWith('fr') ? 'fr' : 'en';
  } catch { return 'en'; }
}

let current: Lang = deviceLang();
const listeners = new Set<(lang: Lang) => void>();

export const getLang = (): Lang => current;
export const dateLocale = (): string => (current === 'fr' ? 'fr-FR' : 'en-GB');

export function t(text: string, params?: Record<string, string | number>): string {
  let out = current === 'fr' ? fr[text] ?? text : text;
  if (params) for (const [key, value] of Object.entries(params)) out = out.split(`{${key}}`).join(String(value));
  return out;
}

export function setLang(lang: Lang, persist = true) {
  if (lang === current) return;
  current = lang;
  listeners.forEach(listener => listener(lang));
  if (persist && Platform.OS !== 'web') void SecureStore.setItemAsync(LANG_KEY, lang).catch(() => undefined);
}

export function onLangChange(listener: (lang: Lang) => void) {
  listeners.add(listener);
  return () => { listeners.delete(listener); };
}

// A saved choice from the Account screen overrides the device language.
export async function loadSavedLang() {
  if (Platform.OS === 'web') return;
  try {
    const saved = await SecureStore.getItemAsync(LANG_KEY);
    if (saved === 'en' || saved === 'fr') setLang(saved, false);
  } catch { /* keep the device language */ }
}
