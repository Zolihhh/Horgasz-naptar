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
            minlength="2"
            maxlength="100"
            required
          />
        </div>

        <div class="col-12">
          <label for="create-photo" class="form-label">Kép fájlnév</label>
          <input
            id="create-photo"
            v-model.trim="newSpecie.photo"
            type="text"
            class="form-control"
            maxlength="255"
            required
          />
        </div>

        <div class="col-12">
          <label for="create-description" class="form-label">Leírás</label>
          <textarea
            id="create-description"
            v-model.trim="newSpecie.description"
            class="form-control"
            rows="3"
            maxlength="255"
          ></textarea>
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
            minlength="2"
            maxlength="100"
            required
          />
        </div>

        <div class="col-12">
          <label for="edit-photo" class="form-label">Kép fájlnév</label>
          <input
            id="edit-photo"
            v-model.trim="editSpecie.photo"
            type="text"
            class="form-control"
            maxlength="255"
            required
          />
        </div>

        <div class="col-12">
          <label for="edit-description" class="form-label">Leírás</label>
          <textarea
            id="edit-description"
            v-model.trim="editSpecie.description"
            class="form-control"
            rows="3"
            maxlength="255"
          ></textarea>
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
      this.createError = "";
      this.$nextTick(() => {
        this.$refs.createSpecieModal?.show();
      });
    },
    async saveCreatedSpecie(done) {
      const payload = {
        fish_name: String(this.newSpecie.fish_name || "").trim(),
        photo: String(this.newSpecie.photo || "").trim(),
        description: String(this.newSpecie.description || "").trim(),
      };

      if (!payload.fish_name || !payload.photo) {
        this.createError = "A halfaj neve és a kép fájlneve kötelező.";
        done(false);
        return;
      }

      this.createError = "";

      try {
        await this.createSpecie(payload);
        done(true);
      } catch (error) {
        this.createError = this.getErrorMessage(error, "A halfaj létrehozása nem sikerült.");
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
      this.editError = "";

      this.$nextTick(() => {
        this.$refs.editSpecieModal?.show();
      });
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

      if (!payload.fish_name || !payload.photo) {
        this.editError = "A halfaj neve és a kép fájlneve kötelező.";
        done(false);
        return;
      }

      const original = this.editSpecieOriginal;
      if (
        payload.fish_name === String(original.fish_name || "").trim() &&
        payload.photo === String(original.photo || "").trim() &&
        payload.description === String(original.description || "").trim()
      ) {
        done(true);
        return;
      }

      this.editError = "";

      try {
        await this.updateSpecie(this.editSpecie.id, payload);
        done(true);
      } catch (error) {
        this.editError = this.getErrorMessage(error, "A halfaj módosítása nem sikerült.");
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

.create-specie-btn {
  color: #ffffff;
  font-weight: 600;
}

.create-specie-btn:hover,
.create-specie-btn:focus {
  color: #ffffff;
}
</style>
