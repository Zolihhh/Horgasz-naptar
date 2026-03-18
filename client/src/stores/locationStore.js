import { defineStore } from "pinia";
import service from "@/api/locationService";

class Item {
  constructor(id = 0, waterAreaCode = "", latitude = null, longitude = null, FishingLakeName = "") {
    this.id = id;
    this.waterAreaCode = waterAreaCode;
    this.latitude = latitude;
    this.longitude = longitude;
    this.FishingLakeName = FishingLakeName;
  }
}

export const useLocationStore = defineStore("location", {
  state: () => ({
    item: new Item(),
    items: [],
    loading: false,
    error: null,
    sortColumn: "id",
    sortDirection: "asc",
  }),
  getters: {
    getItemsLength(state) {
      return state.items.length;
    },
  },
  actions: {
    clearItem() {
      this.item = new Item();
    },
    async getAll() {
      this.loading = true;
      this.error = null;
      try {
        const response = await service.getAll();
        this.items = Array.isArray(response?.data) ? response.data : [];
        return this.items;
      } catch (err) {
        this.error = err;
        throw err;
      } finally {
        this.loading = false;
      }
    },
    async getById(id) {
      this.loading = true;
      this.error = null;
      try {
        const response = await service.getById(id);
        this.item = response?.data ?? new Item();
        return this.item;
      } catch (err) {
        this.error = err;
        throw err;
      } finally {
        this.loading = false;
      }
    },
    async create(data) {
      this.loading = true;
      this.error = null;
      try {
        await service.create(data);
        await this.getAll();
        return true;
      } catch (err) {
        this.error = err;
        throw err;
      } finally {
        this.loading = false;
      }
    },
    async update(id, data) {
      this.loading = true;
      this.error = null;
      try {
        await service.update(id, data);
        await this.getAll();
        return true;
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
        await service.delete(id);
        await this.getAll();
        return true;
      } catch (err) {
        this.error = err;
        throw err;
      } finally {
        this.loading = false;
      }
    },
  },
});
