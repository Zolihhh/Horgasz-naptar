import apiClient from "./axiosClient";

const route = "/cities";

export default {
  async getAll() {
    return await apiClient.get(route);
  },
};
