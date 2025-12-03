<template>
  <q-header elevated>
    <q-toolbar>

      <q-toolbar-title>
        <!-- The title is intentionally generic - it can be replaced with logo or app name -->
        {{ t('generator.title') }}
      </q-toolbar-title>

      <!-- LANGUAGE SELECTOR -->
      <q-select
        v-model="currentLocale"
        :options="locales"
        dense
        borderless
        emit-value
        map-options
        style="width: 90px; margin-right: 20px"
        @update:model-value="changeLanguage"
      />

      <!-- LOGOUT -->
      <q-btn
        flat
        dense
        color="white"
        icon="logout"
        :label="t('common.logout')"
        @click="logout"
      />
    </q-toolbar>
  </q-header>
</template>

<script setup>
/**
 * AppHeader.vue
 * Encapsulates the top toolbar: app title, language selector and logout button.
 * Comments and code are in English.
 */

import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { useRouter } from 'vue-router';
import { useAuthStore } from 'src/stores/auth';

const { t, locale } = useI18n();
const router = useRouter();
const auth = useAuthStore();

/**
 * Supported languages
 */
const locales = [
  { label: 'PL', value: 'pl' },
  { label: 'EN', value: 'en' }
];

const currentLocale = ref(localStorage.getItem('locale') || locale.value || 'pl');

/**
 * Change language - updates i18n locale and stores preference to localStorage.
 */
const changeLanguage = (lang) => {
  locale.value = lang;
  localStorage.setItem('locale', lang);
};

/**
 * Logout - delegates to auth store and redirects to login.
 */
const logout = () => {
  auth.logout();
  router.push('/');
};
</script>
