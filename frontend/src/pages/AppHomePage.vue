<template>
  <q-page class="q-pa-md">
    <h5>Cleaning App</h5>

    <p>
      Logged in as <strong>{{ username }}</strong>
    </p>

    <q-btn
      color="negative"
      label="Logout"
      class="q-mb-md"
      @click="logout"
    />

    <q-separator class="q-my-md" />

    <!-- FILTERS -->
    <div class="row q-col-gutter-md q-mb-md">
      <div class="col-12 col-md-6">
        <q-select
          v-model="selectedCity"
          :options="cityOptions"
          label="Filter by city"
          clearable
          emit-value
          map-options
        />
      </div>

      <div class="col-12 col-md-6">
        <q-input
          v-model="addressFilter"
          label="Filter by address"
          clearable
        />
      </div>
    </div>

    <div v-if="loading">
      Loading apartments…
    </div>

    <div v-else-if="error" class="text-negative">
      Failed to load data.
    </div>

    <div v-else>
      <p>
        <strong>Cities:</strong> {{ cities.length }}<br />
        <strong>Properties:</strong> {{ filteredAndSortedProperties.length }}
      </p>

      <q-list bordered class="q-mt-md">
        <q-item
          v-for="property in filteredAndSortedProperties"
          :key="property.value"
          clickable
          @click="$router.push(`/app/property/${property.value}`)"
        >
          <q-item-section>
            <q-item-label>
              {{ property.label }}
            </q-item-label>

            <q-item-label caption>
              {{ property.city_name }}
            </q-item-label>
          </q-item-section>

          <!-- SESSION STATUS + TIME -->
          <q-item-section side>
            <div class="column items-end">
              <q-chip
                dense
                :color="sessionStatusColor(property)"
                text-color="white"
              >
                {{ $t(sessionStatusLabel(property)) }}
              </q-chip>

              <div
                v-if="sessionDuration(property)"
                class="text-caption text-grey-7 q-mt-xs"
              >
                {{ sessionDuration(property) }}
              </div>
            </div>
          </q-item-section>
        </q-item>
      </q-list>
    </div>
  </q-page>
</template>

<script>
import { useSessionStore } from 'src/stores/session'
import detailsApi from 'src/services/api/cleaning/details'

export default {
  name: 'AppHomePage',

  data() {
    return {
      loading: false,
      error: false,
      cities: [],
      properties: [],
      selectedCity: null,
      addressFilter: ''
    }
  },

  computed: {
    session() {
      return useSessionStore()
    },

    username() {
      return this.session.username
    },

    cityOptions() {
      return this.cities
    },

    filteredAndSortedProperties() {
      const filtered = this.properties.filter(property => {
        const matchCity =
          !this.selectedCity || property.city_id === this.selectedCity

        const matchAddress =
          !this.addressFilter ||
          property.label
            .toLowerCase()
            .includes(this.addressFilter.toLowerCase())

        return matchCity && matchAddress
      })

      return filtered.sort((a, b) => {
        if (a.is_session_started === '1' && b.is_session_started !== '1') return -1
        if (a.is_session_started !== '1' && b.is_session_started === '1') return 1
        return 0
      })
    }
  },

  methods: {
    async loadDetails() {
      this.loading = true
      this.error = false

      try {
        const response = await detailsApi.fetchDetails({
          authToken: this.session.authToken,
          username: this.session.username
        })

        this.cities = response.data?.cities || []
        this.properties = response.data?.properties || []
      } catch {
        this.error = true
      } finally {
        this.loading = false
      }
    },

    logout() {
      this.session.clearSession()
      this.$router.push('/')
    },

    sessionStatusLabel(property) {
      return property.is_session_started === '1'
        ? 'property.session.in_progress'
        : 'property.session.not_started'
    },

    sessionStatusColor(property) {
      if (property.is_session_started !== '1') return 'grey'

      const minutes = this.sessionMinutes(property)
      if (minutes < 30) return 'blue'
      if (minutes < 90) return 'amber'
      return 'red'
    },

    sessionMinutes(property) {
      if (!property.session_started_at) return null
      const start = new Date(property.session_started_at.replace(' ', 'T'))
      const now = new Date()
      return Math.floor((now - start) / 60000)
    },

    sessionDuration(property) {
      const minutes = this.sessionMinutes(property)
      if (!minutes) return null

      if (minutes < 60) return `${minutes}m ago`
      const h = Math.floor(minutes / 60)
      const m = minutes % 60
      return `${h}h ${m}m ago`
    }
  },

  mounted() {
    this.loadDetails()
  }
}
</script>
