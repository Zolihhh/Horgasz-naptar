<template>
  <div class="grid-wrap">
    <div class="page-head">
      <h1>Fogasok</h1>
      <p>Sajat fogasaid kezelese egy helyen</p>
    </div>

    <p v-if="!isLoggedIn" class="status-text">A fogasok megtekintesehez jelentkezz be.</p>

    <div v-else class="top-actions">
      <button type="button" class="primary-btn" @click="toggleCreateForm">
        {{ showCreateForm ? "Urlap bezarasa" : "Uj fogas rogzitese" }}
      </button>
    </div>

    <form v-if="isLoggedIn && showCreateForm" class="create-form" @submit.prevent="createCatch">
      <label>
        Fogasi naplo
        <select v-model.number="newCatch.catchLogId" required>
          <option disabled :value="null">Valassz naplot</option>
          <option v-for="log in userCatchLogs" :key="log.id" :value="log.id">
            #{{ log.id }} ({{ log.fishing_start || "-" }} - {{ log.fishing_end || "-" }})
          </option>
        </select>
      </label>

      <label>
        Halfaj
        <select v-model.number="newCatch.specieId" required>
          <option disabled :value="null">Valassz halfajt</option>
          <option v-for="item in species" :key="item.id" :value="item.id">
            {{ item.fish_name }}
          </option>
        </select>
      </label>

      <label>
        Csali
        <select v-model.number="newCatch.lureId" required>
          <option disabled :value="null">Valassz csalit</option>
          <option v-for="item in lures" :key="item.id" :value="item.id">
            {{ item.lure }}
          </option>
        </select>
      </label>

      <label>
        Suly (kg)
        <input v-model.number="newCatch.weight" type="number" step="0.01" min="0" required />
      </label>

      <label>
        Hossz (cm)
        <input v-model.number="newCatch.length" type="number" step="0.1" min="0" required />
      </label>

      <label>
        Idopont
        <input v-model="newCatch.catchTime" type="datetime-local" required />
      </label>

      <button type="submit" class="primary-btn" :disabled="saving || userCatchLogs.length === 0">
        {{ saving ? "Mentes..." : "Fogas mentese" }}
      </button>
      <p v-if="userCatchLogs.length === 0" class="status-text">
        Elobb hozz letre fogasi naplot, mert ehhez kell kapcsolni a fogast.
      </p>
    </form>

    <p v-if="isLoggedIn && loading" class="status-text">Betoltes...</p>
    <p v-else-if="isLoggedIn && catches.length === 0" class="status-text">Nincs rogzitett fogasod.</p>

    <div v-if="isLoggedIn && catches.length > 0" class="grid">
      <div class="card" v-for="c in catches" :key="c.id">
        <h3>Fogas #{{ c.id }}</h3>
        <p>Halfaj: {{ getFishName(c.specieId) }}</p>
        <p>Suly: {{ c.weight }} kg</p>
        <p>Hossz: {{ c.length }} cm</p>
        <p>Csali: {{ getLureName(c.lureId) }}</p>
        <p>Idopont: {{ formatCatchTime(c.catchTime) }}</p>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from "pinia";
import { useUserLoginLogoutStore } from "@/stores/userLoginLogoutStore";
import fishCatchService from "@/api/fishCatchService";
import specieService from "@/api/specieService";
import lureService from "@/api/lureService";
import catchLogService from "@/api/catchLogService";

function emptyCatch() {
  return {
    catchLogId: null,
    specieId: null,
    lureId: null,
    weight: null,
    length: null,
    catchTime: "",
  };
}

export default {
  name: "CatchView",
  data() {
    return {
      catches: [],
      species: [],
      lures: [],
      userCatchLogs: [],
      speciesById: {},
      luresById: {},
      showCreateForm: false,
      newCatch: emptyCatch(),
      loading: false,
      saving: false,
    };
  },
  computed: {
    ...mapState(useUserLoginLogoutStore, ["isLoggedIn", "item"]),
    currentUserId() {
      return this.item?.id ?? null;
    },
  },
  watch: {
    isLoggedIn(newValue) {
      if (newValue) {
        this.fetchMyCatches();
      } else {
        this.catches = [];
        this.userCatchLogs = [];
        this.showCreateForm = false;
      }
    },
  },
  mounted() {
    if (this.isLoggedIn) {
      this.fetchMyCatches();
    }
  },
  methods: {
    async fetchMyCatches() {
      this.loading = true;
      try {
        const [catchesResponse, speciesResponse, luresResponse, logsResponse] = await Promise.all([
          fishCatchService.getMyCatches(),
          specieService.getAll(),
          lureService.getAll(),
          catchLogService.getAll(),
        ]);

        this.catches = catchesResponse.data;
        this.species = speciesResponse.data;
        this.lures = luresResponse.data;
        this.speciesById = this.species.reduce((acc, item) => {
          acc[item.id] = item.fish_name;
          return acc;
        }, {});
        this.luresById = this.lures.reduce((acc, item) => {
          acc[item.id] = item.lure;
          return acc;
        }, {});
        this.userCatchLogs = logsResponse.data.filter((item) => item.userid === this.currentUserId);
        this.prefillCreateForm();
      } catch (error) {
        this.catches = [];
        this.species = [];
        this.lures = [];
        this.userCatchLogs = [];
        this.speciesById = {};
        this.luresById = {};
      } finally {
        this.loading = false;
      }
    },
    toggleCreateForm() {
      this.showCreateForm = !this.showCreateForm;
      if (this.showCreateForm) {
        this.prefillCreateForm();
      }
    },
    prefillCreateForm() {
      const now = new Date();
      const localDateTime = new Date(now.getTime() - now.getTimezoneOffset() * 60000)
        .toISOString()
        .slice(0, 16);

      this.newCatch = {
        catchLogId: this.userCatchLogs[0]?.id ?? null,
        specieId: this.species[0]?.id ?? null,
        lureId: this.lures[0]?.id ?? null,
        weight: null,
        length: null,
        catchTime: localDateTime,
      };
    },
    async createCatch() {
      if (this.userCatchLogs.length === 0) return;
      this.saving = true;
      try {
        await fishCatchService.create(this.newCatch);
        this.showCreateForm = false;
        await this.fetchMyCatches();
      } catch (error) {
        // Hibakezeles az axios interceptorban tortenik
      } finally {
        this.saving = false;
      }
    },
    getFishName(specieId) {
      return this.speciesById[specieId] || `Ismeretlen halfaj (#${specieId})`;
    },
    getLureName(lureId) {
      return this.luresById[lureId] || `Ismeretlen csali (#${lureId})`;
    },
    formatCatchTime(value) {
      if (!value) return "-";
      const date = new Date(value);
      if (Number.isNaN(date.getTime())) return value;
      return date.toLocaleString("hu-HU");
    },
  },
};
</script>

<style scoped>
.grid-wrap {
  max-width: 1150px;
  margin: 0 auto;
  padding: 28px 20px 40px;
}

.page-head {
  margin-bottom: 18px;
  padding: 16px 18px;
  border-radius: 16px;
  border: 1px solid rgba(210, 232, 241, 0.24);
  background: linear-gradient(
    135deg,
    rgba(6, 18, 26, 0.78),
    rgba(16, 45, 61, 0.55)
  );
  backdrop-filter: blur(8px);
}

.page-head h1 {
  margin: 0;
  font-size: clamp(1.4rem, 3.3vw, 2rem);
  letter-spacing: 0.02em;
}

.page-head p {
  margin: 6px 0 0;
  color: #d6e6ee;
}

.top-actions {
  margin-bottom: 14px;
}

.create-form {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
  gap: 12px;
  margin-bottom: 22px;
  padding: 16px;
  border-radius: 16px;
  border: 1px solid rgba(210, 232, 241, 0.22);
  background: linear-gradient(
    145deg,
    rgba(7, 16, 24, 0.74),
    rgba(8, 29, 41, 0.58)
  );
  backdrop-filter: blur(8px);
  box-shadow: 0 18px 35px rgba(0, 0, 0, 0.24);
}

.create-form label {
  display: flex;
  flex-direction: column;
  gap: 6px;
  font-size: 0.95rem;
}

.create-form input,
.create-form select {
  border: 1px solid rgba(194, 224, 236, 0.35);
  border-radius: 8px;
  padding: 8px 10px;
  background: rgba(5, 14, 20, 0.56);
  color: #fff;
}

.create-form input:focus,
.create-form select:focus {
  outline: none;
  border-color: rgba(214, 238, 248, 0.85);
  box-shadow: 0 0 0 3px rgba(173, 223, 243, 0.16);
}

.primary-btn {
  border: 1px solid rgba(194, 224, 236, 0.62);
  border-radius: 10px;
  background: linear-gradient(
    145deg,
    rgba(10, 31, 44, 0.88),
    rgba(16, 54, 73, 0.78)
  );
  color: #fff;
  font-weight: 600;
  letter-spacing: 0.01em;
  padding: 9px 14px;
  cursor: pointer;
  transition: background 0.2s ease, transform 0.16s ease, box-shadow 0.2s ease;
}

.primary-btn:hover {
  background: linear-gradient(
    145deg,
    rgba(16, 48, 66, 0.94),
    rgba(27, 83, 107, 0.88)
  );
  box-shadow: 0 10px 22px rgba(12, 40, 55, 0.28);
  transform: translateY(-1px);
}

.primary-btn:disabled {
  opacity: 0.55;
  cursor: not-allowed;
  transform: none;
}

.status-text {
  margin: 0 0 12px;
  font-size: 1.05rem;
  color: #e7f2f8;
}

.grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 16px;
}

.card {
  border: 1px solid rgba(202, 227, 238, 0.2);
  background: linear-gradient(
    145deg,
    rgba(8, 17, 25, 0.78),
    rgba(11, 34, 47, 0.62)
  );
  backdrop-filter: blur(7px);
  padding: 18px;
  border-radius: 14px;
  box-shadow: 0 14px 32px rgba(0, 0, 0, 0.22);
}

.card h3 {
  margin: 0 0 8px;
}

.card p {
  margin: 6px 0;
  color: #e4f0f6;
}
</style>
