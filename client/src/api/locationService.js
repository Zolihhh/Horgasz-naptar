import apiClient from "./axiosClient";

const route = "/locations";

export default {
  async getAll() {
    return await apiClient.get(route);
  },
};
