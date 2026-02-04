import { boot } from 'quasar/wrappers'
import { createI18n } from 'vue-i18n'

import en from 'src/i18n/en/app.json'
import pl from 'src/i18n/pl/app.json'
import es from 'src/i18n/es/app.json'
import pt from 'src/i18n/pt/app.json'

export const i18n = createI18n({
  legacy: false,
  locale: localStorage.getItem('locale') || 'en',
  fallbackLocale: 'en',
  messages: { en, pl, es, pt }
})

export default boot(({ app }) => {
  app.use(i18n)
})
