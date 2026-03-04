<template>
  <section class="weather-wrap weather-view">
    <div class="weather-shell">
      <WeatherTopBar
        :today-summary="todaySummary"
        :search-term="searchTerm"
        :loading="loading"
        :metric-mode="metricMode"
        :round="round"
        :format-clock="formatClock"
        @update:search-term="updateSearch"
        @set-metric="metricMode = $event"
      />

      <WeatherCityPicker
        :filtered-cities="filteredCities"
        :selected-city="selectedCity"
        :loading="loading"
        @update:selected-city="onSelectedCityChange"
        @reset-search="resetSearch"
        @refresh="fetchForecast"
      />

      <p v-if="loading" class="state">Betöltés...</p>
      <p v-else-if="error" class="state error">{{ error }}</p>
      <p v-else-if="!filteredCities.length" class="state">Nincs találat a keresésre.</p>

      <div v-else-if="dailyForecast.length" class="content-row">
        <WeatherCurrentCard
          :current-day="currentDay"
          :current-display-temp="currentDisplayTemp"
          :selected-city="selectedCity"
          :current-time="currentTime"
          :round="round"
        />

        <WeatherForecastRow
          :daily-forecast="dailyForecast"
          :metric-mode="metricMode"
          :round="round"
          :format-short-date="formatShortDate"
        />
      </div>
    </div>
  </section>
</template>

<script>
import { useLocationStore } from '@/stores/locationStore'
import { useWeatherForecast } from '@/composables/useWeatherForecast'
import WeatherTopBar from '@/components/Weather/WeatherTopBar.vue'
import WeatherCityPicker from '@/components/Weather/WeatherCityPicker.vue'
import WeatherCurrentCard from '@/components/Weather/WeatherCurrentCard.vue'
import WeatherForecastRow from '@/components/Weather/WeatherForecastRow.vue'

const weatherTools = useWeatherForecast()

export default {
  name: 'WeatherView',
  components: {
    WeatherTopBar,
    WeatherCityPicker,
    WeatherCurrentCard,
    WeatherForecastRow
  },
  data() {
    return {
      cities: [],
      selectedCity: '',
      searchTerm: '',
      dailyForecast: [],
      metricMode: 'temperature',
      loading: false,
      error: '',
      locationStore: useLocationStore()
    }
  },
  computed: {
    filteredCities() {
      const term = this.searchTerm.toLowerCase()
      if (!term) return this.cities
      return this.cities.filter((city) => {
        const inLabel = city.label.toLowerCase().includes(term)
        const inName = city.name.toLowerCase().includes(term)
        return inLabel || inName
      })
    },
    currentDay() {
      return this.dailyForecast[0] || null
    },
    currentDisplayTemp() {
      if (!this.currentDay) return 0
      return (this.currentDay.tempMin + this.currentDay.tempMax) / 2
    },
    currentTime() {
      return new Date().toLocaleTimeString('hu-HU', {
        hour: '2-digit',
        minute: '2-digit'
      })
    },
    todaySummary() {
      return this.dailyForecast[0] || null
    }
  },
  async mounted() {
    await this.loadLocations()
  },
  methods: {
    round(value) {
      return weatherTools.round(value)
    },
    formatShortDate(date) {
      return weatherTools.formatShortDate(date)
    },
    formatClock(value) {
      return weatherTools.formatClock(value)
    },
    updateSearch(value) {
      this.searchTerm = value.trimStart()
      this.syncSelectionWithFilter()
    },
    onSelectedCityChange(value) {
      this.selectedCity = value
      this.fetchForecast()
    },
    syncSelectionWithFilter() {
      if (!this.filteredCities.length) {
        this.selectedCity = ''
        return
      }

      const isSelectedVisible = this.filteredCities.some((city) => city.name === this.selectedCity)
      if (!isSelectedVisible) {
        this.selectedCity = this.filteredCities[0].name
      }
    },
    resetSearch() {
      this.searchTerm = ''
      if (this.cities.length && !this.selectedCity) {
        this.selectedCity = this.cities[0].name
      }
    },
    async loadLocations() {
      this.loading = true
      this.error = ''

      try {
        await this.locationStore.getAll()
        const rows = Array.isArray(this.locationStore.items) ? this.locationStore.items : []
        this.cities = rows
          .map((row) => weatherTools.normalizeLocation(row))
          .filter(Boolean)

        if (!this.cities.length) {
          throw new Error('A locations tábla üres vagy hiányos adatokat tartalmaz.')
        }

        this.selectedCity = this.cities[0].name
        await this.fetchForecast()
      } catch (err) {
        this.cities = []
        this.dailyForecast = []
        this.error = err?.message || 'Hiba történt a helyszínek lekérésénél.'
      } finally {
        this.loading = false
      }
    },
    getSelectedLocation() {
      return this.cities.find((city) => city.name === this.selectedCity) || null
    },
    async fetchForecast() {
      this.loading = true
      this.error = ''

      try {
        const cityData = this.getSelectedLocation()

        if (!cityData) {
          throw new Error('A kiválasztott helyszínhez nem találtam koordinátát.')
        }

        this.dailyForecast = await weatherTools.fetchForecastForLocation(cityData)
      } catch (err) {
        this.dailyForecast = []
        this.error = err?.message || 'Hiba történt az időjárás lekérésénél.'
      } finally {
        this.loading = false
      }
    }
  }
}
</script>
