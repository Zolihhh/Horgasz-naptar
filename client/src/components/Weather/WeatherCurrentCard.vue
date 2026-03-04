<template>
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

    <p class="wind-current">Szél: {{ round(currentDay.windMax) }} km/h</p>
  </article>
</template>

<script>
export default {
  name: 'WeatherCurrentCard',
  props: {
    currentDay: {
      type: Object,
      required: true
    },
    currentDisplayTemp: {
      type: Number,
      required: true
    },
    selectedCity: {
      type: String,
      required: true
    },
    currentTime: {
      type: String,
      required: true
    },
    round: {
      type: Function,
      required: true
    }
  }
}
</script>
