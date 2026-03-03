import apiClient from "./axiosClient";

const route = "/fishcatches";

export default {
  async getMyCatches() {
    return await apiClient.get(route);
  },
  async create(data) {
    return await apiClient.post(route, data);
  },
  async update(id, data) {
    return await apiClient.patch(`${route}/${id}`, data);
  },
};
