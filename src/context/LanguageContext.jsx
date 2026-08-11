import React, { createContext, useContext, useState, useEffect } from 'react';
import translations from '@/i18n/translations';

const LanguageContext = createContext(null);

function detectBrowserLang() {
  const nav = navigator.language || navigator.userLanguage || '';
  const code = nav.toLowerCase();
  if (code.startsWith('zh')) return 'zh';
  if (code.startsWith('ko')) return 'ko';
  if (code.startsWith('en')) return 'en';
  return 'vi';
}

export function LanguageProvider({ children }) {
  const [lang, setLangState] = useState(() => {
    const saved = localStorage.getItem('lang');
    if (saved && translations[saved]) return saved;
    return detectBrowserLang();
  });

  const setLang = (l) => {
    setLangState(l);
    localStorage.setItem('lang', l);
  };

  const t = (key) => {
    const dict = translations[lang] || translations.vi;
    return dict[key] ?? translations.vi[key] ?? key;
  };

  return (
    <LanguageContext.Provider value={{ lang, setLang, t }}>
      {children}
    </LanguageContext.Provider>
  );
}

export function useLanguage() {
  const ctx = useContext(LanguageContext);
  if (!ctx) throw new Error('useLanguage must be used within LanguageProvider');
  return ctx;
}
