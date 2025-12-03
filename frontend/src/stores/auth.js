import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export const useAuthStore = defineStore('auth', () => {
  const token = ref(localStorage.getItem('token') || '');

  const isAuthenticated = computed(() => token.value.length > 0);

  const setToken = (newToken) => {
    token.value = newToken;
    localStorage.setItem('token', newToken);
  };

  const logout = () => {
    token.value = '';
    localStorage.removeItem('token');
  };

  return {
    token,
    isAuthenticated,
    setToken,
    logout
  };
});
