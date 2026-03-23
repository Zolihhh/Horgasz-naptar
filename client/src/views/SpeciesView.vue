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
          placeholder="Keresés halfaj vagy képnév szerint..."
        />
      </div>
    </header>

    <div class="users-table-card">
      <div class="users-toolbar">
        <button type="button" class="btn primary-btn create-specie-btn" @click="openCreateModal">
          Új halfaj
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
      ref="createSpecieModal"
      title="Új halfaj"
      yes="Mentés"
      no="Mégse"
      @yesEvent="saveCreatedSpecie"
    >
      <div class="row g-3 users-edit-modal">
        <div v-if="createError" class="col-12">
          <div class="alert alert-danger py-2 mb-0">{{ createError }}</div>
        </div>

        <div class="col-12">
          <label for="create-fish-name" class="form-label">Halfaj neve</label>
          <input
            id="create-fish-name"
            v-model.trim="newSpecie.fish_name"
            type="text"
            class="form-control"
            :class="{ 'is-invalid': !!createFieldErrors.fish_name }"
            minlength="2"
            maxlength="100"
            required
            @input="handleCreateInput"
          />
          <div class="invalid-feedback">
            {{ createFieldErrors.fish_name || "Töltsd ki a halfaj nevét." }}
          </div>
        </div>

        <div class="col-12">
          <label for="create-photo" class="form-label">Kép feltöltése</label>
          <input
            id="create-photo"
            type="file"
            class="form-control"
            :class="{ 'is-invalid': !!createFieldErrors.photo }"
            accept="image/*"
            required
            @change="onCreatePhotoChange"
          />
          <div class="invalid-feedback">
            {{ createFieldErrors.photo || "Tölts fel egy képet a gépről." }}
          </div>
          <div v-if="newSpecie.photo" class="form-text text-light-emphasis">
            Kiválasztott fájl: {{ newSpecie.photo }}
          </div>
        </div>

        <div class="col-12">
          <label for="create-description" class="form-label">Leírás</label>
          <textarea
            id="create-description"
            v-model.trim="newSpecie.description"
            class="form-control"
            :class="{ 'is-invalid': !!createFieldErrors.description }"
            rows="3"
            maxlength="255"
            @input="handleCreateInput"
          ></textarea>
          <div v-if="createFieldErrors.description" class="invalid-feedback d-block">
            {{ createFieldErrors.description }}
          </div>
        </div>
      </div>
    </Modal>

    <Modal
      ref="editSpecieModal"
      title="Halfaj módosítása"
      yes="Mentés"
      no="Mégse"
      @yesEvent="saveEditedSpecie"
    >
      <div class="row g-3 users-edit-modal">
        <div v-if="editError" class="col-12">
          <div class="alert alert-danger py-2 mb-0">{{ editError }}</div>
        </div>

        <div class="col-12">
          <label for="edit-fish-name" class="form-label">Halfaj neve</label>
          <input
            id="edit-fish-name"
            v-model.trim="editSpecie.fish_name"
            type="text"
            class="form-control"
            :class="{ 'is-invalid': !!editFieldErrors.fish_name }"
            minlength="2"
            maxlength="100"
            required
            @input="handleEditInput"
          />
          <div class="invalid-feedback">
            {{ editFieldErrors.fish_name || "Töltsd ki a halfaj nevét." }}
          </div>
        </div>

        <div class="col-12">
          <label for="edit-photo" class="form-label">Kép feltöltése</label>
          <input
            id="edit-photo"
            type="file"
            class="form-control"
            :class="{ 'is-invalid': !!editFieldErrors.photo }"
            accept="image/*"
            @change="onEditPhotoChange"
          />
          <div class="invalid-feedback">
            {{ editFieldErrors.photo || "Tölts fel egy képet a gépről." }}
          </div>
          <div v-if="editSpecie.photo" class="form-text text-light-emphasis">
            Jelenlegi kép: {{ editSpecie.photo }}
          </div>
          <div v-if="editSpeciePhotoFile" class="form-text text-light-emphasis">
            Új fájl: {{ editSpeciePhotoFile.name }}
          </div>
        </div>

        <div class="col-12">
          <label for="edit-description" class="form-label">Leírás</label>
          <textarea
            id="edit-description"
            v-model.trim="editSpecie.description"
            class="form-control"
            :class="{ 'is-invalid': !!editFieldErrors.description }"
            rows="3"
            maxlength="255"
            @input="handleEditInput"
          ></textarea>
          <div v-if="editFieldErrors.description" class="invalid-feedback d-block">
            {{ editFieldErrors.description }}
          </div>
        </div>
      </div>
    </Modal>
  </section>
</template>

<script>
import { mapActions, mapState } from "pinia";
import { useSpecieStore } from "@/stores/specieStore";
import GenericTable from "@/components/Table/GenericTable.vue";
import Pagination from "@/components/Pagination/Pagination.vue";
import SetSelectedPerPage from "@/components/Pagination/SetSelectedPerPage.vue";
import ConfirmModal from "@/components/Confirm/ConfirmModal.vue";
import Modal from "@/components/Modal/Modal.vue";
import { extractFieldErrors, getApiErrorMessage } from "@/utils/apiValidation";

function emptySpecie() {
  return {
    fish_name: "",
    photo: "",
    description: "",
  };
}

export default {
  name: "SpeciesView",
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
      pageTitle: "Halfajok kezelése",
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
        fish_name: "",
        photo: "",
        description: "",
      },
      editFieldErrors: {
        fish_name: "",
        photo: "",
        description: "",
      },
      newSpeciePhotoFile: null,
      editSpeciePhotoFile: null,
      newSpecie: emptySpecie(),
      editSpecie: {
        id: null,
        ...emptySpecie(),
      },
      editSpecieOriginal: emptySpecie(),
      tableColumns: [
        { key: "id", label: "ID", debug: 2 },
        { key: "fish_name", label: "Halfaj", debug: 2 },
        { key: "photo", label: "Kép", debug: 2 },
        { key: "description", label: "Leírás", debug: 2 },
      ],
      useCollectionStore: useSpecieStore,
    };
  },
  computed: {
    ...mapState(useSpecieStore, ["items", "loading"]),
    filteredItems() {
      const term = this.searchTerm.toLowerCase();
      if (!term) {
        return this.items;
      }

      return this.items.filter((item) => {
        return (
          String(item.fish_name || "").toLowerCase().includes(term) ||
          String(item.photo || "").toLowerCase().includes(term) ||
          String(item.description || "").toLowerCase().includes(term)
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

        if (key === "id") {
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
        return "Biztosan törölni szeretnéd ezt a halfajt?";
      }

      const specie = this.items.find((item) => item.id === this.pendingDeleteId);
      if (!specie) {
        return `Biztosan törölni szeretnéd a halfajt (ID: ${this.pendingDeleteId})?`;
      }

      return `Biztosan törölni szeretnéd a halfajt (${specie.fish_name})?`;
    },
  },
  methods: {
    ...mapActions(useSpecieStore, {
      createSpecie: "create",
      deleteSpecie: "delete",
      getAll: "getAll",
      updateSpecie: "update",
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
    openCreateModal() {
      this.newSpecie = emptySpecie();
      this.newSpeciePhotoFile = null;
      this.createError = "";
      this.createFieldErrors = {
        fish_name: "",
        photo: "",
        description: "",
      };
      this.$nextTick(() => {
        this.$refs.createSpecieModal?.show();
      });
    },
    handleCreateInput() {
      this.createError = "";
      this.createFieldErrors = this.validateSpecie(this.newSpecie, this.newSpeciePhotoFile, true);
    },
    onCreatePhotoChange(event) {
      const file = event?.target?.files?.[0] ?? null;
      this.newSpeciePhotoFile = file;
      this.newSpecie.photo = file?.name || "";
      this.createError = "";
      this.createFieldErrors = this.validateSpecie(this.newSpecie, this.newSpeciePhotoFile, true);
    },
    async saveCreatedSpecie(done) {
      const payload = {
        fish_name: String(this.newSpecie.fish_name || "").trim(),
        description: String(this.newSpecie.description || "").trim(),
      };

      this.createFieldErrors = this.validateSpecie(payload, this.newSpeciePhotoFile, true);
      this.createError = "";

      if (Object.values(this.createFieldErrors).some(Boolean)) {
        done(false);
        return;
      }

      try {
        await this.createSpecie(this.buildSpecieFormData(payload, this.newSpeciePhotoFile));
        done(true);
      } catch (error) {
        const fieldErrors = this.extractSpecieFieldErrors(error);
        this.createFieldErrors = fieldErrors;
        this.createError = Object.values(fieldErrors).some(Boolean)
          ? ""
          : this.getErrorMessage(error, "A halfaj létrehozása nem sikerült.");
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
        await this.deleteSpecie(id);
      } finally {
        this.cancelDelete();
        this.ensureValidPage();
      }
    },
    openEditModal(id) {
      const specie = this.items.find((item) => item.id === id);
      if (!specie) {
        return;
      }

      this.editSpecie = {
        id: specie.id,
        fish_name: String(specie.fish_name || ""),
        photo: String(specie.photo || ""),
        description: String(specie.description || ""),
      };
      this.editSpecieOriginal = {
        fish_name: String(specie.fish_name || ""),
        photo: String(specie.photo || ""),
        description: String(specie.description || ""),
      };
      this.editSpeciePhotoFile = null;
      this.editError = "";
      this.editFieldErrors = {
        fish_name: "",
        photo: "",
        description: "",
      };

      this.$nextTick(() => {
        this.$refs.editSpecieModal?.show();
      });
    },
    handleEditInput() {
      this.editError = "";
      this.editFieldErrors = this.validateSpecie(this.editSpecie, this.editSpeciePhotoFile, false);
    },
    onEditPhotoChange(event) {
      const file = event?.target?.files?.[0] ?? null;
      this.editSpeciePhotoFile = file;
      this.editError = "";
      this.editFieldErrors = this.validateSpecie(this.editSpecie, this.editSpeciePhotoFile, false);
    },
    async saveEditedSpecie(done) {
      if (!this.editSpecie.id) {
        this.editError = "Hiányzó halfaj azonosító.";
        done(false);
        return;
      }

      const payload = {
        fish_name: String(this.editSpecie.fish_name || "").trim(),
        photo: String(this.editSpecie.photo || "").trim(),
        description: String(this.editSpecie.description || "").trim(),
      };

      this.editFieldErrors = this.validateSpecie(payload, this.editSpeciePhotoFile, false);
      this.editError = "";

      if (Object.values(this.editFieldErrors).some(Boolean)) {
        done(false);
        return;
      }

      const original = this.editSpecieOriginal;
      if (
        payload.fish_name === String(original.fish_name || "").trim() &&
        payload.photo === String(original.photo || "").trim() &&
        !this.editSpeciePhotoFile &&
        payload.description === String(original.description || "").trim()
      ) {
        done(true);
        return;
      }

      try {
        await this.updateSpecie(
          this.editSpecie.id,
          this.buildSpecieFormData(payload, this.editSpeciePhotoFile, payload.photo),
        );
        done(true);
      } catch (error) {
        const fieldErrors = this.extractSpecieFieldErrors(error);
        this.editFieldErrors = fieldErrors;
        this.editError = Object.values(fieldErrors).some(Boolean)
          ? ""
          : this.getErrorMessage(error, "A halfaj módosítása nem sikerült.");
        done(false);
      }
    },
    validateSpecie(rawPayload, photoFile, requirePhoto) {
      const payload = {
        fish_name: String(rawPayload?.fish_name || "").trim(),
        description: String(rawPayload?.description || "").trim(),
      };

      const errors = {
        fish_name: "",
        photo: "",
        description: "",
      };

      if (!payload.fish_name) {
        errors.fish_name = "Töltsd ki a halfaj nevét.";
      } else if (payload.fish_name.length < 2) {
        errors.fish_name = "A halfaj neve legalább 2 karakter legyen.";
      } else if (payload.fish_name.length > 100) {
        errors.fish_name = "A halfaj neve legfeljebb 100 karakter lehet.";
      }

      if (requirePhoto && !photoFile) {
        errors.photo = "Tölts fel egy képet a gépről.";
      } else if (photoFile) {
        if (!String(photoFile.type || "").startsWith("image/")) {
          errors.photo = "Csak képfájlt tölthetsz fel.";
        } else if (photoFile.size > 5 * 1024 * 1024) {
          errors.photo = "A kép legfeljebb 5 MB lehet.";
        }
      } else if (!requirePhoto && !String(rawPayload?.photo || "").trim()) {
        errors.photo = "Tölts fel egy képet a gépről.";
      }

      if (payload.description.length > 255) {
        errors.description = "A leírás legfeljebb 255 karakter lehet.";
      }

      return errors;
    },
    extractSpecieFieldErrors(error) {
      const fieldErrors = extractFieldErrors(error);
      return {
        fish_name: fieldErrors.fish_name || "",
        photo: fieldErrors.photo || "",
        description: fieldErrors.description || "",
      };
    },
    buildSpecieFormData(payload, photoFile, existingPhoto = "") {
      const formData = new FormData();
      formData.append("fish_name", payload.fish_name);
      formData.append("description", payload.description || "");

      if (photoFile) {
        formData.append("photo", photoFile);
      }

      if (existingPhoto) {
        formData.append("existing_photo", existingPhoto);
      }

      return formData;
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

.create-specie-btn {
  color: #ffffff;
  font-weight: 600;
}

.create-specie-btn:hover,
.create-specie-btn:focus {
  color: #ffffff;
}
</style>
