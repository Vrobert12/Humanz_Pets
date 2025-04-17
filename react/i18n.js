import i18n from 'i18next';
import { initReactI18next } from 'react-i18next';
import AsyncStorage from '@react-native-async-storage/async-storage';

import en from './locales/en.json';
import hu from './locales/hu.json';
import sr from './locales/sr.json';

const LANG_KEY = 'appLanguage';

const resources = {
    en: { translation: en },
    hu: { translation: hu },
    sr: { translation: sr }
};

i18n
    .use(initReactI18next)
    .init({
        resources,
        fallbackLng: 'en',
        lng: 'en', // default
        interpolation: {
            escapeValue: false
        }
    });

// Optional: load language from AsyncStorage
export const loadLanguage = async () => {
    const savedLang = await AsyncStorage.getItem(LANG_KEY);
    if (savedLang && savedLang !== i18n.language) {
        i18n.changeLanguage(savedLang);
    }
};

export const setLanguage = async (lang) => {
    await AsyncStorage.setItem(LANG_KEY, lang);
    i18n.changeLanguage(lang);
};

export default i18n;
