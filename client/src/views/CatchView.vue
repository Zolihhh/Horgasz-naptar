<template>
  <div class="grid-wrap catch-view">
    <div class="page-head">
      <h1>Fogások</h1>
      <p>Saját fogásaid egy helyen</p>
    </div>

    <p v-if="!isLoggedIn" class="status-text">A fogásaid megtekintéséhez jelentkezz be.</p>

    <div v-else class="top-actions">
      <button type="button" class="secondary-btn" @click="toggleCreateLogForm">
        {{ showCreateLogForm ? "Űrlap bezárása" : "Új fogási napló" }}
      </button>
      <button type="button" class="primary-btn" @click="toggleCreateForm">
        {{ showCreateForm ? "Űrlap bezárása" : "Új fogás rögzítése" }}
      </button>
    </div>

    <CatchLogForm
      v-if="isLoggedIn && showCreateLogForm"
      :new-catch-log="newCatchLog"
      :location-search-term="locationSearchTerm"
      :filtered-locations="filteredLocations"
      :saving-log="savingLog"
      :get-location-name="getLocationName"
      @update:location-search-term="locationSearchTerm = $event"
      @submit="createCatchLog"
    />

    <CatchForm
      v-if="isLoggedIn && showCreateForm"
      :new-catch="newCatch"
      :user-catch-logs="userCatchLogs"
      :species="species"
      :lures="lures"
      :saving="saving"
      :get-catch-log-label="getCatchLogLabel"
      @submit="createCatch"
    />

    <p v-if="isLoggedIn && loading" class="status-text">Betöltés...</p>
    <p v-else-if="isLoggedIn && catches.length === 0" class="status-text">Nincs rögzített fogásod.</p>

    <CatchCardList
      v-if="isLoggedIn && catches.length > 0"
      :catches="catches"
      :editing-catch-id="editingCatchId"
      :edit-catch="editCatch"
      :user-catch-logs="userCatchLogs"
      :species="species"
      :lures="lures"
      :updating="updating"
      :get-fish-name="getFishName"
      :get-location-name-by-catch-log-id="getLocationNameByCatchLogId"
      :get-lure-name="getLureName"
      :format-catch-time="formatCatchTime"
      :get-catch-log-label="getCatchLogLabel"
      @start-edit="startEdit"
      @update-catch="updateCatch"
      @cancel-edit="cancelEdit"
    />
  </div>
</template>

<script>
import { mapState } from "pinia";
import { useUserLoginLogoutStore } from "@/stores/userLoginLogoutStore";
import { useFishCatchStore } from "@/stores/fishCatchStore";
import { useSpecieStore } from "@/stores/specieStore";
import { useLureStore } from "@/stores/lureStore";
import { useCatchLogStore } from "@/stores/catchLogStore";
import { useLocationStore } from "@/stores/locationStore";
import CatchLogForm from "@/components/Catch/CatchLogForm.vue";
import CatchForm from "@/components/Catch/CatchForm.vue";
import CatchCardList from "@/components/Catch/CatchCardList.vue";

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
  components: {
    CatchLogForm,
    CatchForm,
    CatchCardList,
  },
  data() {
    return {
      catches: [],
      species: [],
      lures: [],
      locations: [],
      userCatchLogs: [],
      catchLogsById: {},
      speciesById: {},
      luresById: {},
      locationsById: {},
      showCreateForm: false,
      showCreateLogForm: false,
      newCatch: emptyCatch(),
      newCatchLog: {
        userid: null,
        locationid: null,
        fishing_start: "",
        fishing_end: "",
        comment: "",
      },
      locationSearchTerm: "",
      editCatch: emptyCatch(),
      editingCatchId: null,
      loading: false,
      saving: false,
      savingLog: false,
      updating: false,
      fishCatchStore: useFishCatchStore(),
      specieStore: useSpecieStore(),
      lureStore: useLureStore(),
      catchLogStore: useCatchLogStore(),
      locationStore: useLocationStore(),
    };
  },
  computed: {
    ...mapState(useUserLoginLogoutStore, ["isLoggedIn", "item"]),
    currentUserId() {
      return this.item?.id ?? null;
    },
    filteredLocations() {
      const term = this.locationSearchTerm.toLowerCase();
      if (!term) return this.locations;
      return this.locations.filter((location) =>
        this.getLocationName(location.id).toLowerCase().includes(term),
      );
    },
  },
  watch: {
    isLoggedIn(newValue) {
      if (newValue) {
        this.fetchMyCatches();
      } else {
        this.catches = [];
        this.userCatchLogs = [];
        this.catchLogsById = {};
        this.showCreateForm = false;
        this.showCreateLogForm = false;
        this.editingCatchId = null;
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
        await Promise.all([
          this.fishCatchStore.getMyCatches(),
          this.specieStore.getAll(),
          this.lureStore.getAll(),
          this.catchLogStore.getAll(),
          this.locationStore.getAll(),
        ]);

        this.catches = this.fishCatchStore.items;
        this.species = this.specieStore.items;
        this.lures = this.lureStore.items;
        this.locations = this.locationStore.items;
        this.speciesById = this.species.reduce((acc, item) => {
          acc[item.id] = item.fish_name;
          return acc;
        }, {});
        this.luresById = this.lures.reduce((acc, item) => {
          acc[item.id] = item.lure;
          return acc;
        }, {});
        this.locationsById = this.locations.reduce((acc, item) => {
          const name = item.FishingLakeName || item.fishingLakeName || `Víz #${item.id}`;
          acc[item.id] = name;
          return acc;
        }, {});
        this.userCatchLogs = this.catchLogStore.items.filter((item) => item.userid === this.currentUserId);
        this.catchLogsById = this.userCatchLogs.reduce((acc, item) => {
          acc[item.id] = item;
          return acc;
        }, {});
        this.prefillCreateForm();
        this.prefillCreateLogForm();
      } catch (error) {
        this.catches = [];
        this.species = [];
        this.lures = [];
        this.locations = [];
        this.userCatchLogs = [];
        this.catchLogsById = {};
        this.speciesById = {};
        this.luresById = {};
        this.locationsById = {};
      } finally {
        this.loading = false;
      }
    },
    toggleCreateForm() {
      this.showCreateForm = !this.showCreateForm;
      if (this.showCreateForm) {
        this.showCreateLogForm = false;
        this.cancelEdit();
        this.prefillCreateForm();
      }
    },
    toggleCreateLogForm() {
      this.showCreateLogForm = !this.showCreateLogForm;
      if (this.showCreateLogForm) {
        this.showCreateForm = false;
        this.cancelEdit();
        this.prefillCreateLogForm();
      }
    },
    prefillCreateForm(preferredCatchLogId = null) {
      const now = new Date();
      const localDateTime = new Date(now.getTime() - now.getTimezoneOffset() * 60000)
        .toISOString()
        .slice(0, 16);
      const isPreferredValid = this.userCatchLogs.some((item) => item.id === preferredCatchLogId);
      const catchLogId = isPreferredValid ? preferredCatchLogId : (this.userCatchLogs[0]?.id ?? null);

      this.newCatch = {
        catchLogId,
        specieId: this.species[0]?.id ?? null,
        lureId: this.lures[0]?.id ?? null,
        weight: null,
        length: null,
        catchTime: localDateTime,
      };
    },
    prefillCreateLogForm() {
      const today = this.toLocalDateInput(new Date());
      const tomorrowDate = new Date();
      tomorrowDate.setDate(tomorrowDate.getDate() + 1);

      this.newCatchLog = {
        userid: this.currentUserId,
        locationid: this.locations[0]?.id ?? null,
        fishing_start: today,
        fishing_end: this.toLocalDateInput(tomorrowDate),
        comment: "",
      };
      this.locationSearchTerm = "";
    },
    async createCatchLog() {
      if (!this.currentUserId) return;
      this.savingLog = true;
      try {
        const payload = {
          ...this.newCatchLog,
          userid: this.currentUserId,
        };
        await this.catchLogStore.create(payload);
        const createdLogId = this.catchLogStore.item?.id ?? null;
        this.showCreateLogForm = false;
        await this.fetchMyCatches();
        if (createdLogId) {
          this.prefillCreateForm(createdLogId);
        }
      } catch (error) {
        // Hibakezelés az axios interceptorban történik
      } finally {
        this.savingLog = false;
      }
    },
    async createCatch() {
      if (this.userCatchLogs.length === 0) return;
      this.saving = true;
      try {
        await this.fishCatchStore.create(this.newCatch);
        this.showCreateForm = false;
        await this.fetchMyCatches();
      } catch (error) {
        // Hibakezelés az axios interceptorban történik
      } finally {
        this.saving = false;
      }
    },
    startEdit(catchItem) {
      this.showCreateForm = false;
      this.editingCatchId = catchItem.id;
      this.editCatch = {
        catchLogId: catchItem.catchLogId,
        specieId: catchItem.specieId,
        lureId: catchItem.lureId,
        weight: Number(catchItem.weight),
        length: Number(catchItem.length),
        catchTime: this.toLocalDateTimeInput(catchItem.catchTime),
      };
    },
    cancelEdit() {
      this.editingCatchId = null;
      this.editCatch = emptyCatch();
    },
    async updateCatch(id) {
      this.updating = true;
      try {
        await this.fishCatchStore.update(id, this.editCatch);
        this.cancelEdit();
        await this.fetchMyCatches();
      } catch (error) {
        // Hibakezelés az axios interceptorban történik
      } finally {
        this.updating = false;
      }
    },
    toLocalDateTimeInput(value) {
      if (!value) return "";
      const date = new Date(value);
      if (Number.isNaN(date.getTime())) return "";
      return new Date(date.getTime() - date.getTimezoneOffset() * 60000)
        .toISOString()
        .slice(0, 16);
    },
    toLocalDateInput(dateValue) {
      const date = new Date(dateValue);
      if (Number.isNaN(date.getTime())) return "";
      return new Date(date.getTime() - date.getTimezoneOffset() * 60000)
        .toISOString()
        .slice(0, 10);
    },
    getFishName(specieId) {
      return this.speciesById[specieId] || `Ismeretlen halfaj (#${specieId})`;
    },
    getLureName(lureId) {
      return this.luresById[lureId] || `Ismeretlen csali (#${lureId})`;
    },
    getLocationName(locationId) {
      return this.locationsById[locationId] || `Ismeretlen víz (#${locationId})`;
    },
    getLocationNameByCatchLogId(catchLogId) {
      const catchLog = this.catchLogsById[catchLogId];
      if (!catchLog) return `Ismeretlen víz (#${catchLogId})`;
      return this.locationsById[catchLog.locationid] || `Ismeretlen víz (#${catchLog.locationid})`;
    },
    getCatchLogLabel(log) {
      const locationName = this.locationsById[log.locationid] || `Ismeretlen víz (#${log.locationid})`;
      return `${locationName} | #${log.id} | ${log.fishing_start || "-"} - ${log.fishing_end || "-"}`;
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
