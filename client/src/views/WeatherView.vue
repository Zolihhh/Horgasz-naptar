<template>
  <section class="weather-wrap">
    <div class="weather-shell">
      <div class="top-row">
        <div class="astro" v-if="todaySummary">
          <span>{{ formatClock(todaySummary.sunrise) }} Napkelte</span>
          <span>{{ formatClock(todaySummary.sunset) }} Napnyugta</span>
          <span>{{ round(todaySummary.cloudCover) }}% Felho</span>
        </div>

        <div class="search-pill">
          <i class="bi bi-geo-alt-fill"></i>
          <input
            v-model.trim="searchTerm"
            type="text"
            placeholder="Kereses varosra..."
            :disabled="loading"
            @input="syncSelectionWithFilter"
          />
          <i class="bi bi-search"></i>
        </div>

        <div class="metric-switch">
          <button
            type="button"
            :class="{ active: metricMode === 'temperature' }"
            @click="metricMode = 'temperature'"
          >
            Homerseklet
          </button>
          <button
            type="button"
            :class="{ active: metricMode === 'precipitation' }"
            @click="metricMode = 'precipitation'"
          >
            Csapadek
          </button>
        </div>
      </div>

      <div class="picker-row">
        <select v-model="selectedCity" @change="fetchForecast" :disabled="loading || !filteredCities.length">
          <option v-for="city in filteredCities" :key="city.id" :value="city.name">
            {{ city.label }}
          </option>
        </select>
        <button type="button" class="ghost-btn" :disabled="loading" @click="resetSearch">Kereses torlese</button>
        <button type="button" class="ghost-btn" :disabled="loading" @click="fetchForecast">Frissites</button>
      </div>

      <p v-if="loading" class="state">Betoltes...</p>
      <p v-else-if="error" class="state error">{{ error }}</p>
      <p v-else-if="!filteredCities.length" class="state">Nincs talalat a keresesre.</p>

      <div v-else-if="dailyForecast.length" class="content-row">
        <article class="current-card">
          <img :src="currentDay.icon" :alt="currentDay.label" class="current-icon" />
          <div class="current-temp">{{ round(currentDisplayTemp) }}&deg;</div>
          <div class="current-city">{{ selectedCity }}</div>
          <div class="current-time">{{ currentTime }}</div>

          <div class="current-meta">
            <div>
              <strong>{{ round(currentDay.tempMin) }}&deg;</strong>
              <span>Minimum</span>
            </div>
            <div>
              <strong>{{ round(currentDay.tempMax) }}&deg;</strong>
              <span>Maximum</span>
            </div>
          </div>

          <p class="wind-current">Szel: {{ round(currentDay.windMax) }} km/h</p>
        </article>

        <div class="forecast-row">
          <article v-for="day in dailyForecast" :key="day.date" class="mini-card">
            <p class="mini-day">{{ formatShortDate(day.date) }}</p>
            <img :src="day.icon" :alt="day.label" class="mini-icon" />

            <template v-if="metricMode === 'temperature'">
              <p class="mini-main">{{ round((day.tempMin + day.tempMax) / 2) }}&deg;</p>
              <p class="mini-sub">min {{ round(day.tempMin) }} / max {{ round(day.tempMax) }}</p>
            </template>
            <template v-else>
              <p class="mini-main">{{ round(day.precipitation) }} mm</p>
              <p class="mini-sub">szel {{ round(day.windMax) }} km/h</p>
            </template>
          </article>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import axios from 'axios'
import { useLocationStore } from '@/stores/locationStore'

const WEATHER_VISUALS = {
  clear: {
    label: 'Napos',
    icon: 'https://openweathermap.org/img/wn/01d@2x.png'
  },
  cloud: {
    label: 'Felhos',
    icon: 'https://openweathermap.org/img/wn/03d@2x.png'
  },
  rain: {
    label: 'Esos',
    icon: 'https://openweathermap.org/img/wn/10d@2x.png'
  },
  snow: {
    label: 'Havas',
    icon: 'https://openweathermap.org/img/wn/13d@2x.png'
  },
  storm: {
    label: 'Zivataros',
    icon: 'https://openweathermap.org/img/wn/11d@2x.png'
  },
  fog: {
    label: 'Kodos',
    icon: 'https://openweathermap.org/img/wn/50d@2x.png'
  }
}

export default {
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
      return Math.round(Number(value) * 10) / 10
    },
    formatShortDate(date) {
      return new Date(date).toLocaleDateString('hu-HU', {
        month: 'numeric',
        day: 'numeric'
      })
    },
    formatClock(value) {
      if (!value) return '--:--'
      const date = new Date(value)
      if (Number.isNaN(date.getTime())) return '--:--'
      return date.toLocaleTimeString('hu-HU', {
        hour: '2-digit',
        minute: '2-digit'
      })
    },
    getWeatherVisual(code) {
      if (code === 0) return WEATHER_VISUALS.clear
      if ([1, 2, 3].includes(code)) return WEATHER_VISUALS.cloud
      if ([45, 48].includes(code)) return WEATHER_VISUALS.fog
      if ([95, 96, 99].includes(code)) return WEATHER_VISUALS.storm
      if ([71, 73, 75, 77, 85, 86].includes(code)) return WEATHER_VISUALS.snow
      if ([51, 53, 55, 56, 57, 61, 63, 65, 66, 67, 80, 81, 82].includes(code)) return WEATHER_VISUALS.rain
      return WEATHER_VISUALS.cloud
    },
    normalizeLocation(raw) {
      const name =
        raw.FishingLakeName ||
        raw.fishingLakeName ||
        raw.location ||
        raw.name ||
        ''
      const latitude = Number(raw.latitude)
      const longitude = Number(raw.longitude)

      if (!name || Number.isNaN(latitude) || Number.isNaN(longitude)) {
        return null
      }

      return {
        id: raw.id,
        name,
        label: name,
        latitude,
        longitude
      }
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
          .map((row) => this.normalizeLocation(row))
          .filter(Boolean)

        if (!this.cities.length) {
          throw new Error('A locations tabla ures vagy hianyos adatokat tartalmaz.')
        }

        this.selectedCity = this.cities[0].name
        await this.fetchForecast()
      } catch (err) {
        this.cities = []
        this.dailyForecast = []
        this.error = err?.message || 'Hiba tortent a helyszinek lekeresenel.'
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
          throw new Error('A kivalasztott helyszinhez nem talaltam koordinatat.')
        }

        const weatherResponse = await axios.get('https://api.open-meteo.com/v1/forecast', {
          params: {
            latitude: cityData.latitude,
            longitude: cityData.longitude,
            daily: [
              'temperature_2m_max',
              'temperature_2m_min',
              'precipitation_sum',
              'windspeed_10m_max',
              'weathercode',
              'sunrise',
              'sunset',
              'cloudcover_mean'
            ].join(','),
            timezone: 'auto',
            forecast_days: 7
          }
        })

        const daily = weatherResponse.data.daily
        const weatherCodes = daily.weathercode || daily.weather_code || []

        this.dailyForecast = daily.time.map((date, i) => {
          const visual = this.getWeatherVisual(weatherCodes[i])
          return {
            date,
            tempMax: daily.temperature_2m_max[i],
            tempMin: daily.temperature_2m_min[i],
            precipitation: daily.precipitation_sum[i],
            windMax: daily.windspeed_10m_max[i],
            sunrise: daily.sunrise?.[i] || null,
            sunset: daily.sunset?.[i] || null,
            cloudCover: daily.cloudcover_mean?.[i] ?? 0,
            label: visual.label,
            icon: visual.icon
          }
        })
      } catch (err) {
        this.dailyForecast = []
        this.error = err?.message || 'Hiba tortent az idojaras lekeresenel.'
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<style scoped>
.weather-wrap {
  max-width: 1240px;
  margin: 0 auto;
  padding: 24px 20px 40px;
  color: #f4f9fc;
}

.weather-shell {
  border: 1px solid rgba(215, 232, 241, 0.26);
  border-radius: 28px;
  padding: 24px;
  background: linear-gradient(145deg, rgba(8, 20, 30, 0.52), rgba(18, 42, 58, 0.36));
  backdrop-filter: blur(6px);
  box-shadow: 0 18px 38px rgba(0, 0, 0, 0.28);
}

.top-row {
  display: grid;
  grid-template-columns: minmax(220px, 1fr) minmax(320px, 420px) minmax(220px, 1fr);
  align-items: center;
  gap: 16px;
}

.astro {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
  font-size: 0.95rem;
  color: #e7f2f8;
}

.search-pill {
  display: flex;
  align-items: center;
  gap: 10px;
  border: 1px solid rgba(205, 228, 239, 0.45);
  border-radius: 999px;
  padding: 10px 14px;
  background: rgba(7, 17, 24, 0.34);
}

.search-pill i {
  color: #d6e9f3;
}

.search-pill input {
  flex: 1;
  min-width: 0;
  border: 0;
  background: transparent;
  color: #ffffff;
  font-size: 1rem;
}

.search-pill input:focus {
  outline: none;
}

.search-pill input::placeholder {
  color: #c3d6df;
}

.metric-switch {
  justify-self: end;
  display: inline-flex;
  border-radius: 999px;
  border: 1px solid rgba(205, 228, 239, 0.28);
  background: rgba(12, 28, 38, 0.34);
  padding: 4px;
}

.metric-switch button {
  border: 0;
  background: transparent;
  color: #d3e7f1;
  padding: 8px 18px;
  border-radius: 999px;
  cursor: pointer;
  font-weight: 600;
}

.metric-switch button.active {
  background: linear-gradient(145deg, rgba(0, 141, 214, 0.95), rgba(0, 117, 182, 0.95));
  color: #ffffff;
}

.picker-row {
  margin-top: 16px;
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.picker-row select,
.ghost-btn {
  border: 1px solid rgba(205, 228, 239, 0.35);
  border-radius: 12px;
  padding: 8px 12px;
  background: rgba(8, 23, 33, 0.58);
  color: #eef8fc;
}

.picker-row select {
  min-width: 220px;
}

.picker-row select:focus,
.ghost-btn:focus {
  outline: none;
  border-color: rgba(214, 238, 248, 0.85);
  box-shadow: 0 0 0 3px rgba(173, 223, 243, 0.16);
}

.ghost-btn {
  cursor: pointer;
}

.ghost-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.state {
  margin: 14px 0 0;
  color: #eaf4fa;
}

.error {
  color: #ffd6d6;
}

.content-row {
  margin-top: 20px;
  display: grid;
  grid-template-columns: minmax(220px, 280px) 1fr;
  gap: 22px;
  align-items: end;
}

.current-card {
  padding: 14px 8px;
}

.current-icon {
  width: 92px;
  height: 92px;
  object-fit: contain;
}

.current-temp {
  font-size: clamp(3rem, 8vw, 4.8rem);
  line-height: 1;
  font-weight: 700;
  color: #ffffff;
  margin-top: 6px;
}

.current-city {
  margin-top: 6px;
  font-size: 2rem;
  font-weight: 700;
}

.current-time {
  margin-top: 2px;
  font-size: 2rem;
  color: #dcecf4;
}

.current-meta {
  margin-top: 14px;
  display: flex;
  gap: 20px;
}

.current-meta div {
  display: grid;
}

.current-meta strong {
  font-size: 1.6rem;
  line-height: 1;
}

.current-meta span {
  color: #d4e7f0;
}

.wind-current {
  margin: 12px 0 0;
  color: #e6f2f8;
  font-weight: 600;
}

.forecast-row {
  display: grid;
  grid-auto-flow: column;
  grid-auto-columns: minmax(108px, 1fr);
  gap: 10px;
  overflow-x: auto;
  padding-bottom: 4px;
}

.forecast-row::-webkit-scrollbar {
  height: 8px;
}

.forecast-row::-webkit-scrollbar-thumb {
  background: rgba(203, 226, 238, 0.35);
  border-radius: 999px;
}

.mini-card {
  border: 1px solid rgba(214, 232, 241, 0.35);
  border-radius: 16px;
  background: rgba(255, 255, 255, 0.16);
  backdrop-filter: blur(4px);
  padding: 10px;
  text-align: center;
  min-height: 160px;
}

.mini-day {
  margin: 0;
  color: #e4f1f7;
  font-size: 0.9rem;
}

.mini-icon {
  width: 54px;
  height: 54px;
  object-fit: contain;
  margin: 6px auto;
}

.mini-main {
  margin: 0;
  font-size: 1.9rem;
  line-height: 1.1;
  font-weight: 700;
  color: #ffffff;
}

.mini-sub {
  margin: 4px 0 0;
  color: #d8eaf3;
  font-size: 0.82rem;
}

@media (max-width: 1080px) {
  .top-row {
    grid-template-columns: 1fr;
  }

  .metric-switch {
    justify-self: start;
  }

  .content-row {
    grid-template-columns: 1fr;
  }

  .current-card {
    padding: 0;
  }
}
</style>
