import { defineStore } from "pinia";
import service from "@/api/fishCatchService";

export const useFishCatchStore = defineStore("fishCatch", {
  state: () => ({
    items: [],
    item: null,
    loading: false,
    error: null,
  }),
  getters: {
    getItemsLength(state) {
      return state.items.length;
    },
  },
  actions: {
    async getMyCatches() {
      this.loading = true;
      this.error = null;
      try {
        const response = await service.getMyCatches();
        this.items = Array.isArray(response?.data) ? response.data : [];
      } catch (err) {
        this.error = err;
        throw err;
      } finally {
        this.loading = false;
      }
    },
    async create(payload) {
      this.loading = true;
      this.error = null;
      try {
        const response = await service.create(payload);
        this.item = response?.data ?? null;
        await this.getMyCatches();
        return response;
      } catch (err) {
        this.error = err;
        throw err;
      } finally {
        this.loading = false;
      }
    },
    async update(id, payload) {
      this.loading = true;
      this.error = null;
      try {
        const response = await service.update(id, payload);
        this.item = response?.data ?? null;
        await this.getMyCatches();
        return response;
      } catch (err) {
        this.error = err;
        throw err;
      } finally {
        this.loading = false;
      }
    },
  },
});
