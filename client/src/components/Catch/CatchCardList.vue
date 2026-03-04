<template>
  <div class="grid">
    <div class="card" v-for="c in catches" :key="c.id">
      <h3>Fogás #{{ c.id }}</h3>
      <p>Halfaj: {{ getFishName(c.specieId) }}</p>
      <p>Víz: {{ getLocationNameByCatchLogId(c.catchLogId) }}</p>
      <p>Súly: {{ c.weight }} kg</p>
      <p>Hossz: {{ c.length }} cm</p>
      <p>Csali: {{ getLureName(c.lureId) }}</p>
      <p>Időpont: {{ formatCatchTime(c.catchTime) }}</p>

      <div class="card-actions">
        <button type="button" class="secondary-btn" @click="$emit('start-edit', c)">
          Fogás módosítása
        </button>
      </div>

      <form
        v-if="editingCatchId === c.id"
        class="edit-form"
        @submit.prevent="$emit('update-catch', c.id)"
      >
        <label>
          Fogási napló
          <select v-model.number="editCatch.catchLogId" required>
            <option disabled :value="null">Válassz naplót</option>
            <option v-for="log in userCatchLogs" :key="log.id" :value="log.id">
              {{ getCatchLogLabel(log) }}
            </option>
          </select>
        </label>

        <label>
          Halfaj
          <select v-model.number="editCatch.specieId" required>
            <option disabled :value="null">Válassz halfajt</option>
            <option v-for="item in species" :key="item.id" :value="item.id">
              {{ item.fish_name }}
            </option>
          </select>
        </label>

        <label>
          Csali
          <select v-model.number="editCatch.lureId" required>
            <option disabled :value="null">Válassz csalit</option>
            <option v-for="item in lures" :key="item.id" :value="item.id">
              {{ item.lure }}
            </option>
          </select>
        </label>

        <label>
          Súly (kg)
          <input v-model.number="editCatch.weight" type="number" step="0.01" min="0" max="9.99" required />
        </label>

        <label>
          Hossz (cm)
          <input v-model.number="editCatch.length" type="number" step="0.1" min="0" required />
        </label>

        <label>
          Időpont
          <input v-model="editCatch.catchTime" type="datetime-local" required />
        </label>

        <div class="inline-actions">
          <button type="submit" class="primary-btn" :disabled="updating">
            {{ updating ? "Mentés..." : "Változások mentése" }}
          </button>
          <button type="button" class="secondary-btn" @click="$emit('cancel-edit')">
            Mégsem
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  name: "CatchCardList",
  props: {
    catches: {
      type: Array,
      required: true,
    },
    editingCatchId: {
      type: Number,
      default: null,
    },
    editCatch: {
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
    updating: {
      type: Boolean,
      required: true,
    },
    getFishName: {
      type: Function,
      required: true,
    },
    getLocationNameByCatchLogId: {
      type: Function,
      required: true,
    },
    getLureName: {
      type: Function,
      required: true,
    },
    formatCatchTime: {
      type: Function,
      required: true,
    },
    getCatchLogLabel: {
      type: Function,
      required: true,
    },
  },
  emits: ["start-edit", "update-catch", "cancel-edit"],
};
</script>
