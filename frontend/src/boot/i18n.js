// src/boot/i18n.js
import { boot } from 'quasar/wrappers';
import { createI18n } from 'vue-i18n';

import en from 'src/i18n/en.json';
import pl from 'src/i18n/pl.json';

const i18n = createI18n({
  legacy: false,
  locale: localStorage.getItem('locale') || 'pl',
  fallbackLocale: 'en',
  messages: { en, pl }
});

export default boot(({ app }) => {
  app.use(i18n);
});

export { i18n };
