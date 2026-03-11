<template>
  <form class="create-form" @submit.prevent="$emit('submit')">
    <label>
      Fogási napló
      <select v-model.number="newCatch.catchLogId" class="form-select" required>
        <option disabled :value="null">Válassz naplót</option>
        <option v-for="log in userCatchLogs" :key="log.id" :value="log.id">
          {{ getCatchLogLabel(log) }}
        </option>
      </select>
    </label>

    <label>
      Halfaj
      <select v-model.number="newCatch.specieId" class="form-select" required>
        <option disabled :value="null">Válassz halfajt</option>
        <option v-for="item in species" :key="item.id" :value="item.id">
          {{ item.fish_name }}
        </option>
      </select>
    </label>

    <label>
      Csali
      <select v-model.number="newCatch.lureId" class="form-select" required>
        <option disabled :value="null">Válassz csalit</option>
        <option v-for="item in lures" :key="item.id" :value="item.id">
          {{ item.lure }}
        </option>
      </select>
    </label>

    <label>
      Súly (kg)
      <input v-model.number="newCatch.weight" class="form-control" type="number" step="0.01" min="0" max="100" required />
    </label>

    <label>
      Hossz (cm)
      <input
        v-model.number="newCatch.length"
        class="form-control"
        type="number"
        step="0.1"
        min="0"
        max="999.9"
        required
      />
    </label>

    <label>
      Időpont
      <input v-model="newCatch.catchTime" class="form-control" type="datetime-local" required />
    </label>

    <button type="submit" class="primary-btn btn" :disabled="saving || userCatchLogs.length === 0">
      {{ saving ? "Mentés..." : "Fogás mentése" }}
    </button>
    <p v-if="userCatchLogs.length === 0" class="status-text">
      Előbb hozz létre fogási naplót, mert ehhez kell kapcsolni a fogást.
    </p>
  </form>
</template>

<script>
export default {
  name: "CatchForm",
  props: {
    newCatch: {
      type: Object,
      required: true,
    },
    userCatchLogs: {
      type: Array,
      required: true,
    },
    species: {
      type: Array,
      required: true,
    },
    lures: {
      type: Array,
      required: true,
    },
    saving: {
      type: Boolean,
      required: true,
    },
    getCatchLogLabel: {
      type: Function,
      required: true,
    },
  },
  emits: ["submit"],
};
</script>
