import { defineStore } from "pinia";
import service from "@/api/lureService";

export const useLureStore = defineStore("lure", {
  state: () => ({
    items: [],
    loading: false,
    error: null,
  }),
  getters: {
    getItemsLength(state) {
      return state.items.length;
    },
  },
  actions: {
    async getAll() {
      this.loading = true;
      this.error = null;
      try {
        const response = await service.getAll();
        this.items = Array.isArray(response?.data) ? response.data : [];
      } catch (err) {
        this.error = err;
        throw err;
      } finally {
        this.loading = false;
      }
    },
  },
});
