<template>
  <q-page padding>

    <h3>{{ t('generator.title') }}</h3>

    <q-input
      v-model="text"
      :placeholder="t('generator.placeholder')"
      type="textarea"
      autogrow
      outlined
      class="q-mb-md"
    />

    <q-btn
      color="primary"
      :label="t('generator.button')"
      @click="send"
      :loading="loading"
    />

    <q-banner
      v-if="error"
      class="q-mt-md"
      type="negative"
    >
      {{ errorMessage }}
    </q-banner>

    <q-dialog v-model="showDialog">
      <q-card style="width: 80vw; max-width: 900px">
        <q-card-section>
          <iframe
            :src="pdfUrl"
            style="width: 100%; height: 80vh"
          ></iframe>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="OK" color="primary" v-close-popup />
        </q-card-actions>
      </q-card>
    </q-dialog>

  </q-page>
</template>

<script setup>
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { api } from 'src/boot/axios';
import { useAuthStore } from 'src/stores/auth';

const { t } = useI18n();
const auth = useAuthStore();

const text = ref('');
const loading = ref(false);
const error = ref(false);
const errorMessage = ref('');
const showDialog = ref(false);
const pdfUrl = ref('');

const send = async () => {
  error.value = false;

  if (!text.value || text.value.trim().length === 0) {
    error.value = true;
    errorMessage.value = t('generator.validation_required');
    return;
  }

  loading.value = true;

  try {
    const res = await api.post('/generate-pdf', { text: text.value });

    if (res.data.success) {
      pdfUrl.value = res.data.pdf_url;
      showDialog.value = true;
    } else {
      error.value = true;
      errorMessage.value = res.data.error || t('generator.error');
    }

  } catch (e) {
    error.value = true;

    // Backend returns translated error message
    errorMessage.value = e?.response?.data?.error || t('generator.error');

    // 🔥 Jeśli token nieprawidłowy → automatyczny logout
    if (e?.response?.status === 401 || e?.response?.status === 403) {
      auth.logout();
    }

  } finally {
    loading.value = false;
  }
};
</script>
