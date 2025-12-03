<template>
  <q-page class="flex flex-center">

    <!-- LANGUAGE SELECTOR -->
    <div class="absolute-top-right q-pa-md">
      <q-select
        v-model="currentLocale"
        :options="locales"
        dense
        borderless
        emit-value
        map-options
        style="width: 90px"
        @update:model-value="changeLanguage"
      />
    </div>

    <!-- LOGIN CARD -->
    <q-card style="width: 350px; max-width: 90%">
      <q-card-section>
        <div class="text-h6">{{ t('login.title') }}</div>
      </q-card-section>

      <q-card-section>
        <q-input
          v-model="username"
          :label="t('login.username')"
          filled
          class="q-mb-md"
        />

        <q-input
          v-model="password"
          :label="t('login.password')"
          type="password"
          filled
        />
      </q-card-section>

      <q-card-actions align="right">
        <q-btn
          color="primary"
          :label="t('login.button')"
          @click="submit"
        />
      </q-card-actions>

      <q-card-section v-if="error">
        <q-banner class="bg-red-4 text-white">
          {{ errorMessage }}
        </q-banner>
      </q-card-section>
    </q-card>

  </q-page>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';
import { api } from 'src/boot/axios';
import { useAuthStore } from 'src/stores/auth';

const username = ref('');
const password = ref('');
const error = ref(false);
const errorMessage = ref('');

const { t, locale } = useI18n();
const auth = useAuthStore();
const router = useRouter();

/* Language selector */
const locales = [
  { label: 'PL', value: 'pl' },
  { label: 'EN', value: 'en' }
];

const currentLocale = ref(localStorage.getItem('locale') || 'pl');

const changeLanguage = (lang) => {
  locale.value = lang;
  localStorage.setItem('locale', lang);
};

/* Login submit */
const submit = async () => {
  error.value = false;

  try {
    const res = await api.post('/login', {
      username: username.value,
      password: password.value
    });

    auth.setToken(res.data.token);
    router.push('/generator');

  } catch {
    error.value = true;
    errorMessage.value = t('login.error');
  }
};
</script>
