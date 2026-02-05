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

    <!-- STATES -->
    <div v-if="loading">
      {{ $t('common.loading') }}
    </div>

    <div v-else-if="error" class="text-negative">
      {{ $t('common.error') }}
    </div>

    <!-- CONTENT -->
    <div v-else>

      <!-- 🔵 GLOBAL PROGRESS -->
      <div class="q-mb-md">
        <div class="row items-center q-mb-xs">
          <div class="col text-weight-medium">
            {{ $t('property.progress') }}
          </div>
          <div class="col-auto text-caption text-grey-7">
            {{ completedTotal }} / {{ totalTodos }}
          </div>
        </div>

        <q-linear-progress
          :value="globalProgress"
          rounded
          size="10px"
          color="primary"
        />
      </div>

      <!-- 🧾 CLEANING SUMMARY -->
      <div class="row q-col-gutter-sm q-mb-lg">
        <div class="col-4">
          <q-card flat bordered class="q-pa-sm text-center">
            <div class="text-caption">
              {{ $t('property.summary.completed') }}
            </div>
            <div class="text-h6">
              {{ summaryCompleted }}
            </div>
          </q-card>
        </div>

        <div class="col-4">
          <q-card flat bordered class="q-pa-sm text-center">
            <div class="text-caption">
              {{ $t('property.summary.pending') }}
            </div>
            <div class="text-h6">
              {{ summaryPending }}
            </div>
          </q-card>
        </div>

        <div
          v-if="summaryReclean > 0"
          class="col-4"
        >
          <q-card flat bordered class="q-pa-sm text-center">
            <div class="text-caption">
              {{ $t('property.summary.reclean') }}
            </div>
            <div class="text-h6 text-orange">
              {{ summaryReclean }}
            </div>
          </q-card>
        </div>
      </div>

      <!-- GROUPS -->
      <div
        v-for="group in groups"
        :key="group.id + '-' + currentLang"
        class="q-mb-lg"
      >
        <q-expansion-item
          :default-opened="group.id === autoExpandGroupId"
          expand-separator
          expand-icon="none"
        >
          <!-- HEADER -->
          <template #header="{ expanded }">
            <div class="row items-center full-width no-wrap">
              <div class="col text-weight-medium row items-center">
                {{ groupLabel(group) }}

                <q-icon
                  v-if="groupHasReclean(group.id)"
                  name="warning"
                  color="orange"
                  size="18px"
                  class="q-ml-xs"
                />
              </div>

              <div class="col-auto text-caption text-grey-7 q-mr-sm">
                {{ completedCount(group.id) }} / {{ totalCount(group.id) }}
              </div>

              <div class="col-auto">
                <q-icon
                  :name="expanded ? 'expand_less' : 'expand_more'"
                  size="24px"
                />
              </div>
            </div>
          </template>

          <!-- GROUP PROGRESS -->
          <div class="q-px-md q-pt-sm">
            <q-linear-progress
              :value="groupProgress(group.id)"
              rounded
              size="8px"
              color="primary"
            />
          </div>

          <!-- TODOS -->
          <q-list bordered class="q-mt-sm">
            <q-item
              v-for="todo in todosByGroup(group.id)"
              :key="todo.id"
            >
              <q-item-section avatar>
                <q-checkbox
                  :model-value="todo.answer === '1'"
                  :disable="todo._saving === true"
                  @update:model-value="val => toggleTodo(todo, val)"
                />
              </q-item-section>

              <q-item-section>
                <q-item-label>
                  {{ todoLabel(todo) }}
                </q-item-label>

                <q-chip
                  v-if="todo.reclean_required === '1'"
                  dense
                  color="orange-2"
                  text-color="orange-9"
                  size="sm"
                  class="q-mt-xs"
                >
                  RE-CLEAN
                </q-chip>
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
import answerApi from 'src/services/api/cleaning/answer'
import { i18n } from 'src/boot/i18n'

export default {
  name: 'PropertyDetailsPage',

  data() {
    return {
      loading: false,
      error: false,
      property: null,
      groups: [],
      todos: [],
      autoExpandGroupId: null
    }
  },

  computed: {
    session() {
      return useSessionStore()
    },

    propertyId() {
      return this.$route.params.propertyId
    },

    currentLang() {
      return i18n.global.locale.value
    },

    totalTodos() {
      return this.todos.length
    },

    completedTotal() {
      return this.todos.filter(this.isTodoCompleted).length
    },

    globalProgress() {
      if (this.totalTodos === 0) return 0
      return this.completedTotal / this.totalTodos
    },

    /* SUMMARY */
    summaryCompleted() {
      return this.todos.filter(this.isTodoCompleted).length
    },

    summaryPending() {
      return this.todos.filter(t => t.answer !== '1').length
    },

    summaryReclean() {
      return this.todos.filter(t => t.reclean_required === '1').length
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

        this.todos = (data.todos || []).map(todo => ({
          ...todo,
          _saving: false
        }))

        this.detectAutoExpandGroup()
      } catch {
        this.error = true
      } finally {
        this.loading = false
      }
    },

    detectAutoExpandGroup() {
      // priority: reclean > pending
      const problematicTodo =
        this.todos.find(t => t.reclean_required === '1') ||
        this.todos.find(t => t.answer !== '1')

      if (!problematicTodo) return

      this.autoExpandGroupId = problematicTodo.group_id
    },

    todosByGroup(groupId) {
      return this.todos.filter(todo => todo.group_id === groupId)
    },

    isTodoCompleted(todo) {
      return todo.answer === '1' && todo.reclean_required !== '1'
    },

    totalCount(groupId) {
      return this.todosByGroup(groupId).length
    },

    completedCount(groupId) {
      return this.todosByGroup(groupId)
        .filter(this.isTodoCompleted).length
    },

    groupProgress(groupId) {
      const total = this.totalCount(groupId)
      if (total === 0) return 0
      return this.completedCount(groupId) / total
    },

    groupHasReclean(groupId) {
      return this.todosByGroup(groupId).some(
        todo => todo.reclean_required === '1'
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
    },

    async toggleTodo(todo, checked) {
      const previous = todo.answer
      const nextValue = checked ? '1' : '0'

      todo.answer = nextValue
      todo._saving = true

      try {
        await answerApi.submitAnswer({
          authToken: this.session.authToken,
          username: this.session.username,
          propertyId: this.propertyId,
          stepId: todo.id,
          answer: nextValue
        })
      } catch {
        todo.answer = previous
        this.$q.notify({
          type: 'negative',
          message: this.$t('common.error')
        })
      } finally {
        todo._saving = false
      }
    }
  },

  mounted() {
    this.loadSteps()
  }
}
</script>
