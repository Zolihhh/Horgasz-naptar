<template>
  <section class="users-wrap users-view">
    <header class="users-head">
      <div class="title-row">
        <h1>{{ pageTitle }}</h1>
        <div class="count-wrap">
          <i v-if="loading" class="bi bi-hourglass-split"></i>
          <p>({{ sortedItems.length }})</p>
        </div>
      </div>

      <div class="search-wrap">
        <input
          v-model.trim="searchTerm"
          type="text"
          class="users-search"
          placeholder="Keresés tó vagy vízterület kód szerint..."
        />
      </div>
    </header>

    <div class="users-table-card">
      <div class="users-toolbar">
        <button type="button" class="btn primary-btn" @click="openCreateModal">
          Új helyszín
        </button>

        <div v-if="sortedItems.length > 0" class="users-controls">
          <SetSelectedPerPage
            v-model="selectedPerPage"
            label="Sor/oldal:"
            :options="[5, 10, 20, 50]"
            all-label="Összes"
          />

          <Pagination
            :current-page="currentPage"
            :last-page="lastPage"
            @change="changePage"
          />
        </div>
      </div>

      <GenericTable
        v-if="paginatedItems.length > 0"
        :items="paginatedItems"
        :columns="tableColumns"
        :useCollectionStore="useCollectionStore"
        :sort-column="sortColumnLocal"
        :sort-direction="sortDirectionLocal"
        :cButtonVisible="false"
        :uButtonVisible="true"
        :dButtonVisible="true"
        :pButtonVisible="false"
        @sort="sortHandler"
        @delete="openDeleteConfirm"
        @update="openEditModal"
      />

      <p v-else class="empty-state">Nincs találat</p>
    </div>

    <ConfirmModal
      :isOpenConfirmModal="isDeleteConfirmOpen"
      title="Törlés megerősítése"
      :message="deleteMessage"
      cancel="Mégse"
      confirm="Törlés"
      @cancel="cancelDelete"
      @confirm="confirmDelete"
    />

    <Modal
      ref="createLocationModal"
      title="Új helyszín"
      yes="Mentés"
      no="Mégse"
      @yesEvent="saveCreatedLocation"
    >
      <div class="row g-3 users-edit-modal">
        <div v-if="createError" class="col-12">
          <div class="alert alert-danger py-2 mb-0">{{ createError }}</div>
        </div>

        <div class="col-12">
          <label for="create-lake-name" class="form-label">Horgásztó neve</label>
          <input
            id="create-lake-name"
            v-model.trim="newLocation.FishingLakeName"
            type="text"
            class="form-control"
            maxlength="80"
            required
          />
        </div>

        <div class="col-12">
          <label for="create-water-area-code" class="form-label">Vízterület kód</label>
          <input
            id="create-water-area-code"
            v-model.trim="newLocation.waterAreaCode"
            type="text"
            class="form-control"
            maxlength="20"
            required
          />
        </div>

        <div class="col-md-6">
          <label for="create-latitude" class="form-label">Szélesség</label>
          <input
            id="create-latitude"
            v-model="newLocation.latitude"
            type="number"
            class="form-control"
            step="0.000001"
            min="-90"
            max="90"
          />
        </div>

        <div class="col-md-6">
          <label for="create-longitude" class="form-label">Hosszúság</label>
          <input
            id="create-longitude"
            v-model="newLocation.longitude"
            type="number"
            class="form-control"
            step="0.000001"
            min="-180"
            max="180"
          />
        </div>
      </div>
    </Modal>

    <Modal
      ref="editLocationModal"
      title="Helyszín módosítása"
      yes="Mentés"
      no="Mégse"
      @yesEvent="saveEditedLocation"
    >
      <div class="row g-3 users-edit-modal">
        <div v-if="editError" class="col-12">
          <div class="alert alert-danger py-2 mb-0">{{ editError }}</div>
        </div>

        <div class="col-12">
          <label for="edit-lake-name" class="form-label">Horgásztó neve</label>
          <input
            id="edit-lake-name"
            v-model.trim="editLocation.FishingLakeName"
            type="text"
            class="form-control"
            maxlength="80"
            required
          />
        </div>

        <div class="col-12">
          <label for="edit-water-area-code" class="form-label">Vízterület kód</label>
          <input
            id="edit-water-area-code"
            v-model.trim="editLocation.waterAreaCode"
            type="text"
            class="form-control"
            maxlength="20"
            required
          />
        </div>

        <div class="col-md-6">
          <label for="edit-latitude" class="form-label">Szélesség</label>
          <input
            id="edit-latitude"
            v-model="editLocation.latitude"
            type="number"
            class="form-control"
            step="0.000001"
            min="-90"
            max="90"
          />
        </div>

        <div class="col-md-6">
          <label for="edit-longitude" class="form-label">Hosszúság</label>
          <input
            id="edit-longitude"
            v-model="editLocation.longitude"
            type="number"
            class="form-control"
            step="0.000001"
            min="-180"
            max="180"
          />
        </div>
      </div>
    </Modal>
  </section>
</template>

<script>
import { mapActions, mapState } from "pinia";
import { useLocationStore } from "@/stores/locationStore";
import GenericTable from "@/components/Table/GenericTable.vue";
import Pagination from "@/components/Pagination/Pagination.vue";
import SetSelectedPerPage from "@/components/Pagination/SetSelectedPerPage.vue";
import ConfirmModal from "@/components/Confirm/ConfirmModal.vue";
import Modal from "@/components/Modal/Modal.vue";

function emptyLocation() {
  return {
    waterAreaCode: "",
    latitude: "",
    longitude: "",
    FishingLakeName: "",
  };
}

export default {
  name: "LocationsView",
  components: {
    ConfirmModal,
    GenericTable,
    Modal,
    Pagination,
    SetSelectedPerPage,
  },
  watch: {
    filteredItems() {
      this.ensureValidPage();
    },
    lastPage() {
      this.ensureValidPage();
    },
  },
  data() {
    return {
      pageTitle: "Helyszínek kezelése",
      searchTerm: "",
      currentPage: 1,
      selectedPerPage: 10,
      sortColumnLocal: "id",
      sortDirectionLocal: "asc",
      isDeleteConfirmOpen: false,
      pendingDeleteId: null,
      createError: "",
      editError: "",
      newLocation: emptyLocation(),
      editLocation: {
        id: null,
        ...emptyLocation(),
      },
      editLocationOriginal: emptyLocation(),
      tableColumns: [
        { key: "id", label: "ID", debug: 2 },
        { key: "FishingLakeName", label: "Horgásztó", debug: 2 },
        { key: "waterAreaCode", label: "Vízterület kód", debug: 2 },
        { key: "latitude", label: "Szélesség", debug: 2 },
        { key: "longitude", label: "Hosszúság", debug: 2 },
      ],
      useCollectionStore: useLocationStore,
    };
  },
  computed: {
    ...mapState(useLocationStore, ["items", "loading"]),
    filteredItems() {
      const term = this.searchTerm.toLowerCase();
      if (!term) {
        return this.items;
      }

      return this.items.filter((item) => {
        return (
          String(item.FishingLakeName || "").toLowerCase().includes(term) ||
          String(item.waterAreaCode || "").toLowerCase().includes(term)
        );
      });
    },
    sortedItems() {
      const list = [...this.filteredItems];
      const key = this.sortColumnLocal;
      const directionMultiplier = this.sortDirectionLocal === "asc" ? 1 : -1;

      return list.sort((a, b) => {
        let valueA = a?.[key];
        let valueB = b?.[key];

        if (["id", "latitude", "longitude"].includes(key)) {
          valueA = Number(valueA || 0);
          valueB = Number(valueB || 0);
          return (valueA - valueB) * directionMultiplier;
        }

        valueA = String(valueA || "").toLowerCase();
        valueB = String(valueB || "").toLowerCase();
        return valueA.localeCompare(valueB, "hu") * directionMultiplier;
      });
    },
    effectivePerPage() {
      if (this.selectedPerPage >= 1000000) {
        return Math.max(1, this.sortedItems.length);
      }
      return Math.max(1, Number(this.selectedPerPage) || 10);
    },
    lastPage() {
      return Math.max(1, Math.ceil(this.sortedItems.length / this.effectivePerPage));
    },
    paginatedItems() {
      if (!this.sortedItems.length) {
        return [];
      }

      const start = (this.currentPage - 1) * this.effectivePerPage;
      const end = start + this.effectivePerPage;
      return this.sortedItems.slice(start, end);
    },
    deleteMessage() {
      if (!this.pendingDeleteId) {
        return "Biztosan törölni szeretnéd ezt a helyszínt?";
      }

      const location = this.items.find((item) => item.id === this.pendingDeleteId);
      if (!location) {
        return `Biztosan törölni szeretnéd a helyszínt (ID: ${this.pendingDeleteId})?`;
      }

      return `Biztosan törölni szeretnéd a helyszínt (${location.FishingLakeName})?`;
    },
  },
  methods: {
    ...mapActions(useLocationStore, {
      createLocation: "create",
      deleteLocation: "delete",
      getAll: "getAll",
      updateLocation: "update",
    }),
    sortHandler(column) {
      if (this.sortColumnLocal === column) {
        this.sortDirectionLocal = this.sortDirectionLocal === "asc" ? "desc" : "asc";
      } else {
        this.sortColumnLocal = column;
        this.sortDirectionLocal = "asc";
      }
      this.currentPage = 1;
    },
    changePage(page) {
      this.currentPage = page;
      this.ensureValidPage();
    },
    normalizeNumber(value) {
      if (value === null || value === undefined || value === "") {
        return null;
      }

      const normalized = Number(value);
      return Number.isNaN(normalized) ? value : normalized;
    },
    openCreateModal() {
      this.newLocation = emptyLocation();
      this.createError = "";
      this.$nextTick(() => {
        this.$refs.createLocationModal?.show();
      });
    },
    async saveCreatedLocation(done) {
      const payload = {
        FishingLakeName: String(this.newLocation.FishingLakeName || "").trim(),
        waterAreaCode: String(this.newLocation.waterAreaCode || "").trim(),
        latitude: this.normalizeNumber(this.newLocation.latitude),
        longitude: this.normalizeNumber(this.newLocation.longitude),
      };

      if (!payload.FishingLakeName || !payload.waterAreaCode) {
        this.createError = "A tó neve és a vízterület kód kötelező.";
        done(false);
        return;
      }

      this.createError = "";

      try {
        await this.createLocation(payload);
        done(true);
      } catch (error) {
        this.createError = this.getErrorMessage(error, "A helyszín létrehozása nem sikerült.");
        done(false);
      }
    },
    openDeleteConfirm(id) {
      this.pendingDeleteId = id;
      this.isDeleteConfirmOpen = true;
    },
    cancelDelete() {
      this.pendingDeleteId = null;
      this.isDeleteConfirmOpen = false;
    },
    async confirmDelete() {
      const id = this.pendingDeleteId;
      if (!id) {
        this.cancelDelete();
        return;
      }

      try {
        await this.deleteLocation(id);
      } finally {
        this.cancelDelete();
        this.ensureValidPage();
      }
    },
    openEditModal(id) {
      const location = this.items.find((item) => item.id === id);
      if (!location) {
        return;
      }

      this.editLocation = {
        id: location.id,
        waterAreaCode: String(location.waterAreaCode || ""),
        latitude: location.latitude ?? "",
        longitude: location.longitude ?? "",
        FishingLakeName: String(location.FishingLakeName || ""),
      };
      this.editLocationOriginal = {
        waterAreaCode: String(location.waterAreaCode || ""),
        latitude: location.latitude ?? "",
        longitude: location.longitude ?? "",
        FishingLakeName: String(location.FishingLakeName || ""),
      };
      this.editError = "";

      this.$nextTick(() => {
        this.$refs.editLocationModal?.show();
      });
    },
    async saveEditedLocation(done) {
      if (!this.editLocation.id) {
        this.editError = "Hiányzó helyszín azonosító.";
        done(false);
        return;
      }

      const payload = {
        FishingLakeName: String(this.editLocation.FishingLakeName || "").trim(),
        waterAreaCode: String(this.editLocation.waterAreaCode || "").trim(),
        latitude: this.normalizeNumber(this.editLocation.latitude),
        longitude: this.normalizeNumber(this.editLocation.longitude),
      };

      if (!payload.FishingLakeName || !payload.waterAreaCode) {
        this.editError = "A tó neve és a vízterület kód kötelező.";
        done(false);
        return;
      }

      const original = this.editLocationOriginal;
      if (
        payload.FishingLakeName === String(original.FishingLakeName || "").trim() &&
        payload.waterAreaCode === String(original.waterAreaCode || "").trim() &&
        String(payload.latitude ?? "") === String(original.latitude ?? "") &&
        String(payload.longitude ?? "") === String(original.longitude ?? "")
      ) {
        done(true);
        return;
      }

      this.editError = "";

      try {
        await this.updateLocation(this.editLocation.id, payload);
        done(true);
      } catch (error) {
        this.editError = this.getErrorMessage(error, "A helyszín módosítása nem sikerült.");
        done(false);
      }
    },
    getErrorMessage(error, fallback) {
      const responseData = error?.response?.data;
      const message = responseData?.message;
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
    ensureValidPage() {
      if (this.currentPage < 1) {
        this.currentPage = 1;
      }
      if (this.currentPage > this.lastPage) {
        this.currentPage = this.lastPage;
      }
    },
  },
  async mounted() {
    await this.getAll();
    this.ensureValidPage();
  },
};
</script>

<style scoped>
.users-toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  flex-wrap: wrap;
}
</style>
