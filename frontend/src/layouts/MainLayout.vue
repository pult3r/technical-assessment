<template>
  <q-layout view="hHh lpR fFf">
    <!-- Use the reusable header component -->
    <AppHeader />

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script setup>
/**
 * MainLayout.vue
 * Simplified layout which imports AppHeader component.
 * Comments and code are in English.
 */

import AppHeader from 'src/components/AppHeader.vue';
</script>
