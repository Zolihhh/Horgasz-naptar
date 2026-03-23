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

      <div v-if="loading" class="state loading-state" role="status" aria-live="polite">
        <div class="spinner-border loading-spinner" aria-hidden="true"></div>
        <span>Időjárás betöltése...</span>
      </div>
      <p v-else-if="error" class="state error">{{ error }}</p>
      <p v-else-if="!filteredCities.length" class="state">Nincs találat a keresésre.</p>

      <div v-else-if="dailyForecast.length" class="content-row row g-4 align-items-end">
        <WeatherCurrentCard
          class="col-12 col-xl-auto"
          :current-day="currentDay"
          :current-display-temp="currentDisplayTemp"
          :selected-city="selectedCity"
          :current-time="currentTime"
          :round="round"
        />

        <WeatherForecastRow
          class="col-12 col-xl"
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
import { useCityStore } from '@/stores/cityStore'
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
      currentTimestamp: Date.now(),
      metricMode: 'temperature',
      loading: false,
      error: '',
      currentTimeTimeoutId: null,
      currentTimeIntervalId: null,
      cityStore: useCityStore()
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
      return new Date(this.currentTimestamp).toLocaleTimeString('hu-HU', {
        hour: '2-digit',
        minute: '2-digit'
      })
    },
    todaySummary() {
      return this.dailyForecast[0] || null
    }
  },
  async mounted() {
    this.startClock()
    await this.loadCities()
  },
  beforeUnmount() {
    this.stopClock()
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
    updateCurrentTimestamp() {
      this.currentTimestamp = Date.now()
    },
    startClock() {
      this.stopClock()
      this.updateCurrentTimestamp()

      const now = new Date()
      const msUntilNextMinute =
        ((60 - now.getSeconds()) * 1000 - now.getMilliseconds()) || 60000

      this.currentTimeTimeoutId = window.setTimeout(() => {
        this.updateCurrentTimestamp()
        this.currentTimeIntervalId = window.setInterval(() => {
          this.updateCurrentTimestamp()
        }, 60000)
      }, msUntilNextMinute)
    },
    stopClock() {
      if (this.currentTimeTimeoutId) {
        window.clearTimeout(this.currentTimeTimeoutId)
        this.currentTimeTimeoutId = null
      }

      if (this.currentTimeIntervalId) {
        window.clearInterval(this.currentTimeIntervalId)
        this.currentTimeIntervalId = null
      }
    },
    async loadCities() {
      this.loading = true
      this.error = ''

      try {
        await this.cityStore.getAll()
        const rows = Array.isArray(this.cityStore.items) ? this.cityStore.items : []
        this.cities = rows
          .map((row) => weatherTools.normalizeCity(row))
          .filter(Boolean)

        if (!this.cities.length) {
          throw new Error('A cities tábla üres vagy hiányos adatokat tartalmaz.')
        }

        this.selectedCity = this.cities[0].name
        await this.fetchForecast()
      } catch (err) {
        this.cities = []
        this.dailyForecast = []
        this.error = err?.message || 'Hiba történt a városok lekérésénél.'
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
