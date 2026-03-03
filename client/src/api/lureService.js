import apiClient from "./axiosClient";

const route = "/lures";

export default {
  async getAll() {
    return await apiClient.get(route);
  },
};
