import axios from 'axios'

const WEATHER_VISUALS = {
  clear: {
    label: 'Napos',
    icon: 'https://openweathermap.org/img/wn/01d@2x.png'
  },
  cloud: {
    label: 'Felhős',
    icon: 'https://openweathermap.org/img/wn/03d@2x.png'
  },
  rain: {
    label: 'Esős',
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
    label: 'Ködös',
    icon: 'https://openweathermap.org/img/wn/50d@2x.png'
  }
}

export function useWeatherForecast() {
  function round(value) {
    return Math.round(Number(value) * 10) / 10
  }

  function formatShortDate(date) {
    return new Date(date).toLocaleDateString('hu-HU', {
      month: 'numeric',
      day: 'numeric'
    })
  }

  function formatClock(value) {
    if (!value) return '--:--'
    const date = new Date(value)
    if (Number.isNaN(date.getTime())) return '--:--'
    return date.toLocaleTimeString('hu-HU', {
      hour: '2-digit',
      minute: '2-digit'
    })
  }

  function getWeatherVisual(code) {
    if (code === 0) return WEATHER_VISUALS.clear
    if ([1, 2, 3].includes(code)) return WEATHER_VISUALS.cloud
    if ([45, 48].includes(code)) return WEATHER_VISUALS.fog
    if ([95, 96, 99].includes(code)) return WEATHER_VISUALS.storm
    if ([71, 73, 75, 77, 85, 86].includes(code)) return WEATHER_VISUALS.snow
    if ([51, 53, 55, 56, 57, 61, 63, 65, 66, 67, 80, 81, 82].includes(code)) return WEATHER_VISUALS.rain
    return WEATHER_VISUALS.cloud
  }

  function normalizeCity(raw) {
    const name = raw.name || raw.city || ''
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
  }

  // Backward-compatible alias for legacy callers.
  function normalizeLocation(raw) {
    return normalizeCity(raw)
  }

  async function fetchForecastForLocation(location) {
    const weatherResponse = await axios.get('https://api.open-meteo.com/v1/forecast', {
      params: {
        latitude: location.latitude,
        longitude: location.longitude,
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

    return daily.time.map((date, i) => {
      const visual = getWeatherVisual(weatherCodes[i])
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
  }

  return {
    round,
    formatShortDate,
    formatClock,
    normalizeCity,
    normalizeLocation,
    fetchForecastForLocation
  }
}
