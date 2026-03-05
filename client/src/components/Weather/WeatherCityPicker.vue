<template>
  <div class="picker-row">
    <select
      :value="selectedCity"
      :disabled="loading || !filteredCities.length"
      @change="$emit('update:selected-city', $event.target.value)"
    >
      <option v-for="city in filteredCities" :key="city.id" :value="city.name">
        {{ city.label }}
      </option>
    </select>
    <button type="button" class="ghost-btn" :disabled="loading" @click="$emit('refresh')">
      Frissítés
    </button>
  </div>
</template>

<script>
export default {
  name: 'WeatherCityPicker',
  props: {
    filteredCities: {
      type: Array,
      required: true
    },
    selectedCity: {
      type: String,
      required: true
    },
    loading: {
      type: Boolean,
      required: true
    }
  },
  emits: ['update:selected-city', 'reset-search', 'refresh']
}
</script>
