<template>
  <section class="users-wrap">
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
          placeholder="Kereses nev, email vagy szerepkor szerint..."
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

      <p v-else class="empty-state">Nincs talalat</p>
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
      pageTitle: "Osszes felhasznalo",
      searchTerm: "",
      tableColumns: [
        { key: "id", label: "ID", debug: import.meta.env.VITE_DEBUG_MODE },
        { key: "name", label: "User nev", debug: 2 },
        { key: "email", label: "Email", debug: 2 },
        { key: "role", label: "Szerepkor", debug: 2 },
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
          item.role === 1 ? "admin" : item.role === 2 ? "tanar" : item.role === 3 ? "diak" : "";
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

<style scoped>
.users-wrap {
  max-width: 1150px;
  margin: 0 auto;
  padding: 28px 20px 40px;
  color: #ffffff;
}

.users-head {
  margin-bottom: 14px;
  padding: 16px 18px;
  border-radius: 16px;
  border: 1px solid rgba(210, 232, 241, 0.24);
  background: linear-gradient(135deg, rgba(6, 18, 26, 0.78), rgba(16, 45, 61, 0.55));
  backdrop-filter: blur(8px);
}

.title-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  flex-wrap: wrap;
}

.title-row h1 {
  margin: 0;
  color: #ffffff;
  font-size: clamp(1.4rem, 3.1vw, 2rem);
}

.count-wrap {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #e6f2f8;
}

.count-wrap p {
  margin: 0;
}

.search-wrap {
  margin-top: 12px;
}

.users-search {
  width: 100%;
  border: 1px solid rgba(194, 224, 236, 0.35);
  border-radius: 10px;
  padding: 10px 12px;
  background: rgba(5, 14, 20, 0.56);
  color: #ffffff;
}

.users-search::placeholder {
  color: #bcd0da;
}

.users-search:focus {
  outline: none;
  border-color: rgba(214, 238, 248, 0.85);
  box-shadow: 0 0 0 3px rgba(173, 223, 243, 0.16);
}

.users-table-card {
  border: 1px solid rgba(202, 227, 238, 0.2);
  border-radius: 16px;
  background: linear-gradient(145deg, rgba(8, 17, 25, 0.78), rgba(11, 34, 47, 0.62));
  backdrop-filter: blur(7px);
  box-shadow: 0 14px 32px rgba(0, 0, 0, 0.22);
  padding: 14px;
}

.empty-state {
  margin: 8px 0;
  color: #e7f2f8;
  text-align: center;
}

:deep(.my-table-container) {
  border: 1px solid rgba(202, 227, 238, 0.2);
  border-radius: 12px;
  background: rgba(4, 13, 19, 0.45);
}

:deep(.table) {
  --bs-table-color: #eaf5fb;
  --bs-table-bg: transparent;
  --bs-table-border-color: rgba(202, 227, 238, 0.14);
  color: #eaf5fb;
  margin-bottom: 0;
}

:deep(.table > :not(caption) > * > *) {
  background: transparent;
  border-color: rgba(202, 227, 238, 0.14);
}

:deep(.table td) {
  color: #eaf5fb !important;
}

:deep(.table-dark.sticky-top) {
  background: rgba(8, 23, 32, 0.92) !important;
}

:deep(.table-dark th) {
  color: #ffffff !important;
  border-bottom: 1px solid rgba(202, 227, 238, 0.14);
}

:deep(tbody tr:hover) {
  background: rgba(216, 238, 248, 0.06) !important;
}

:deep(.table-primary),
:deep(.table-primary > *) {
  background: rgba(53, 122, 158, 0.35) !important;
  color: #ffffff !important;
}

:deep(.my-pointer) {
  cursor: pointer;
}
</style>
