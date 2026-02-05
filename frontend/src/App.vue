<template>
  <q-layout view="lHh Lpr lFf">
    <q-header elevated class="app-header">
      <q-toolbar class="app-toolbar">

        <!-- LOGO ONLY -->
        <img
          src="~assets/student-cribs-logo.svg"
          alt="Student Cribs"
          class="header-logo"
        />

        <q-space />

        <!-- LANGUAGE SWITCHER -->
        <LanguageSwitcher />
      </q-toolbar>
    </q-header>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script setup>
import LanguageSwitcher from 'src/components/LanguageSwitcher.vue'
</script>

<style scoped>
/* HEADER BACKGROUND + BORDER */
.app-header {
  background: #ffffff;
  color: #000;
  border-bottom: 1px solid #e0e0e0;
}

/* TOOLBAR SPACING */
.app-toolbar {
  min-height: 56px; /* mobile friendly */
  padding-left: 16px;
  padding-right: 16px;
}

/* LOGO */
.header-logo {
  height: 28px;
  width: auto;
  display: block;
}
</style>
