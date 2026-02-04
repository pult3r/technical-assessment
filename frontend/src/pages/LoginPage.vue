<template>
  <q-page class="q-pa-md flex flex-center">
    <q-card class="q-pa-lg" style="width:420px;max-width:90%;">
      <q-card-section>
        <div class="text-h6">{{ $t('login.title') }}</div>
      </q-card-section>

      <q-form @submit.prevent="submit">
        <q-card-section>
          <q-input
            filled
            v-model="username"
            :label="$t('login.username')"
          />

          <q-input
            filled
            v-model="password"
            type="password"
            class="q-mt-md"
            :label="$t('login.password')"
          />
        </q-card-section>

        <q-card-section v-if="error" class="text-negative">
          {{ errorMessage }}
        </q-card-section>

        <q-card-actions align="between">
          <q-btn
            type="submit"
            color="primary"
            :label="$t('login.button')"
            :loading="loading"
          />

          <q-btn
            flat
            color="secondary"
            :label="$t('login.go_register')"
            @click="$router.push('/register')"
          />
        </q-card-actions>
      </q-form>
    </q-card>
  </q-page>
</template>

<script>
import authApi from 'src/services/api/cleaning/auth'
import { useSessionStore } from 'src/stores/session'

export default {
  name: 'LoginPage',

  data() {
    return {
      username: '',
      password: '',
      loading: false,
      error: false,
      errorMessage: ''
    }
  },

  methods: {
    async submit() {
      this.error = false

      if (!this.username || !this.password) {
        this.error = true
        this.errorMessage = this.$t('login.validation_required')
        return
      }

      this.loading = true

      try {
        const response = await authApi.login(this.username, this.password)

        const sessionStore = useSessionStore()

        sessionStore.setSession({
          authToken: response.auth_token,
          userId: response.data.id,
          username: this.username
        })

        // ✅ REDIRECT AFTER SUCCESSFUL LOGIN
        this.$router.push('/app')

      } catch {
        this.error = true
        this.errorMessage = this.$t('login.error')
      } finally {
        this.loading = false
      }
    }
  }
}
</script>
