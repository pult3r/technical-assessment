// src/boot/axios.js
import { boot } from 'quasar/wrappers';
import axios from 'axios';
import appConfig from 'src/config/app';
import { useAuthStore } from 'src/stores/auth';

const api = axios.create({
  baseURL: appConfig.apiBaseUrl
});

api.interceptors.request.use((config) => {
  try {
    const auth = useAuthStore();
    if (auth.token) {
      config.headers.Authorization = `Bearer ${auth.token}`;
    }
  } catch {
    // if Pinia not ready yet - ignore silently
  }
  return config;
});

export default boot(({ app }) => {
  app.config.globalProperties.$api = api;
});

export { api };
