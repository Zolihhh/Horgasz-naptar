<template>
  <div class="picker-row d-flex flex-wrap gap-2 align-items-start">
    <div class="city-dropdown w-100">
      <button
        type="button"
        class="city-trigger"
        :disabled="loading || !filteredCities.length"
        @click="toggleMenu"
      >
        <span>{{ selectedCityLabel }}</span>
        <i class="bi" :class="isMenuOpen ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
      </button>

      <ul v-if="isMenuOpen" class="city-menu">
        <li v-for="city in filteredCities" :key="city.id">
          <button
            type="button"
            :class="{ active: city.name === selectedCity }"
            @click="selectCity(city.name)"
          >
            {{ city.label }}
          </button>
        </li>
      </ul>
    </div>

    <button type="button" class="ghost-btn btn" :disabled="loading" @click="$emit('refresh')">
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
  emits: ['update:selected-city', 'reset-search', 'refresh'],
  data() {
    return {
      isMenuOpen: false
    }
  },
  computed: {
    selectedCityLabel() {
      const selected = this.filteredCities.find((city) => city.name === this.selectedCity)
      if (selected?.label) return selected.label
      if (this.selectedCity) return this.selectedCity
      return 'Válassz várost'
    }
  },
  methods: {
    toggleMenu() {
      this.isMenuOpen = !this.isMenuOpen
    },
    selectCity(cityName) {
      this.$emit('update:selected-city', cityName)
      this.isMenuOpen = false
    },
    handleClickOutside(event) {
      if (!this.$el.contains(event.target)) {
        this.isMenuOpen = false
      }
    },
    handleEscape(event) {
      if (event.key === 'Escape') {
        this.isMenuOpen = false
      }
    }
  },
  mounted() {
    document.addEventListener('click', this.handleClickOutside)
    document.addEventListener('keydown', this.handleEscape)
  },
  beforeUnmount() {
    document.removeEventListener('click', this.handleClickOutside)
    document.removeEventListener('keydown', this.handleEscape)
  }
}
</script>
