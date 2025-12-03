// src/router/index.js
import { createRouter, createWebHashHistory } from 'vue-router';

import LoginPage from 'src/pages/LoginPage.vue';
import RegisterPage from 'src/pages/RegisterPage.vue';
import GeneratePdfPage from 'src/pages/GeneratePdfPage.vue';
import MainLayout from 'src/layouts/MainLayout.vue';

import { useAuthStore } from 'src/stores/auth';

const routes = [
  // --- LOGIN ---
  {
    path: '/',
    name: 'login',
    component: LoginPage
  },

  // --- REGISTER ---
  {
    path: '/register',
    name: 'register',
    component: RegisterPage
  },

  // --- PROTECTED AREA ---
  {
    path: '/generator',
    component: MainLayout,
    children: [
      {
        path: '',
        name: 'generator',
        component: GeneratePdfPage,
        meta: { requiresAuth: true }
      }
    ]
  }
];

const router = createRouter({
  history: createWebHashHistory(),
  routes
});

// ---------------------------------------------------
// GLOBAL AUTH GUARD
// ---------------------------------------------------
router.beforeEach((to, from, next) => {
  const auth = useAuthStore();

  // 1. Jeśli wymaga autoryzacji, a user nie jest zalogowany → redirect
  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return next('/');
  }

  // 2. Jeśli user jest zalogowany i wchodzi na "/" → redirect do generatora
  if (to.path === '/' && auth.isAuthenticated) {
    return next('/generator');
  }

  next();
});

export default router;
