/**
 * register-service-worker.js
 * Required by Quasar PWA mode (GenerateSW).
 * Handles service worker registration.
 */

if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker
      .register('/service-worker.js')
      .then(registration => {
        console.log('[PWA] Service Worker registered:', registration)
      })
      .catch(error => {
        console.error('[PWA] Service Worker registration failed:', error)
      })
  })
}
