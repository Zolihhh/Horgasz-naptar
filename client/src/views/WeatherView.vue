<template>
  <section class="weather-wrap">
    <div class="page-head">
      <h1>Idojaras</h1>
      <p>Sajat helyszineid 7 napos elorejelzese egy helyen</p>
    </div>

    <div class="top-actions">
      <button type="button" class="primary-btn" :disabled="loading" @click="fetchForecast">
        Elorejelzes frissitese
      </button>
      <button type="button" class="primary-btn secondary" :disabled="loading" @click="resetSearch">
        Kereses torlese
      </button>
    </div>

    <div class="controls">
      <div class="field">
        <label for="city-search">Kereses</label>
        <input
          id="city-search"
          v-model.trim="searchTerm"
          type="text"
          placeholder="Kereses helyszinnevre..."
          :disabled="loading"
          @input="syncSelectionWithFilter"
        />
      </div>

      <div class="field">
        <label for="city">Helyszin</label>
        <select id="city" v-model="selectedCity" @change="fetchForecast" :disabled="loading">
          <option v-for="city in filteredCities" :key="city.id" :value="city.name">
            {{ city.label }}
          </option>
        </select>
      </div>
    </div>

    <p v-if="loading" class="state">Betoltes...</p>
    <p v-else-if="error" class="state error">{{ error }}</p>
    <p v-else-if="!filteredCities.length" class="state">Nincs talalat a keresesi feltetelre.</p>

    <div v-else-if="dailyForecast.length" class="grid">
      <article v-for="day in dailyForecast" :key="day.date" class="card">
        <div class="card-head">
          <img :src="day.icon" :alt="day.label" class="weather-icon" />
          <div>
            <h3>{{ formatDate(day.date) }}</h3>
            <p class="weather-label">{{ day.label }}</p>
          </div>
        </div>

        <p>Min: {{ round(day.tempMin) }} C</p>
        <p>Max: {{ round(day.tempMax) }} C</p>
        <p>Csapadek: {{ round(day.precipitation) }} mm</p>
        <p>Szel (max): {{ round(day.windMax) }} km/h</p>
      </article>
    </div>
  </section>
</template>

<script>
import axios from 'axios'
import locationService from '@/api/locationService'

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
      loading: false,
      error: ''
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
    }
  },
  async mounted() {
    await this.loadLocations()
  },
  methods: {
    round(value) {
      return Math.round(value * 10) / 10
    },
    formatDate(date) {
      return new Date(date).toLocaleDateString('hu-HU', {
        weekday: 'long',
        month: 'short',
        day: 'numeric'
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
        const response = await locationService.getAll()
        const rows = Array.isArray(response?.data) ? response.data : []
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
              'weathercode'
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
  max-width: 1150px;
  margin: 0 auto;
  padding: 28px 20px 40px;
  color: #ffffff;
}

.page-head {
  margin-bottom: 18px;
  padding: 16px 18px;
  border-radius: 16px;
  border: 1px solid rgba(210, 232, 241, 0.24);
  background: linear-gradient(135deg, rgba(6, 18, 26, 0.78), rgba(16, 45, 61, 0.55));
  backdrop-filter: blur(8px);
}

.page-head h1 {
  margin: 0;
  font-size: clamp(1.4rem, 3.3vw, 2rem);
  letter-spacing: 0.02em;
  color: #ffffff;
}

.page-head p {
  margin: 6px 0 0;
  color: #d6e6ee;
}

.top-actions {
  margin-bottom: 14px;
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.controls {
  margin-bottom: 18px;
  padding: 16px;
  border-radius: 16px;
  border: 1px solid rgba(210, 232, 241, 0.22);
  background: linear-gradient(145deg, rgba(7, 16, 24, 0.74), rgba(8, 29, 41, 0.58));
  backdrop-filter: blur(8px);
  box-shadow: 0 18px 35px rgba(0, 0, 0, 0.24);
  display: grid;
  gap: 14px;
  grid-template-columns: minmax(280px, 1fr) minmax(280px, 1fr);
}

.field {
  min-width: 0;
  display: flex;
  flex-direction: column;
  gap: 6px;
}

label {
  display: block;
  margin-bottom: 6px;
  font-size: 0.95rem;
  color: #eef7fb;
}

select {
  width: 100%;
  border: 1px solid rgba(194, 224, 236, 0.35);
  border-radius: 8px;
  padding: 8px 10px;
  background: rgba(5, 14, 20, 0.56);
  color: #fff;
}

input {
  width: 100%;
  border: 1px solid rgba(194, 224, 236, 0.35);
  border-radius: 8px;
  padding: 8px 10px;
  background: rgba(5, 14, 20, 0.56);
  color: #fff;
}

input:disabled {
  opacity: 0.75;
}

input:focus {
  outline: none;
  border-color: rgba(214, 238, 248, 0.85);
  box-shadow: 0 0 0 3px rgba(173, 223, 243, 0.16);
}

select:focus {
  outline: none;
  border-color: rgba(214, 238, 248, 0.85);
  box-shadow: 0 0 0 3px rgba(173, 223, 243, 0.16);
}

.primary-btn {
  border: 1px solid rgba(194, 224, 236, 0.62);
  border-radius: 10px;
  background: linear-gradient(145deg, rgba(10, 31, 44, 0.88), rgba(16, 54, 73, 0.78));
  color: #fff;
  font-weight: 600;
  letter-spacing: 0.01em;
  padding: 9px 14px;
  cursor: pointer;
  transition: background 0.2s ease, transform 0.16s ease, box-shadow 0.2s ease;
}

.primary-btn:hover {
  background: linear-gradient(145deg, rgba(16, 48, 66, 0.94), rgba(27, 83, 107, 0.88));
  box-shadow: 0 10px 22px rgba(12, 40, 55, 0.28);
  transform: translateY(-1px);
}

.primary-btn:disabled {
  opacity: 0.55;
  cursor: not-allowed;
  transform: none;
}

.primary-btn.secondary {
  background: linear-gradient(145deg, rgba(16, 33, 43, 0.9), rgba(19, 52, 67, 0.76));
}

.state {
  margin: 0 0 12px;
  font-size: 1.05rem;
  color: #e7f2f8;
}

.error {
  color: #ffd3d3;
}

.grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 16px;
}

.card {
  border: 1px solid rgba(202, 227, 238, 0.2);
  background: linear-gradient(145deg, rgba(8, 17, 25, 0.78), rgba(11, 34, 47, 0.62));
  backdrop-filter: blur(7px);
  padding: 18px;
  border-radius: 14px;
  box-shadow: 0 14px 32px rgba(0, 0, 0, 0.22);
}

.card-head {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 6px;
}

.weather-icon {
  width: 56px;
  height: 56px;
  object-fit: contain;
}

.card h3 {
  margin: 0;
  font-size: 1rem;
}

.weather-label {
  margin: 2px 0 0;
  color: #d6e6ee;
  font-size: 0.9rem;
}

.card p {
  margin: 6px 0;
  color: #e4f0f6;
}

@media (max-width: 1100px) {
  .controls {
    grid-template-columns: 1fr;
  }
}
</style>
