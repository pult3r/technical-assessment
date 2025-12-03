<template>
  <q-page class="q-pa-xl flex flex-center">
    <q-card class="q-pa-lg" style="width: 380px; max-width: 90%;">
      <h3 class="text-center">{{ t('register.title') }}</h3>

      <q-input
        filled
        v-model="username"
        class="q-mt-md"
        :label="t('register.username')"
        lazy-rules
        :rules="[val => !!val || t('register.validation.username')]"
      />

      <q-input
        filled
        v-model="email"
        class="q-mt-md"
        type="email"
        :label="t('register.email')"
        lazy-rules
        :rules="[val => !!val || t('register.validation.email')]"
      />

      <q-input
        filled
        v-model="password"
        type="password"
        class="q-mt-md"
        :label="t('register.password')"
        lazy-rules
        :rules="[val => !!val || t('register.validation.password')]"
      />

      <q-btn
        :label="t('register.button')"
        color="primary"
        class="q-mt-lg full-width"
        :loading="loading"
        @click="register"
      />

      <div class="q-mt-md text-negative" v-if="error">
        {{ errorMessage }}
      </div>

      <q-btn
        flat
        class="q-mt-md full-width"
        :label="t('register.go_login')"
        @click="router.push('/')"
      />
    </q-card>
  </q-page>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { api } from 'src/boot/axios';
import { useI18n } from 'vue-i18n';
import { useAuthStore } from 'src/stores/auth';

const router = useRouter();
const { t } = useI18n();
const auth = useAuthStore();

const username = ref('');
const email = ref('');
const password = ref('');

const loading = ref(false);
const error = ref(false);
const errorMessage = ref('');

const register = async () => {
  loading.value = true;
  error.value = false;

  try {
    const res = await api.post('/register', {
      username: username.value,
      email: email.value,
      password: password.value
    });

    if (res.data.success && res.data.token) {
      auth.setToken(res.data.token);
      router.push('/generator');
      return;
    }

    error.value = true;
    errorMessage.value = t('register.error');

  } catch (e) {
    error.value = true;

    if (e.response?.data?.error) {
      errorMessage.value = e.response.data.error;
    } else {
      errorMessage.value = t('register.error');
    }
  } finally {
    loading.value = false;
  }
};
</script>
