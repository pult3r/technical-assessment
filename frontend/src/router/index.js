import { createRouter, createWebHashHistory } from 'vue-router';
import LoginPage from 'src/pages/LoginPage.vue';
import GeneratePdfPage from 'src/pages/GeneratePdfPage.vue';
import MainLayout from 'src/layouts/MainLayout.vue';
import { useAuthStore } from 'src/stores/auth';

const routes = [
  {
    path: '/',
    name: 'login',
    component: LoginPage
  },

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

// Auth guard
router.beforeEach((to, from, next) => {
  const auth = useAuthStore();

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return next('/');
  }

  if (to.path === '/' && auth.isAuthenticated) {
    return next('/generator');
  }

  next();
});

export default router;
