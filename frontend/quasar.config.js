// Configuration for your app
// https://v2.quasar.dev/quasar-cli-vite/quasar-config-file

import { defineConfig } from '#q-app/wrappers'

export default defineConfig((/* ctx */) => {
  return {
    // Boot files: executed before app start
    // We load axios and i18n here
    boot: [
        'pinia',      
        'i18n',
        'axios',
        'router-guard'
    ],

    // CSS
    css: ['app.scss'],

    // Quasar extras
    extras: [
      'roboto-font',
      'material-icons'
    ],

    // Build configuration
    build: {
      target: {
        browser: ['es2022', 'firefox115', 'chrome115', 'safari14'],
        node: 'node20'
      },

      vueRouterMode: 'hash',

      vitePlugins: [
        [
          'vite-plugin-checker',
          {
            eslint: {
              lintCommand: 'eslint -c ./eslint.config.js "./src*/**/*.{js,mjs,cjs,vue}"',
              useFlatConfig: true
            }
          },
          { server: false }
        ]
      ]
    },

    // Dev server configuration
    devServer: {
      open: true
    },

    // Framework: components, plugins, language pack etc.
    framework: {
      config: {},

      // Quasar plugins that we use
      plugins: [
        'Dialog',
        'LocalStorage',
        'Notify'
      ]
    },

    animations: [],

    // SSR configuration
    ssr: {
      prodPort: 3000,

      middlewares: [
        'render'
      ],

      pwa: false
    },

    // PWA
    pwa: {
      workboxMode: 'GenerateSW'
    },

    // Cordova
    cordova: {},

    // Capacitor
    capacitor: {
      hideSplashscreen: true
    },

    // Electron
    electron: {
      preloadScripts: ['electron-preload'],
      inspectPort: 5858,
      bundler: 'packager',

      packager: {},

      builder: {
        appId: 'frontend'
      }
    },

    // Browser extension
    bex: {
      extraScripts: []
    }
  }
})
