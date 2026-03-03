import apiClient from "./axiosClient";

const route = "/catchlogs";

export default {
  async getAll() {
    return await apiClient.get(route);
  },
};
