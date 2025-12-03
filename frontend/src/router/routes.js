// src/router/routes.js

import LoginPage from 'src/pages/LoginPage.vue';
import GeneratePdfPage from 'src/pages/GeneratePdfPage.vue';

export default [
  {
    path: '/',
    component: LoginPage
  },
  {
    path: '/generator',
    component: GeneratePdfPage
  }
];
