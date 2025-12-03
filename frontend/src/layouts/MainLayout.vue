<template>
  <q-layout view="hHh lpR fFf">

    <q-header elevated>
      <q-toolbar>

        <q-toolbar-title>
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

    <q-page-container>
      <router-view />
    </q-page-container>

  </q-layout>
</template>

<script setup>
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

const currentLocale = ref(localStorage.getItem('locale') || 'pl');

/**
 * Change language
 */
const changeLanguage = (lang) => {
  locale.value = lang;
  localStorage.setItem('locale', lang);
};

/**
 * Logout
 */
const logout = () => {
  auth.logout();
  router.push('/');
};
</script>
