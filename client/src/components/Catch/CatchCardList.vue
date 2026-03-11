<template>
  <div class="grid log-grid">
    <div class="card log-card" v-for="log in catchLogs" :key="log.id">
      <div class="log-head">
        <div>
          <h3>Fogási napló</h3>
          <p>{{ getCatchLogLabel(log) }}</p>
          <p v-if="log.comment">Megjegyzés: {{ log.comment }}</p>
        </div>

        <div class="d-flex flex-wrap gap-2">
          <button type="button" class="btn btn-sm btn-outline-light secondary-btn" @click="toggleLog(log.id)">
            {{ isOpen(log.id) ? "Fogások elrejtése" : "Fogások megnyitása" }}
          </button>
          <button
            type="button"
            class="btn btn-sm btn-outline-danger"
            :disabled="deletingCatchLogId === log.id"
            @click="$emit('request-delete-log', log)"
          >
            {{ deletingCatchLogId === log.id ? "Törlés..." : "Napló törlése" }}
          </button>
        </div>
      </div>

      <div v-if="isOpen(log.id)" class="log-content">
        <p v-if="log.catches.length === 0" class="status-text">Ehhez a naplóhoz még nincs fogás.</p>

        <div v-else class="catch-list">
          <div class="catch-item" v-for="c in log.catches" :key="c.id">
            <h4>Fogás</h4>
            <p>Halfaj: {{ getFishName(c.specieId) }}</p>
            <p>Víz: {{ getLocationNameByCatchLogId(c.catchLogId) }}</p>
            <p>Súly: {{ c.weight }} kg</p>
            <p>Hossz: {{ c.length }} cm</p>
            <p>Csali: {{ getLureName(c.lureId) }}</p>
            <p>Időpont: {{ formatCatchTime(c.catchTime) }}</p>

            <div class="card-actions d-flex flex-wrap gap-2">
              <button
                type="button"
                class="btn btn-sm btn-outline-light secondary-btn"
                @click="$emit('start-edit', c)"
              >
                Fogás módosítása
              </button>
              <button
                type="button"
                class="btn btn-sm btn-outline-danger"
                :disabled="deletingCatchId === c.id"
                @click="$emit('request-delete-catch', c)"
              >
                {{ deletingCatchId === c.id ? "Törlés..." : "Fogás törlése" }}
              </button>
            </div>

            <form
              v-if="editingCatchId === c.id"
              class="edit-form"
              @submit.prevent="$emit('update-catch', c.id)"
            >
              <label>
                Fogási napló
                <select v-model.number="editCatch.catchLogId" class="form-select" required>
                  <option disabled :value="null">Válassz naplót</option>
                  <option v-for="item in userCatchLogs" :key="item.id" :value="item.id">
                    {{ getCatchLogLabel(item) }}
                  </option>
                </select>
              </label>

              <label>
                Halfaj
                <select v-model.number="editCatch.specieId" class="form-select" required>
                  <option disabled :value="null">Válassz halfajt</option>
                  <option v-for="item in species" :key="item.id" :value="item.id">
                    {{ item.fish_name }}
                  </option>
                </select>
              </label>

              <label>
                Csali
                <select v-model.number="editCatch.lureId" class="form-select" required>
                  <option disabled :value="null">Válassz csalit</option>
                  <option v-for="item in lures" :key="item.id" :value="item.id">
                    {{ item.lure }}
                  </option>
                </select>
              </label>

              <label>
                Súly (kg)
                <input
                  v-model.number="editCatch.weight"
                  class="form-control"
                  type="number"
                  step="0.01"
                  min="0"
                  max="100"
                  required
                />
              </label>

              <label>
                Hossz (cm)
                <input
                  v-model.number="editCatch.length"
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
                <input v-model="editCatch.catchTime" class="form-control" type="datetime-local" required />
              </label>

              <div class="inline-actions">
                <button type="submit" class="btn btn-primary primary-btn" :disabled="updating">
                  {{ updating ? "Mentés..." : "Változások mentése" }}
                </button>
                <button type="button" class="btn btn-outline-secondary secondary-btn" @click="$emit('cancel-edit')">
                  Mégsem
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "CatchCardList",
  props: {
    catchLogs: {
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
    deletingCatchId: {
      type: Number,
      default: null,
    },
    deletingCatchLogId: {
      type: Number,
      default: null,
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
  emits: [
    "start-edit",
    "update-catch",
    "cancel-edit",
    "request-delete-catch",
    "request-delete-log",
  ],
  data() {
    return {
      openLogId: null,
    };
  },
  methods: {
    isOpen(logId) {
      return this.openLogId === logId;
    },
    toggleLog(logId) {
      if (this.isOpen(logId)) {
        this.openLogId = null;
        return;
      }
      this.openLogId = logId;
    },
  },
};
</script>
