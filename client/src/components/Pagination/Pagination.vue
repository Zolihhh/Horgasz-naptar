<template>
  <nav v-if="pagination.last_page > 1" class="ms-2">
    <ul class="pagination m-0">
      <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
        <button class="page-link" @click="goToPage(1)" title="Első oldal">
          &laquo;&laquo;
        </button>
      </li>

      <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
        <button class="page-link" @click="goToPage(pagination.current_page - 1)">
          &laquo;
        </button>
      </li>

      <li
        v-for="p in pagination.last_page"
        :key="p"
        class="page-item"
        :class="{ active: p === pagination.current_page }"
      >
        <button class="page-link" @click="goToPage(p)">
          {{ p }}
        </button>
      </li>

      <li
        class="page-item"
        :class="{ disabled: pagination.current_page === pagination.last_page }"
      >
        <button class="page-link" @click="goToPage(pagination.current_page + 1)">
          &raquo;
        </button>
      </li>

      <li
        class="page-item"
        :class="{ disabled: pagination.current_page === pagination.last_page }"
      >
        <button class="page-link" @click="goToPage(pagination.last_page)" title="Utolsó oldal">
          &raquo;&raquo;
        </button>
      </li>
    </ul>
  </nav>
</template>

<script>
export default {
  name: "PaginationBar",
  props: {
    useCollectionStore: { type: Function, default: null },
    currentPage: { type: Number, default: 1 },
    lastPage: { type: Number, default: 1 },
  },
  data() {
    return {
      store: null,
    };
  },
  created() {
    if (this.useCollectionStore) {
      this.store = this.useCollectionStore();
    }
  },
  computed: {
    pagination() {
      if (this.store?.pagination) {
        return this.store.pagination;
      }

      return {
        current_page: Math.max(1, Number(this.currentPage) || 1),
        last_page: Math.max(1, Number(this.lastPage) || 1),
      };
    },
  },
  methods: {
    async goToPage(page) {
      const targetPage = Math.min(
        Math.max(1, Number(page) || 1),
        this.pagination.last_page,
      );

      if (this.store?.getPaging) {
        await this.store.getPaging(targetPage);
        return;
      }

      this.$emit("update:currentPage", targetPage);
      this.$emit("change", targetPage);
    },
  },
};
</script>
