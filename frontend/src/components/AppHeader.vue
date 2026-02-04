<template>
  <q-header elevated>
    <q-toolbar>

      <q-toolbar-title>
        {{ $t('app.title') }}
      </q-toolbar-title>

      <q-select
        v-model="currentLocale"
        :options="locales"
        dense
        borderless
        emit-value
        map-options
        style="width: 110px; margin-right: 16px"
        @update:model-value="changeLanguage"
      />

      <q-btn
        flat
        dense
        color="white"
        icon="logout"
        :label="$t('common.logout')"
        @click="logout"
      />
    </q-toolbar>
  </q-header>
</template>

<script>
import { useSessionStore } from 'src/stores/session'
import { i18n } from 'src/boot/i18n'

export default {
  name: 'AppHeader',

  data() {
    return {
      currentLocale: localStorage.getItem('locale') || 'en',
      locales: [
        { label: 'EN', value: 'en' },
        { label: 'PL', value: 'pl' },
        { label: 'ES', value: 'es' },
        { label: 'PT', value: 'pt' }
      ]
    }
  },

  methods: {
    changeLanguage(lang) {
      i18n.global.locale.value = lang
      localStorage.setItem('locale', lang)
    },

    logout() {
      const session = useSessionStore()
      session.clearSession()
      this.$router.push('/')
    }
  }
}
</script>
