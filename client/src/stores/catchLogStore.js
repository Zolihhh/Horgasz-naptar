import { defineStore } from "pinia";
import service from "@/api/catchLogService";

export const useCatchLogStore = defineStore("catchLog", {
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
    async create(payload) {
      this.loading = true;
      this.error = null;
      try {
        const response = await service.create(payload);
        this.item = response?.data ?? null;
        return response;
      } catch (err) {
        this.error = err;
        throw err;
      } finally {
        this.loading = false;
      }
    },
    async delete(id) {
      this.loading = true;
      this.error = null;
      try {
        const response = await service.delete(id);
        this.item = response?.data ?? null;
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
