<template>
  <div class="top-row">
    <div class="astro" v-if="todaySummary">
      <span>{{ formatClock(todaySummary.sunrise) }} Napkelte</span>
      <span>{{ formatClock(todaySummary.sunset) }} Napnyugta</span>
      <span>{{ round(todaySummary.cloudCover) }}% Felhő</span>
    </div>

    <div class="search-pill">
      <i class="bi bi-geo-alt-fill"></i>
      <input
        :value="searchTerm"
        type="text"
        placeholder="Keresés városra..."
        :disabled="loading"
        @input="$emit('update:search-term', $event.target.value)"
      />
      <i class="bi bi-search"></i>
    </div>

    <div class="metric-switch">
      <button
        type="button"
        :class="{ active: metricMode === 'temperature' }"
        @click="$emit('set-metric', 'temperature')"
      >
        Hőmérséklet
      </button>
      <button
        type="button"
        :class="{ active: metricMode === 'precipitation' }"
        @click="$emit('set-metric', 'precipitation')"
      >
        Csapadék
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'WeatherTopBar',
  props: {
    todaySummary: {
      type: Object,
      default: null
    },
    searchTerm: {
      type: String,
      required: true
    },
    loading: {
      type: Boolean,
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
    formatClock: {
      type: Function,
      required: true
    }
  },
  emits: ['update:search-term', 'set-metric']
}
</script>
