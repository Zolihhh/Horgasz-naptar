<template>
  <div class="grid-wrap catch-view">
    <p v-if="!isLoggedIn" class="status-text">A fogásaid megtekintéséhez jelentkezz be.</p>

    <div v-else class="top-actions">
      <button type="button" class="secondary-btn btn" @click="toggleCreateLogForm">
        {{ showCreateLogForm ? "Űrlap bezárása" : "Új fogási napló" }}
      </button>
      <button type="button" class="primary-btn btn" @click="toggleCreateForm">
        {{ showCreateForm ? "Űrlap bezárása" : "Új fogás rögzítése" }}
      </button>
    </div>
    <p v-if="actionError" class="status-text error-text">{{ actionError }}</p>

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
    <p v-else-if="isLoggedIn && userCatchLogs.length === 0" class="status-text">
      Nincs még fogási naplód.
    </p>

    <CatchCardList
      v-if="isLoggedIn && userCatchLogs.length > 0"
      :catch-logs="catchLogsWithCatches"
      :editing-catch-id="editingCatchId"
      :edit-catch="editCatch"
      :user-catch-logs="userCatchLogs"
      :species="species"
      :lures="lures"
      :updating="updating"
      :deleting-catch-id="deletingCatchId"
      :deleting-catch-log-id="deletingCatchLogId"
      :get-fish-name="getFishName"
      :get-location-name-by-catch-log-id="getLocationNameByCatchLogId"
      :get-lure-name="getLureName"
      :format-catch-time="formatCatchTime"
      :get-catch-log-label="getCatchLogLabel"
      @start-edit="startEdit"
      @update-catch="updateCatch"
      @cancel-edit="cancelEdit"
      @request-delete-catch="requestDeleteCatch"
      @request-delete-log="requestDeleteCatchLog"
    />

    <ConfirmModal
      :is-open-confirm-modal="isConfirmOpen"
      :title="confirmTitle"
      :message="confirmMessage"
      cancel="Mégsem"
      confirm="Igen, törlés"
      @cancel="closeConfirmModal"
      @confirm="confirmDelete"
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
import { CatchForm, CatchLogForm } from "@/components/Forms";
import CatchCardList from "@/components/Catch/CatchCardList.vue";
import ConfirmModal from "@/components/Confirm/ConfirmModal.vue";

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
    ConfirmModal,
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
      deletingCatchId: null,
      deletingCatchLogId: null,
      actionError: "",
      isConfirmOpen: false,
      confirmTitle: "",
      confirmMessage: "",
      confirmType: "",
      confirmId: null,
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
    catchLogsWithCatches() {
      const catchesByLogId = this.catches.reduce((acc, catchItem) => {
        if (!acc[catchItem.catchLogId]) {
          acc[catchItem.catchLogId] = [];
        }
        acc[catchItem.catchLogId].push(catchItem);
        return acc;
      }, {});

      return this.userCatchLogs
        .map((log) => ({
          ...log,
          catches: catchesByLogId[log.id] || [],
        }))
        .sort((a, b) => {
          const dateA = new Date(a.fishing_start || 0).getTime();
          const dateB = new Date(b.fishing_start || 0).getTime();
          return dateB - dateA;
        });
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
      this.actionError = "";
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
      this.actionError = "";
      this.showCreateForm = !this.showCreateForm;
      if (this.showCreateForm) {
        this.showCreateLogForm = false;
        this.cancelEdit();
        this.prefillCreateForm();
      }
    },
    toggleCreateLogForm() {
      this.actionError = "";
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
        locationid: this.locations[0]?.id ?? null,
        fishing_start: today,
        fishing_end: this.toLocalDateInput(tomorrowDate),
        comment: "",
      };
      this.locationSearchTerm = "";
    },
    async createCatchLog() {
      if (!this.currentUserId) return;
      const isDuplicateLog = this.userCatchLogs.some((log) => {
        return (
          Number(log.locationid) === Number(this.newCatchLog.locationid) &&
          String(log.fishing_start) === String(this.newCatchLog.fishing_start) &&
          String(log.fishing_end) === String(this.newCatchLog.fishing_end)
        );
      });
      if (isDuplicateLog) {
        this.actionError = "Ehhez a vízhez és időszakhoz már létezik fogási napló.";
        console.error("[CatchView][createCatchLog] Duplikált napló", {
          payload: this.newCatchLog,
          userId: this.currentUserId,
        });
        return;
      }

      this.savingLog = true;
      this.actionError = "";
      try {
        const payload = { ...this.newCatchLog };
        await this.catchLogStore.create(payload);
        const createdLogId = this.catchLogStore.item?.id ?? null;
        this.showCreateLogForm = false;
        await this.fetchMyCatches();
        if (createdLogId) {
          this.prefillCreateForm(createdLogId);
        }
      } catch (error) {
        this.actionError = this.getErrorMessage(error, "A fogási napló mentése nem sikerült.");
      } finally {
        this.savingLog = false;
      }
    },
    async createCatch() {
      if (this.userCatchLogs.length === 0) return;
      if (Number(this.newCatch.weight) > 100) {
        this.actionError = "A súly mező legnagyobb értéke 100 kg lehet.";
        console.error("[CatchView][createCatch] Súly túl nagy", {
          weight: this.newCatch.weight,
          payload: this.newCatch,
        });
        return;
      }
      if (Number(this.newCatch.length) > 999.9) {
        this.actionError = "A hossz mező legnagyobb értéke 999.9 cm lehet.";
        console.error("[CatchView][createCatch] Hossz túl nagy", {
          length: this.newCatch.length,
          payload: this.newCatch,
        });
        return;
      }
      this.saving = true;
      this.actionError = "";
      try {
        const payload = {
          ...this.newCatch,
          catchTime: this.toMysqlDateTime(this.newCatch.catchTime),
        };
        await this.fishCatchStore.create(payload);
        this.showCreateForm = false;
        await this.fetchMyCatches();
      } catch (error) {
        this.actionError = this.getErrorMessage(error, "A fogás mentése nem sikerült.");
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
      if (Number(this.editCatch.weight) > 100) {
        this.actionError = "A súly mező legnagyobb értéke 100 kg lehet.";
        console.error("[CatchView][updateCatch] Súly túl nagy", {
          id,
          weight: this.editCatch.weight,
          payload: this.editCatch,
        });
        return;
      }
      if (Number(this.editCatch.length) > 999.9) {
        this.actionError = "A hossz mező legnagyobb értéke 999.9 cm lehet.";
        console.error("[CatchView][updateCatch] Hossz túl nagy", {
          id,
          length: this.editCatch.length,
          payload: this.editCatch,
        });
        return;
      }
      this.updating = true;
      try {
        const payload = {
          ...this.editCatch,
          catchTime: this.toMysqlDateTime(this.editCatch.catchTime),
        };
        await this.fishCatchStore.update(id, payload);
        this.cancelEdit();
        await this.fetchMyCatches();
      } catch (error) {
        this.actionError = this.getErrorMessage(error, "A fogás módosítása nem sikerült.");
      } finally {
        this.updating = false;
      }
    },
    requestDeleteCatch(catchItem) {
      this.confirmType = "catch";
      this.confirmId = catchItem?.id ?? null;
      this.confirmTitle = "Fogás törlése";
      this.confirmMessage = "Biztosan törölni szeretnéd ezt a fogást?";
      this.isConfirmOpen = true;
      this.actionError = "";
    },
    requestDeleteCatchLog(catchLog) {
      this.confirmType = "catchLog";
      this.confirmId = catchLog?.id ?? null;
      this.confirmTitle = "Fogási napló törlése";
      this.confirmMessage = "A napló törlésével a hozzá tartozó fogások is törlődnek. Folytatod?";
      this.isConfirmOpen = true;
      this.actionError = "";
    },
    closeConfirmModal() {
      this.isConfirmOpen = false;
      this.confirmType = "";
      this.confirmId = null;
      this.confirmTitle = "";
      this.confirmMessage = "";
    },
    async confirmDelete() {
      if (!this.confirmId) {
        this.closeConfirmModal();
        return;
      }

      if (this.confirmType === "catch") {
        await this.deleteCatch(this.confirmId);
      } else if (this.confirmType === "catchLog") {
        await this.deleteCatchLog(this.confirmId);
      }

      this.closeConfirmModal();
    },
    async deleteCatch(id) {
      this.deletingCatchId = id;
      this.actionError = "";
      try {
        await this.fishCatchStore.delete(id);
        if (this.editingCatchId === id) {
          this.cancelEdit();
        }
        await this.fetchMyCatches();
      } catch (error) {
        this.actionError = this.getErrorMessage(error, "A fogás törlése nem sikerült.");
      } finally {
        this.deletingCatchId = null;
      }
    },
    async deleteCatchLog(id) {
      this.deletingCatchLogId = id;
      this.actionError = "";
      try {
        await this.catchLogStore.delete(id);
        this.cancelEdit();
        await this.fetchMyCatches();
      } catch (error) {
        this.actionError = this.getErrorMessage(error, "A fogási napló törlése nem sikerült.");
      } finally {
        this.deletingCatchLogId = null;
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
    toMysqlDateTime(value) {
      if (!value) return "";
      const normalized = String(value).trim();
      // datetime-local returns YYYY-MM-DDTHH:mm, backend/db expects SQL datetime.
      if (normalized.includes("T")) {
        return `${normalized.replace("T", " ")}:00`;
      }
      return normalized;
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
      return date.toLocaleString("hu-HU", {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
      });
    },
    getErrorMessage(error, fallback) {
      console.error("[CatchView] API hiba", {
        message: error?.message,
        status: error?.response?.status,
        data: error?.response?.data,
        error,
      });
      const responseData = error?.response?.data;
      const message = responseData?.message;
      if (typeof message === "string" && message.includes("Duplicate entry")) {
        return "Ehhez a vízhez és időszakhoz már létezik fogási napló.";
      }
      if (typeof message === "string" && message.includes("fish_catches_catchlogid_foreign")) {
        return "A kiválasztott fogási napló már nem létezik vagy nem elérhető.";
      }
      if (typeof message === "string" && message.includes("fish_catches_specieid_foreign")) {
        return "A kiválasztott halfaj már nem létezik.";
      }
      if (typeof message === "string" && message.includes("fish_catches_lureid_foreign")) {
        return "A kiválasztott csali már nem létezik.";
      }
      if (typeof message === "string" && message.includes("Out of range value for column 'weight'")) {
        return "A súly mező legnagyobb értéke 100 kg lehet.";
      }
      if (typeof message === "string" && message.includes("Out of range value for column 'length'")) {
        return "A megadott hossz túl nagy az adatbázis mezőhöz. Adj meg kisebb értéket.";
      }
      if (typeof message === "string" && message.includes("Integrity constraint violation")) {
        return "Az adatok ütköznek adatbázis szabállyal (kapcsolódó rekord hiányzik vagy duplikált).";
      }
      if (typeof message === "string" && message.trim()) {
        return message;
      }

      const errors = responseData?.errors;
      if (errors && typeof errors === "object") {
        const firstError = Object.values(errors)?.[0];
        if (Array.isArray(firstError) && firstError[0]) {
          return String(firstError[0]);
        }
        if (typeof firstError === "string") {
          return firstError;
        }
      }

      return fallback;
    },
  },
};
</script>
