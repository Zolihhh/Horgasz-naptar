<template>
  <section class="users-wrap users-view">
    <header class="users-head">
      <div class="title-row">
        <h1>{{ pageTitle }}</h1>
        <div class="count-wrap">
          <i v-if="loading" class="bi bi-hourglass-split"></i>
          <p>({{ filteredItems.length }})</p>
        </div>
      </div>

      <div class="search-wrap">
        <input
          v-model.trim="searchTerm"
          type="text"
          class="users-search"
          placeholder="Keresés név, email vagy szerepkör szerint..."
        />
      </div>
    </header>

    <div class="users-table-card">
      <div v-if="filteredItems.length > 0" class="users-controls">
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

      <GenericTable
        v-if="paginatedItems.length > 0"
        :items="paginatedItems"
        :columns="tableColumns"
        :useCollectionStore="useCollectionStore"
        :cButtonVisible="false"
        :uButtonVisible="false"
        :dButtonVisible="true"
        :pButtonVisible="false"
        @sort="sortHandler"
        @delete="openDeleteConfirm"
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
  </section>
</template>

<script>
import { mapActions, mapState } from "pinia";
import { useUserStore } from "@/stores/userStore";
import { useSearchStore } from "@/stores/searchStore";
import GenericTable from "@/components/Table/GenericTable.vue";
import Pagination from "@/components/Pagination/Pagination.vue";
import SetSelectedPerPage from "@/components/Pagination/SetSelectedPerPage.vue";
import ConfirmModal from "@/components/Confirm/ConfirmModal.vue";

export default {
  name: "UsersView",
  components: {
    ConfirmModal,
    GenericTable,
    Pagination,
    SetSelectedPerPage,
  },
  watch: {
    searchWord() {
      this.getAllSortSearch(this.sortColumn, this.sortDirection);
      this.currentPage = 1;
    },
    filteredItems() {
      this.ensureValidPage();
    },
    lastPage() {
      this.ensureValidPage();
    },
  },
  data() {
    return {
      pageTitle: "Összes felhasználó",
      searchTerm: "",
      currentPage: 1,
      selectedPerPage: 10,
      isDeleteConfirmOpen: false,
      pendingDeleteId: null,
      tableColumns: [
        { key: "id", label: "ID", debug: import.meta.env.VITE_DEBUG_MODE },
        { key: "name", label: "User név", debug: 2 },
        { key: "email", label: "Email", debug: 2 },
        { key: "role", label: "Szerepkör", debug: 2 },
      ],
      useCollectionStore: useUserStore,
    };
  },
  computed: {
    ...mapState(useUserStore, [
      "items",
      "loading",
      "getItemsLength",
      "sortColumn",
      "sortDirection",
    ]),
    ...mapState(useSearchStore, ["searchWord"]),
    filteredItems() {
      const term = this.searchTerm.toLowerCase();
      if (!term) return this.items;

      return this.items.filter((item) => {
        const roleText =
          item.role === 1
            ? "admin"
            : item.role === 2
              ? "tanár"
              : item.role === 3
                ? "diák"
                : "";

        return (
          String(item.name || "").toLowerCase().includes(term) ||
          String(item.email || "").toLowerCase().includes(term) ||
          String(item.role || "").toLowerCase().includes(term) ||
          roleText.includes(term)
        );
      });
    },
    effectivePerPage() {
      if (this.selectedPerPage >= 1000000) {
        return Math.max(1, this.filteredItems.length);
      }
      return Math.max(1, Number(this.selectedPerPage) || 10);
    },
    lastPage() {
      return Math.max(1, Math.ceil(this.filteredItems.length / this.effectivePerPage));
    },
    paginatedItems() {
      if (!this.filteredItems.length) return [];

      const start = (this.currentPage - 1) * this.effectivePerPage;
      const end = start + this.effectivePerPage;
      return this.filteredItems.slice(start, end);
    },
    deleteMessage() {
      if (!this.pendingDeleteId) {
        return "Biztosan törölni szeretnéd ezt a felhasználót?";
      }

      const user = this.items.find((item) => item.id === this.pendingDeleteId);
      if (!user) {
        return `Biztosan törölni szeretnéd a felhasználót (ID: ${this.pendingDeleteId})?`;
      }

      return `Biztosan törölni szeretnéd a felhasználót (${user.name})?`;
    },
  },
  methods: {
    ...mapActions(useUserStore, {
      deleteUser: "delete",
      getAll: "getAll",
      getAllSortSearch: "getAllSortSearch",
    }),
    ...mapActions(useSearchStore, ["resetSearchWord"]),
    sortHandler(column) {
      this.getAllSortSearch(column);
      this.currentPage = 1;
    },
    changePage(page) {
      this.currentPage = page;
      this.ensureValidPage();
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
        await this.deleteUser(id);
      } finally {
        this.cancelDelete();
        this.ensureValidPage();
      }
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
    this.resetSearchWord();
    await this.getAll();
    this.ensureValidPage();
  },
};
</script>
