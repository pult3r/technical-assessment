// src/config/app.js
export default {
  apiBaseUrl: import.meta.env.VITE_API_URL || 'http://localhost:8080/api',
  jwtKey: 'technical_jwt_token'
};
