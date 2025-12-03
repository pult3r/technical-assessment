<template>
  <q-page class="q-pa-md">
    <div class="row items-center justify-between q-mb-md">
      <div class="text-h6">{{ t('generator.title') }}</div>

      <div class="row items-center q-gutter-sm">
        <q-select
          dense
          filled
          v-model="locale"
          :options="langs"
          @update:model-value="changeLocale"
          style="width:120px"
        />

        <q-btn flat color="negative" :label="t('common.logout')" @click="logout" />
      </div>
    </div>

    <q-card class="q-pa-md">
      <auto-expand-input
        v-model="text"
        :placeholder="t('generator.placeholder')"
      />

      <div class="q-mt-md">
        <q-btn color="primary" :label="t('generator.button')" @click="submit" />
      </div>

      <div v-if="error" class="text-negative q-mt-md">{{ errorMessage }}</div>
    </q-card>

    <q-dialog v-model="showDialog">
      <q-card style="width:90vw;height:90vh;">
        <iframe :src="pdfUrl" style="width:100%;height:100%;border:none;"></iframe>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup>
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { api } from 'src/boot/axios';
import { useAuthStore } from 'src/stores/auth';
import AutoExpandInput from 'src/components/AutoExpandInput.vue';

const { locale, t } = useI18n();
const langs = [
  { label: 'PL', value: 'pl' },
  { label: 'EN', value: 'en' }
];

const text = ref('');
const error = ref(false);
const errorMessage = ref('');
const showDialog = ref(false);
const pdfUrl = ref('');

const auth = useAuthStore();

const changeLocale = val => {
  locale.value = val;
  localStorage.setItem('locale', val);
};

const logout = () => {
  auth.logout();
  window.location = '/';
};

const submit = async () => {
  error.value = false;

  if (!text.value.trim()) {
    error.value = true;
    errorMessage.value = t('generator.validation_required');
    return;
  }

  try {
    const res = await api.post('/generate-pdf', { text: text.value });

    pdfUrl.value = res.data.pdf_url;
    showDialog.value = true;

  } catch {
    error.value = true;
    errorMessage.value = t('generator.error');
  }
};
</script>
