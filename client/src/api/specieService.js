import apiClient from "./axiosClient";

const route = "/species";

export default {
  async getAll() {
    return await apiClient.get(route);
  },
};
