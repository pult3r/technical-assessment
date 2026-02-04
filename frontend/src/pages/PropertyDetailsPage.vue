<template>
  <q-page class="q-pa-md">
    <!-- BACK -->
    <q-btn
      flat
      icon="arrow_back"
      :label="$t('common.back')"
      class="q-mb-md"
      @click="$router.push('/app')"
    />

    <!-- 🔴 TEMP DEBUG -->
    <div class="q-mb-md text-negative">
      DEBUG LANG: {{ currentLang }}
    </div>

    <!-- STATES -->
    <div v-if="loading">
      {{ $t('common.loading') }}
    </div>

    <div v-else-if="error" class="text-negative">
      {{ $t('common.error') }}
    </div>

    <!-- CONTENT -->
    <div v-else>
      <div
        v-for="group in groups"
        :key="group.id + '-' + currentLang"
        class="q-mb-md"
      >
        <q-expansion-item
          :default-opened="group.expand === '1'"
        >
          <!-- ✅ REACTIVE HEADER -->
          <template #header>
            <div class="text-weight-medium">
              {{ groupLabel(group) }}
            </div>
          </template>

          <q-list bordered>
            <q-item
              v-for="todo in todosByGroup(group.id)"
              :key="todo.id"
            >
              <q-item-section>
                <q-item-label>
                  {{ todoLabel(todo) }}
                </q-item-label>
              </q-item-section>
            </q-item>
          </q-list>
        </q-expansion-item>
      </div>
    </div>
  </q-page>
</template>

<script>
import { useSessionStore } from 'src/stores/session'
import stepsApi from 'src/services/api/cleaning/steps'
import { i18n } from 'src/boot/i18n'

export default {
  name: 'PropertyDetailsPage',

  data() {
    return {
      loading: false,
      error: false,
      property: null,
      groups: [],
      todos: []
    }
  },

  computed: {
    session() {
      return useSessionStore()
    },

    propertyId() {
      return this.$route.params.propertyId
    },

    // 🔴 DEBUG SOURCE OF TRUTH
    currentLang() {
      return i18n.global.locale.value
    }
  },

  methods: {
    async loadSteps() {
      this.loading = true
      this.error = false

      try {
        const response = await stepsApi.fetchSteps({
          authToken: this.session.authToken,
          username: this.session.username,
          propertyId: this.propertyId
        })

        const data = response.data || {}

        this.property = data.property || null
        this.groups = data.groups || []
        this.todos = data.todos || []

      } catch (e) {
        console.error('LOAD STEPS ERROR', e)
        this.error = true
      } finally {
        this.loading = false
      }
    },

    todosByGroup(groupId) {
      return this.todos.filter(
        todo => todo.group_id === groupId
      )
    },

    groupLabel(group) {
      return (
        group[`name_${this.currentLang}`] ||
        group.name_en ||
        ''
      ).trim()
    },

    todoLabel(todo) {
      return (
        todo[`name_${this.currentLang}`] ||
        todo.name_en ||
        ''
      ).trim()
    }
  },

  mounted() {
    this.loadSteps()
  }
}
</script>
