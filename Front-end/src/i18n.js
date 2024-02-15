import i18n from 'i18next';
import { initReactI18next } from 'react-i18next';

import translationEN from './locales/en/translation.json';
import translationFR from './locales/fr/translation.json';

// les ressources de traduction
const resources = {
  en: {
    translation: translationEN
  },
  fr: {
    translation: translationFR
  }
};

i18n
  .use(initReactI18next) // passe i18n down to react-i18next
  .init({
    resources,
    lng: 'fr', // langue initiale
    fallbackLng: 'en', // en cas de non disponibilité de certaines traductions

    interpolation: {
      escapeValue: false // réagit déjà à l'échappement des valeurs
    }
  });

export default i18n;
