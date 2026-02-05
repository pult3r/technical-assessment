<template>
  <q-header elevated>
    <q-toolbar>

      <!-- LOGO + TITLE -->
      <div class="row items-center no-wrap">
        <img
          src="~assets/student-cribs-logo.svg"
          alt="Student Cribs"
          class="header-logo q-mr-sm"
        />

        <q-toolbar-title class="text-weight-medium">
          Student Cribs
        </q-toolbar-title>
      </div>

      <q-space />

      <!-- LANGUAGE SELECTOR -->
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

      <!-- LOGOUT -->
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
      this.$i18n.locale = lang
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

<style scoped>
.header-logo {
  height: 28px;
  width: auto;
  display: block;
}
</style>
