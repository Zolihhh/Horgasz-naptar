<template>
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
        <p class="mini-sub">szél {{ round(day.windMax) }} km/h</p>
      </template>
    </article>
  </div>
</template>

<script>
export default {
  name: 'WeatherForecastRow',
  props: {
    dailyForecast: {
      type: Array,
      required: true
    },
    metricMode: {
      type: String,
      required: true
    },
    round: {
      type: Function,
      required: true
    },
    formatShortDate: {
      type: Function,
      required: true
    }
  }
}
</script>
