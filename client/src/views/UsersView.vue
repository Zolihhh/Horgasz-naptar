<template>
  <div>
    <div class="d-flex align-items-center m-0 mb-2">
      <h1>{{ pageTitle }}</h1>
      <div class="d-flex align-items-center m-0 ms-2">
        <i v-if="loading" class="bi bi-hourglass-split fs-3 col-auto p-0 pe-1"></i>
        <p class="m-0 ms-2">({{ filteredItems.length }})</p>
      </div>
    </div>

    <div class="mb-3">
      <input
        v-model.trim="searchTerm"
        type="text"
        class="form-control"
        placeholder="Kereses nev, email vagy szerepkor szerint..."
      />
    </div>

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
    <div v-else style="width: 100px" class="m-auto">Nincs talalat</div>
  </div>
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
      pageTitle: "Userek",
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
