<template>
  <q-page class="q-pa-md">
    <q-btn
      flat
      icon="arrow_back"
      :label="$t('common.back')"
      class="q-mb-md"
      @click="$router.push('/app')"
    />

    <div v-if="loading">
      {{ $t('common.loading') }}
    </div>

    <div v-else-if="error" class="text-negative">
      {{ $t('common.error') }}
    </div>

    <div v-else>
      <q-card flat bordered class="q-pa-md q-mb-lg">
        <div class="text-subtitle1 text-weight-medium">
          {{ property?.address }}
        </div>

        <div class="q-mt-sm">
          <q-chip
            dense
            :color="propertyStatusColor"
            text-color="white"
            icon="home"
          >
            {{ $t(propertyStatusLabel) }}
          </q-chip>
        </div>
      </q-card>

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
          color="transparent"
          track-color="transparent"
          :style="progressStyle(globalProgress)"
          class="progress-bar"
        />

        <div v-if="globalProgress === 1" class="q-mt-sm">
          <q-chip dense color="green" text-color="white" icon="check_circle">
            COMPLETED
          </q-chip>
        </div>
      </div>

      <div class="row q-col-gutter-sm q-mb-lg">
        <div class="col-4">
          <q-card flat bordered class="q-pa-sm text-center">
            <div class="text-caption">{{ $t('property.summary.completed') }}</div>
            <div class="text-h6">{{ summaryCompleted }}</div>
          </q-card>
        </div>

        <div class="col-4">
          <q-card flat bordered class="q-pa-sm text-center">
            <div class="text-caption">{{ $t('property.summary.pending') }}</div>
            <div class="text-h6">{{ summaryPending }}</div>
          </q-card>
        </div>

        <div v-if="summaryReclean > 0" class="col-4">
          <q-card flat bordered class="q-pa-sm text-center">
            <div class="text-caption">{{ $t('property.summary.reclean') }}</div>
            <div class="text-h6 text-orange">{{ summaryReclean }}</div>
          </q-card>
        </div>
      </div>

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

          <div class="q-px-md q-pt-sm">
            <q-linear-progress
              :value="groupProgress(group.id)"
              rounded
              size="8px"
              color="transparent"
              track-color="transparent"
              :style="progressStyle(groupProgress(group.id))"
              class="progress-bar"
            />
          </div>

          <q-list bordered class="q-mt-sm">
            <q-item v-for="todo in todosByGroup(group.id)" :key="todo.id">
              <q-item-section avatar>
                <q-checkbox
                  :model-value="todo.answer === '1'"
                  :disable="todo._saving === true"
                  @update:model-value="val => toggleTodo(todo, val)"
                />
              </q-item-section>

              <q-item-section>
                <q-item-label>{{ todoLabel(todo) }}</q-item-label>

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
    session() { return useSessionStore() },
    propertyId() { return this.$route.params.propertyId },
    currentLang() { return i18n.global.locale.value },
    totalTodos() { return this.todos.length },
    completedTotal() {
      return this.todos.filter(this.isTodoCompleted).length
    },
    globalProgress() {
      return this.totalTodos === 0 ? 0 : this.completedTotal / this.totalTodos
    },
    summaryCompleted() {
      return this.todos.filter(this.isTodoCompleted).length
    },
    summaryPending() {
      return this.todos.filter(t => t.answer !== '1').length
    },
    summaryReclean() {
      return this.todos.filter(t => t.reclean_required === '1').length
    },
    propertyStatusLabel() {
      if (this.summaryReclean > 0) return 'property.status.reclean'
      if (this.completedTotal === 0) return 'property.status.not_started'
      if (this.completedTotal < this.totalTodos) return 'property.status.in_progress'
      return 'property.status.completed'
    },
    propertyStatusColor() {
      if (this.summaryReclean > 0) return 'orange'
      if (this.completedTotal === 0) return 'grey'
      if (this.completedTotal < this.totalTodos) return 'blue'
      return 'green'
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
        this.todos = (data.todos || []).map(todo => ({ ...todo, _saving: false }))
        this.detectAutoExpandGroup()
      } catch {
        this.error = true
      } finally {
        this.loading = false
      }
    },
    detectAutoExpandGroup() {
      const t =
        this.todos.find(t => t.reclean_required === '1') ||
        this.todos.find(t => t.answer !== '1')
      if (t) this.autoExpandGroupId = t.group_id
    },
    todosByGroup(id) {
      return this.todos.filter(t => t.group_id === id)
    },
    isTodoCompleted(todo) {
      return todo.answer === '1' && todo.reclean_required !== '1'
    },
    totalCount(id) {
      return this.todosByGroup(id).length
    },
    completedCount(id) {
      return this.todosByGroup(id).filter(this.isTodoCompleted).length
    },
    groupProgress(id) {
      const t = this.totalCount(id)
      return t === 0 ? 0 : this.completedCount(id) / t
    },
    groupHasReclean(id) {
      return this.todosByGroup(id).some(t => t.reclean_required === '1')
    },
    groupLabel(g) {
      return (g[`name_${this.currentLang}`] || g.name_en || '').trim()
    },
    todoLabel(t) {
      return (t[`name_${this.currentLang}`] || t.name_en || '').trim()
    },
    progressStyle(value) {
      const s = { r: 255, g: 167, b: 38 }
      const e = { r: 76, g: 175, b: 80 }
      const r = Math.round(s.r + (e.r - s.r) * value)
      const g = Math.round(s.g + (e.g - s.g) * value)
      const b = Math.round(s.b + (e.b - s.b) * value)
      return {
        background: `linear-gradient(90deg,
          rgb(${r}, ${g}, ${b}) ${value * 100}%,
          #e0e0e0 ${value * 100}%)`
      }
    },
    async toggleTodo(todo, checked) {
      const prev = todo.answer
      todo.answer = checked ? '1' : '0'
      todo._saving = true
      try {
        await answerApi.submitAnswer({
          authToken: this.session.authToken,
          username: this.session.username,
          propertyId: this.propertyId,
          stepId: todo.id,
          answer: todo.answer
        })
      } catch {
        todo.answer = prev
        this.$q.notify({ type: 'negative', message: this.$t('common.error') })
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
