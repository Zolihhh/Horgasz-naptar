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
          placeholder="Keresés csali név szerint..."
        />
      </div>
    </header>

    <div class="users-table-card">
      <div class="users-toolbar">
        <button type="button" class="btn primary-btn create-lure-btn" @click="openCreateModal">
          Új csali
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
      ref="createLureModal"
      title="Új csali"
      yes="Mentés"
      no="Mégse"
      @yesEvent="saveCreatedLure"
    >
      <div class="row g-3 users-edit-modal">
        <div v-if="createError" class="col-12">
          <div class="alert alert-danger py-2 mb-0">{{ createError }}</div>
        </div>

        <div class="col-12">
          <label for="create-lure" class="form-label">Csali neve</label>
          <input
            id="create-lure"
            v-model.trim="newLure.lure"
            type="text"
            class="form-control"
            minlength="2"
            maxlength="75"
            required
          />
        </div>
      </div>
    </Modal>

    <Modal
      ref="editLureModal"
      title="Csali módosítása"
      yes="Mentés"
      no="Mégse"
      @yesEvent="saveEditedLure"
    >
      <div class="row g-3 users-edit-modal">
        <div v-if="editError" class="col-12">
          <div class="alert alert-danger py-2 mb-0">{{ editError }}</div>
        </div>

        <div class="col-12">
          <label for="edit-lure" class="form-label">Csali neve</label>
          <input
            id="edit-lure"
            v-model.trim="editLure.lure"
            type="text"
            class="form-control"
            minlength="2"
            maxlength="75"
            required
          />
        </div>
      </div>
    </Modal>
  </section>
</template>

<script>
import { mapActions, mapState } from "pinia";
import { useLureStore } from "@/stores/lureStore";
import GenericTable from "@/components/Table/GenericTable.vue";
import Pagination from "@/components/Pagination/Pagination.vue";
import SetSelectedPerPage from "@/components/Pagination/SetSelectedPerPage.vue";
import ConfirmModal from "@/components/Confirm/ConfirmModal.vue";
import Modal from "@/components/Modal/Modal.vue";

export default {
  name: "LuresView",
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
      pageTitle: "Csalik kezelése",
      searchTerm: "",
      currentPage: 1,
      selectedPerPage: 10,
      sortColumnLocal: "id",
      sortDirectionLocal: "asc",
      isDeleteConfirmOpen: false,
      pendingDeleteId: null,
      createError: "",
      editError: "",
      newLure: {
        lure: "",
      },
      editLure: {
        id: null,
        lure: "",
      },
      editLureOriginal: {
        lure: "",
      },
      tableColumns: [
        { key: "id", label: "ID", debug: 2 },
        { key: "lure", label: "Csali", debug: 2 },
      ],
      useCollectionStore: useLureStore,
    };
  },
  computed: {
    ...mapState(useLureStore, ["items", "loading"]),
    filteredItems() {
      const term = this.searchTerm.toLowerCase();
      if (!term) {
        return this.items;
      }

      return this.items.filter((item) =>
        String(item.lure || "").toLowerCase().includes(term),
      );
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
        return "Biztosan törölni szeretnéd ezt a csalit?";
      }

      const lure = this.items.find((item) => item.id === this.pendingDeleteId);
      if (!lure) {
        return `Biztosan törölni szeretnéd a csalit (ID: ${this.pendingDeleteId})?`;
      }

      return `Biztosan törölni szeretnéd a csalit (${lure.lure})?`;
    },
  },
  methods: {
    ...mapActions(useLureStore, {
      createLure: "create",
      deleteLure: "delete",
      getAll: "getAll",
      updateLure: "update",
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
      this.newLure = { lure: "" };
      this.createError = "";
      this.$nextTick(() => {
        this.$refs.createLureModal?.show();
      });
    },
    async saveCreatedLure(done) {
      const lure = String(this.newLure.lure || "").trim();
      if (!lure) {
        this.createError = "A csali neve kötelező.";
        done(false);
        return;
      }

      this.createError = "";

      try {
        await this.createLure({ lure });
        done(true);
      } catch (error) {
        this.createError = this.getErrorMessage(error, "A csali létrehozása nem sikerült.");
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
        await this.deleteLure(id);
      } finally {
        this.cancelDelete();
        this.ensureValidPage();
      }
    },
    openEditModal(id) {
      const lure = this.items.find((item) => item.id === id);
      if (!lure) {
        return;
      }

      this.editLure = {
        id: lure.id,
        lure: String(lure.lure || ""),
      };
      this.editLureOriginal = {
        lure: String(lure.lure || ""),
      };
      this.editError = "";

      this.$nextTick(() => {
        this.$refs.editLureModal?.show();
      });
    },
    async saveEditedLure(done) {
      if (!this.editLure.id) {
        this.editError = "Hiányzó csali azonosító.";
        done(false);
        return;
      }

      const nextLure = String(this.editLure.lure || "").trim();
      const prevLure = String(this.editLureOriginal.lure || "").trim();

      if (!nextLure) {
        this.editError = "A csali neve kötelező.";
        done(false);
        return;
      }

      if (nextLure === prevLure) {
        done(true);
        return;
      }

      this.editError = "";

      try {
        await this.updateLure(this.editLure.id, { lure: nextLure });
        done(true);
      } catch (error) {
        this.editError = this.getErrorMessage(error, "A csali módosítása nem sikerült.");
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

.create-lure-btn {
  color: #ffffff;
  font-weight: 600;
}

.create-lure-btn:hover,
.create-lure-btn:focus {
  color: #ffffff;
}
</style>
