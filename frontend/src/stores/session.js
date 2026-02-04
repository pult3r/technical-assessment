import { defineStore } from 'pinia'

export const useSessionStore = defineStore('session', {
  state: () => ({
    authToken: localStorage.getItem('auth_token') || null,
    userId: localStorage.getItem('user_id') || null,
    username: localStorage.getItem('username') || null
  }),

  getters: {
    isAuthenticated(state) {
      return !!state.authToken
    }
  },

  actions: {
    setSession({ authToken, userId, username }) {
      this.authToken = authToken
      this.userId = userId
      this.username = username

      localStorage.setItem('auth_token', authToken)
      localStorage.setItem('user_id', userId)
      localStorage.setItem('username', username)
    },

    clearSession() {
      this.authToken = null
      this.userId = null
      this.username = null

      localStorage.removeItem('auth_token')
      localStorage.removeItem('user_id')
      localStorage.removeItem('username')
    }
  }
})
