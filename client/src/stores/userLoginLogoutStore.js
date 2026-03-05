import { defineStore } from "pinia";
// import { useToastStore } from "@/stores/toastStore";
import service from "@/api/userLoginLogoutService";
import { useToastStore } from "./toastStore";

export const useUserLoginLogoutStore = defineStore("userLoginLogout", {
  //Ezek a változók
  state: () => ({
    item: JSON.parse(localStorage.getItem("user_data")) || null,
    loading: false,
    error: null,
    rolNames: ["Admin", "Tanár", "Diák"],
  }),
  //valamilyen formában visszaadja
  getters: {
    token() {
      if (!this.item) {
        return null;
      }
      return this.item.token;
    },
    role() {
      if (!this.item) {
        return null;
      }
      return this.item.role;
    },
    userName() {
      if (!this.item) {
        return null;
      }
      return this.item.name;
    },
    userNameWithRole() {
      if (!this.item) {
        return null;
      }
      const userInfo = `${this.item.name}: ${this.rolNames[this.item.role - 1]}`;
      return userInfo;
    },
    isLoggedIn() {
      return this.item != null ? true : false;
    },
  },
  //csinál vele valamit
  actions: {
    canAccess(requiredRoles) {
      // Itt a 'this' kulcsszóval éred el a state-et
      if (!requiredRoles || requiredRoles.length === 0) return true;
      if (!this.isLoggedIn) return false;
      return requiredRoles.includes(this.role);
    },
    async login(data) {
      try {
        this.loading = true;
        this.error = null;
        const response = await service.login(data);
        this.item = response.data;
        localStorage.setItem("user_data", JSON.stringify(response.data));
        // const toastStore = useToastStore();
        // toastStore.messages.push("Sikeres bejelentkezés");
        // toastStore.show("Success");
        return true;
      } catch (err) {
        this.error = err;
        throw err;
        return false;
      } finally {
        this.loading = false;
      }
    },
    async register(data) {
      try {
        this.loading = true;
        this.error = null;
        await service.register(data);
        const toastStore = useToastStore();
        toastStore.messages.push("Sikeres regisztráció");
        toastStore.show("Success");
        return true;
      } catch (err) {
        this.error = err;
        throw err;
      } finally {
        this.loading = false;
      }
    },
    async logout() {
      this.error = null;
      this.loading = true;
      // Optimista kijelentkeztetés: az UI azonnal lépjen ki.
      this.item = null;
      localStorage.removeItem("user_data");

      try {
        await service.logout();
      } catch (err) {
        // Ha a backend logout hibázik, a lokális session már törölve van.
        this.error = err;
      } finally {
        const toastStore = useToastStore();
        toastStore.messages.push("Sikeres kijelentkezés");
        toastStore.show("Success");
        this.loading = false;
      }

      return true;
    },
    async getMeRefresh() {
      try {
        this.error = null;
        this.loading = true;
        const response = await service.getMeRefresh();
        const me = response?.data?.data || response?.data || {};
        if (this.item) {
          this.item.name = me.name ?? this.item.name;
          this.item.email = me.email ?? this.item.email;
          localStorage.setItem("user_data", JSON.stringify(this.item));
        }
        return true;
      } catch (err) {
        this.error = err;
        throw err;
        return false;
      } finally {
        this.loading = false;
      }
    },
    async updateMe(data) {
      try {
        this.error = null;
        this.loading = true;
        const response = await service.updateMe(data);
        const updated = response?.data?.data || response?.data || {};
        if (this.item) {
          this.item.name = updated.name ?? this.item.name;
          this.item.email = updated.email ?? this.item.email;
          localStorage.setItem("user_data", JSON.stringify(this.item));
        }
        return true;
      } catch (err) {
        this.error = err;
        throw err;
        return false;
      } finally {
        this.loading = false;
      }
    },
    async updatePassword(data) {
      try {
        this.error = null;
        this.loading = true;
        await service.updatePassword(data);
        return true;
      } catch (err) {
        this.error = err;
        throw err;
        return false;
      } finally {
        this.loading = false;
      }
    },
  },
});

