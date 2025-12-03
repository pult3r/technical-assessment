import { boot } from 'quasar/wrappers';
import { useAuthStore } from 'src/stores/auth';

export default boot(({ router }) => {
  router.beforeEach((to, from, next) => {
    const auth = useAuthStore();

    // Protected pages
    if (to.meta.requiresAuth && !auth.isAuthenticated) {
      return next('/');
    }

    // Already logged in → redirect to generator
    if (to.path === '/' && auth.isAuthenticated) {
      return next('/generator');
    }

    next();
  });
});
