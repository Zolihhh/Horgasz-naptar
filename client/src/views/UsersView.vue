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
      <GenericTable
        v-if="filteredItems.length > 0"
        :items="filteredItems"
        :columns="tableColumns"
        :useCollectionStore="useCollectionStore"
        :cButtonVisible="false"
        :uButtonVisible="false"
        :dButtonVisible="false"
        :pButtonVisible="false"
        @sort="sortHandler"
      />

      <p v-else class="empty-state">Nincs találat</p>
    </div>
  </section>
</template>

<script>
import { mapActions, mapState } from "pinia";
import { useUserStore } from "@/stores/userStore";
import { useSearchStore } from "@/stores/searchStore";
import GenericTable from "@/components/Table/GenericTable.vue";

export default {
  name: "UsersView",
  components: {
    GenericTable,
  },
  watch: {
    searchWord() {
      this.getAllSortSearch(this.sortColumn, this.sortDirection);
    },
  },
  data() {
    return {
      pageTitle: "Összes felhasználó",
      searchTerm: "",
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
    ...mapState(useUserStore, ["items", "loading", "getItemsLength", "sortColumn", "sortDirection"]),
    ...mapState(useSearchStore, ["searchWord"]),
    filteredItems() {
      const term = this.searchTerm.toLowerCase();
      if (!term) return this.items;

      return this.items.filter((item) => {
        const roleText =
          item.role === 1 ? "admin" : item.role === 2 ? "tanár" : item.role === 3 ? "diák" : "";
        return (
          String(item.name || "").toLowerCase().includes(term) ||
          String(item.email || "").toLowerCase().includes(term) ||
          String(item.role || "").toLowerCase().includes(term) ||
          roleText.includes(term)
        );
      });
    },
  },
  methods: {
    ...mapActions(useUserStore, ["getAll", "getAllSortSearch"]),
    ...mapActions(useSearchStore, ["resetSearchWord"]),
    sortHandler(column) {
      this.getAllSortSearch(column);
    },
  },
  async mounted() {
    this.resetSearchWord();
    await this.getAll();
  },
};
</script>
