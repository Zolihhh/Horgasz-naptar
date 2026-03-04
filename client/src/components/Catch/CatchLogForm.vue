<template>
  <form class="create-form" @submit.prevent="$emit('submit')">
    <label>
      Víz keresése
      <input
        :value="locationSearchTerm"
        type="text"
        placeholder="Víz névre szűrés..."
        @input="$emit('update:locationSearchTerm', $event.target.value)"
      />
    </label>

    <label>
      Víz
      <select v-model.number="newCatchLog.locationid" required>
        <option disabled :value="null">Válassz vizet</option>
        <option v-for="location in filteredLocations" :key="location.id" :value="location.id">
          {{ getLocationName(location.id) }}
        </option>
      </select>
    </label>

    <label>
      Horgászat kezdete
      <input v-model="newCatchLog.fishing_start" type="date" required />
    </label>

    <label>
      Horgászat vége
      <input v-model="newCatchLog.fishing_end" type="date" required />
    </label>

    <label>
      Megjegyzés
      <input v-model="newCatchLog.comment" type="text" placeholder="Opcionális" />
    </label>

    <button type="submit" class="primary-btn" :disabled="savingLog">
      {{ savingLog ? "Mentés..." : "Fogási napló mentése" }}
    </button>
  </form>
</template>

<script>
export default {
  name: "CatchLogForm",
  props: {
    newCatchLog: {
      type: Object,
      required: true,
    },
    locationSearchTerm: {
      type: String,
      required: true,
    },
    filteredLocations: {
      type: Array,
      required: true,
    },
    savingLog: {
      type: Boolean,
      required: true,
    },
    getLocationName: {
      type: Function,
      required: true,
    },
  },
  emits: ["submit", "update:locationSearchTerm"],
};
</script>
