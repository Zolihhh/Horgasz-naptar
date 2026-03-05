<template>
  <div class="top-row row g-3 align-items-center">
    <div class="astro col-12 col-xl" v-if="todaySummary">
      <span>{{ formatClock(todaySummary.sunrise) }} Napkelte</span>
      <span>{{ formatClock(todaySummary.sunset) }} Napnyugta</span>
      <span>{{ round(todaySummary.cloudCover) }}% Felhő</span>
    </div>

    <div class="col-12 col-xl-5">
      <div class="search-pill input-group">
        <i class="bi bi-geo-alt-fill"></i>
        <input
          :value="searchTerm"
          type="text"
          class="form-control"
          placeholder="Keresés városra..."
          :disabled="loading"
          @input="$emit('update:search-term', $event.target.value)"
        />
        <i class="bi bi-search"></i>
      </div>
    </div>

    <div class="col-12 col-xl d-flex justify-content-xl-end">
      <div class="metric-switch btn-group" role="group" aria-label="Metrika választó">
        <button
          type="button"
          class="btn"
          :class="{ active: metricMode === 'temperature' }"
          @click="$emit('set-metric', 'temperature')"
        >
          Hőmérséklet
        </button>
        <button
          type="button"
          class="btn"
          :class="{ active: metricMode === 'precipitation' }"
          @click="$emit('set-metric', 'precipitation')"
        >
          Csapadék
        </button>
      </div>
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
