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
        <strong>Properties:</strong> {{ filteredProperties.length }}
      </p>

      <q-list bordered class="q-mt-md">
        <q-item
          v-for="property in filteredProperties"
          :key="property.value"
          clickable
          @click="$router.push(`/app/property/${property.value}`)"
        >
          <q-item-section>
            <q-item-label>{{ property.label }}</q-item-label>
            <q-item-label caption>
              {{ property.city_name }}
              • Session started:
              {{ property.is_session_started === '1' ? 'Yes' : 'No' }}
            </q-item-label>
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

      // filters
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

    filteredProperties() {
      return this.properties.filter(property => {
        const matchCity =
          !this.selectedCity || property.city_id === this.selectedCity

        const matchAddress =
          !this.addressFilter ||
          property.label
            .toLowerCase()
            .includes(this.addressFilter.toLowerCase())

        return matchCity && matchAddress
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
    }
  },

  mounted() {
    this.loadDetails()
  }
}
</script>
