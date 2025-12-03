<template>
  <q-btn flat dense icon="language">
    <q-menu>
      <q-list style="min-width: 120px">
        <q-item clickable v-close-popup @click="setLang('pl')">
          <q-item-section>Polski</q-item-section>
        </q-item>
        <q-item clickable v-close-popup @click="setLang('en')">
          <q-item-section>English</q-item-section>
        </q-item>
      </q-list>
    </q-menu>
  </q-btn>
</template>

<script setup>
import { i18n } from 'boot/i18n';

const setLang = (lang) => {
  i18n.global.locale.value = lang;
  localStorage.setItem('locale', lang);
};
</script>
