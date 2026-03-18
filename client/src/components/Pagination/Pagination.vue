<template>
  <nav v-if="pagination.last_page > 1" class="pagination-nav ms-2">
    <ul class="pagination m-0">
      <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
        <button type="button" class="page-link" @click="goToPage(1)" title="Első oldal">
          &laquo;&laquo;
        </button>
      </li>

      <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
        <button type="button" class="page-link" @click="goToPage(pagination.current_page - 1)">
          &laquo;
        </button>
      </li>

      <li
        v-for="item in pagesToRender"
        :key="item.key"
        class="page-item"
        :class="{
          active: item.type === 'page' && item.value === pagination.current_page,
          disabled: item.type === 'ellipsis',
        }"
      >
        <span v-if="item.type === 'ellipsis'" class="page-link">…</span>
        <button v-else type="button" class="page-link" @click="goToPage(item.value)">
          {{ item.value }}
        </button>
      </li>

      <li
        class="page-item"
        :class="{ disabled: pagination.current_page === pagination.last_page }"
      >
        <button type="button" class="page-link" @click="goToPage(pagination.current_page + 1)">
          &raquo;
        </button>
      </li>

      <li
        class="page-item"
        :class="{ disabled: pagination.current_page === pagination.last_page }"
      >
        <button type="button" class="page-link" @click="goToPage(pagination.last_page)" title="Utolsó oldal">
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
    pagesToRender() {
      const currentPage = this.pagination.current_page;
      const lastPage = this.pagination.last_page;

      if (lastPage <= 7) {
        return Array.from({ length: lastPage }, (_, index) => ({
          type: "page",
          value: index + 1,
          key: `page-${index + 1}`,
        }));
      }

      const visiblePages = new Set([1, lastPage, currentPage, currentPage - 1, currentPage + 1]);

      if (currentPage <= 3) {
        visiblePages.add(2);
        visiblePages.add(3);
        visiblePages.add(4);
      }

      if (currentPage >= lastPage - 2) {
        visiblePages.add(lastPage - 1);
        visiblePages.add(lastPage - 2);
        visiblePages.add(lastPage - 3);
      }

      const sortedPages = [...visiblePages]
        .filter((page) => page >= 1 && page <= lastPage)
        .sort((a, b) => a - b);

      const items = [];

      sortedPages.forEach((page, index) => {
        items.push({
          type: "page",
          value: page,
          key: `page-${page}`,
        });

        const nextPage = sortedPages[index + 1];
        if (nextPage && nextPage - page > 1) {
          items.push({
            type: "ellipsis",
            key: `ellipsis-${page}-${nextPage}`,
          });
        }
      });

      return items;
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

<style scoped>
.pagination-nav .pagination {
  flex-wrap: wrap;
  gap: 0.25rem;
}
</style>
