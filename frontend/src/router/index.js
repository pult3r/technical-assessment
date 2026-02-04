import { createRouter, createWebHashHistory } from 'vue-router'
import { useSessionStore } from 'src/stores/session'

import LoginPage from 'src/pages/LoginPage.vue'
import RegisterPage from 'src/pages/RegisterPage.vue'
import AppHomePage from 'src/pages/AppHomePage.vue'
import PropertyDetailsPage from 'src/pages/PropertyDetailsPage.vue'

const routes = [
  {
    path: '/',
    name: 'login',
    component: LoginPage,
    meta: { public: true }
  },
  {
    path: '/register',
    name: 'register',
    component: RegisterPage,
    meta: { public: true }
  },
  {
    path: '/app',
    name: 'app',
    component: AppHomePage,
    meta: { requiresAuth: true }
  },
  {
    path: '/app/property/:propertyId',
    name: 'property-details',
    component: PropertyDetailsPage,
    meta: { requiresAuth: true }
  }
]

const router = createRouter({
  history: createWebHashHistory(),
  routes
})

// -------------------------------
// GLOBAL AUTH GUARD
// -------------------------------
router.beforeEach((to, from, next) => {
  const sessionStore = useSessionStore()

  if (to.meta.requiresAuth && !sessionStore.isAuthenticated) {
    return next({ name: 'login' })
  }

  if (to.meta.public && sessionStore.isAuthenticated) {
    return next({ name: 'app' })
  }

  next()
})

export default router
