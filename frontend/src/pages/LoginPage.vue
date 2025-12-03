<template>
  <q-page class="q-pa-md flex flex-center">
    <q-card class="q-pa-lg" style="width:420px;max-width:90%;">
      <q-card-section>
        <div class="text-h6">{{ t('login.title') }}</div>
      </q-card-section>

      <q-form @submit.prevent="submit">
        <q-card-section>
          <q-input filled v-model="username" :label="t('login.username')" />
          <q-input filled v-model="password" type="password" class="q-mt-md"
                   :label="t('login.password')" />
        </q-card-section>

        <q-card-section v-if="error" class="text-negative">
          {{ errorMessage }}
        </q-card-section>

        <q-card-actions align="between">
          <q-btn type="submit" color="primary" :label="t('login.button')" />

          <q-btn flat color="secondary" :label="t('login.go_register')"
                 @click="router.push('/register')" />
        </q-card-actions>
      </q-form>
    </q-card>
  </q-page>
</template>

<script setup>
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { api } from 'src/boot/axios';
import { useAuthStore } from 'src/stores/auth';
import { useRouter } from 'vue-router';

const username = ref('');
const password = ref('');
const error = ref(false);
const errorMessage = ref('');

const { t } = useI18n();
const router = useRouter();
const auth = useAuthStore();

const submit = async () => {
  error.value = false;

  if (!username.value || !password.value) {
    error.value = true;
    errorMessage.value = t('login.validation_required');
    return;
  }

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
