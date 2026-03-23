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
        <button type="button" class="btn primary-btn create-location-btn" @click="openCreateModal">
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
            :class="{ 'is-invalid': !!createFieldErrors.FishingLakeName }"
            maxlength="80"
            required
            @input="handleCreateInput"
          />
          <div class="invalid-feedback">
            {{ createFieldErrors.FishingLakeName || "Töltsd ki a horgásztó nevét." }}
          </div>
        </div>

        <div class="col-12">
          <label for="create-water-area-code" class="form-label">Vízterület kód</label>
          <input
            id="create-water-area-code"
            v-model.trim="newLocation.waterAreaCode"
            type="text"
            class="form-control"
            :class="{ 'is-invalid': !!createFieldErrors.waterAreaCode }"
            maxlength="20"
            required
            @input="handleCreateInput"
          />
          <div class="invalid-feedback">
            {{ createFieldErrors.waterAreaCode || "Töltsd ki a vízterület kódját." }}
          </div>
        </div>

        <div class="col-md-6">
          <label for="create-latitude" class="form-label">Szélesség</label>
          <input
            id="create-latitude"
            v-model="newLocation.latitude"
            type="number"
            class="form-control"
            :class="{ 'is-invalid': !!createFieldErrors.latitude }"
            step="0.000001"
            min="-90"
            max="90"
            @input="handleCreateInput"
          />
          <div v-if="createFieldErrors.latitude" class="invalid-feedback d-block">
            {{ createFieldErrors.latitude }}
          </div>
        </div>

        <div class="col-md-6">
          <label for="create-longitude" class="form-label">Hosszúság</label>
          <input
            id="create-longitude"
            v-model="newLocation.longitude"
            type="number"
            class="form-control"
            :class="{ 'is-invalid': !!createFieldErrors.longitude }"
            step="0.000001"
            min="-180"
            max="180"
            @input="handleCreateInput"
          />
          <div v-if="createFieldErrors.longitude" class="invalid-feedback d-block">
            {{ createFieldErrors.longitude }}
          </div>
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
            :class="{ 'is-invalid': !!editFieldErrors.FishingLakeName }"
            maxlength="80"
            required
            @input="handleEditInput"
          />
          <div class="invalid-feedback">
            {{ editFieldErrors.FishingLakeName || "Töltsd ki a horgásztó nevét." }}
          </div>
        </div>

        <div class="col-12">
          <label for="edit-water-area-code" class="form-label">Vízterület kód</label>
          <input
            id="edit-water-area-code"
            v-model.trim="editLocation.waterAreaCode"
            type="text"
            class="form-control"
            :class="{ 'is-invalid': !!editFieldErrors.waterAreaCode }"
            maxlength="20"
            required
            @input="handleEditInput"
          />
          <div class="invalid-feedback">
            {{ editFieldErrors.waterAreaCode || "Töltsd ki a vízterület kódját." }}
          </div>
        </div>

        <div class="col-md-6">
          <label for="edit-latitude" class="form-label">Szélesség</label>
          <input
            id="edit-latitude"
            v-model="editLocation.latitude"
            type="number"
            class="form-control"
            :class="{ 'is-invalid': !!editFieldErrors.latitude }"
            step="0.000001"
            min="-90"
            max="90"
            @input="handleEditInput"
          />
          <div v-if="editFieldErrors.latitude" class="invalid-feedback d-block">
            {{ editFieldErrors.latitude }}
          </div>
        </div>

        <div class="col-md-6">
          <label for="edit-longitude" class="form-label">Hosszúság</label>
          <input
            id="edit-longitude"
            v-model="editLocation.longitude"
            type="number"
            class="form-control"
            :class="{ 'is-invalid': !!editFieldErrors.longitude }"
            step="0.000001"
            min="-180"
            max="180"
            @input="handleEditInput"
          />
          <div v-if="editFieldErrors.longitude" class="invalid-feedback d-block">
            {{ editFieldErrors.longitude }}
          </div>
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
import { extractFieldErrors, getApiErrorMessage } from "@/utils/apiValidation";

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
      createFieldErrors: {
        FishingLakeName: "",
        waterAreaCode: "",
        latitude: "",
        longitude: "",
      },
      editFieldErrors: {
        FishingLakeName: "",
        waterAreaCode: "",
        latitude: "",
        longitude: "",
      },
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
      this.createFieldErrors = {
        FishingLakeName: "",
        waterAreaCode: "",
        latitude: "",
        longitude: "",
      };
      this.$nextTick(() => {
        this.$refs.createLocationModal?.show();
      });
    },
    handleCreateInput() {
      this.createError = "";
      this.createFieldErrors = this.validateLocation(this.newLocation);
    },
    handleEditInput() {
      this.editError = "";
      this.editFieldErrors = this.validateLocation(this.editLocation);
    },
    async saveCreatedLocation(done) {
      const payload = {
        FishingLakeName: String(this.newLocation.FishingLakeName || "").trim(),
        waterAreaCode: String(this.newLocation.waterAreaCode || "").trim(),
        latitude: this.normalizeNumber(this.newLocation.latitude),
        longitude: this.normalizeNumber(this.newLocation.longitude),
      };

      this.createFieldErrors = this.validateLocation(payload);
      this.createError = "";

      if (Object.values(this.createFieldErrors).some(Boolean)) {
        done(false);
        return;
      }

      try {
        await this.createLocation(payload);
        done(true);
      } catch (error) {
        const fieldErrors = this.extractLocationFieldErrors(error);
        this.createFieldErrors = fieldErrors;
        this.createError = Object.values(fieldErrors).some(Boolean)
          ? ""
          : this.getErrorMessage(error, "A helyszín létrehozása nem sikerült.");
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
      this.editFieldErrors = {
        FishingLakeName: "",
        waterAreaCode: "",
        latitude: "",
        longitude: "",
      };

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

      this.editFieldErrors = this.validateLocation(payload);
      this.editError = "";

      if (Object.values(this.editFieldErrors).some(Boolean)) {
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

      try {
        await this.updateLocation(this.editLocation.id, payload);
        done(true);
      } catch (error) {
        const fieldErrors = this.extractLocationFieldErrors(error);
        this.editFieldErrors = fieldErrors;
        this.editError = Object.values(fieldErrors).some(Boolean)
          ? ""
          : this.getErrorMessage(error, "A helyszín módosítása nem sikerült.");
        done(false);
      }
    },
    validateLocation(rawPayload) {
      const payload = {
        FishingLakeName: String(rawPayload?.FishingLakeName || "").trim(),
        waterAreaCode: String(rawPayload?.waterAreaCode || "").trim(),
        latitude: rawPayload?.latitude,
        longitude: rawPayload?.longitude,
      };

      const errors = {
        FishingLakeName: "",
        waterAreaCode: "",
        latitude: "",
        longitude: "",
      };

      if (!payload.FishingLakeName) {
        errors.FishingLakeName = "Töltsd ki a horgásztó nevét.";
      } else if (payload.FishingLakeName.length > 80) {
        errors.FishingLakeName = "A horgásztó neve legfeljebb 80 karakter lehet.";
      }

      if (!payload.waterAreaCode) {
        errors.waterAreaCode = "Töltsd ki a vízterület kódját.";
      } else if (payload.waterAreaCode.length > 20) {
        errors.waterAreaCode = "A vízterület kód legfeljebb 20 karakter lehet.";
      }

      if (payload.latitude !== null && payload.latitude !== "") {
        const latitude = Number(payload.latitude);
        if (Number.isNaN(latitude)) {
          errors.latitude = "A szélességi fok szám legyen.";
        } else if (latitude < -90 || latitude > 90) {
          errors.latitude = "A szélességi fok -90 és 90 között lehet.";
        }
      }

      if (payload.longitude !== null && payload.longitude !== "") {
        const longitude = Number(payload.longitude);
        if (Number.isNaN(longitude)) {
          errors.longitude = "A hosszúsági fok szám legyen.";
        } else if (longitude < -180 || longitude > 180) {
          errors.longitude = "A hosszúsági fok -180 és 180 között lehet.";
        }
      }

      return errors;
    },
    extractLocationFieldErrors(error) {
      const fieldErrors = extractFieldErrors(error);
      return {
        FishingLakeName: fieldErrors.FishingLakeName || "",
        waterAreaCode: fieldErrors.waterAreaCode || "",
        latitude: fieldErrors.latitude || "",
        longitude: fieldErrors.longitude || "",
      };
    },
    getErrorMessage(error, fallback) {
      return getApiErrorMessage(error, fallback);
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

.create-location-btn {
  color: #ffffff;
  font-weight: 600;
}

.create-location-btn:hover,
.create-location-btn:focus {
  color: #ffffff;
}
</style>
