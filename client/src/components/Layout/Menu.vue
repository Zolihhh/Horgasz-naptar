<template>
  <nav class="menu d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between">
    <div class="menu-links d-flex flex-nowrap align-items-center gap-2 gap-md-4">
      <RouterLink to="/">Főoldal</RouterLink>
      <RouterLink to="/weather">Időjárás</RouterLink>
      <RouterLink to="/catches">Fogások</RouterLink>
    </div>

    <div class="menu-auth d-flex align-items-center gap-2 position-relative w-auto align-self-end align-self-md-auto justify-content-md-end">
      <div v-if="isLoggedIn" ref="userMenu" class="user-menu">
        <button
          type="button"
          class="menu-user menu-user-btn"
          :aria-expanded="isUserMenuOpen ? 'true' : 'false'"
          @click="toggleUserMenu"
        >
          {{ userName }}
        </button>

        <div v-if="isUserMenuOpen" class="menu-dropdown">
          <RouterLink
            v-if="isAdmin"
            to="/users"
            class="menu-dropdown-link"
            @click="closeUserMenu"
          >
            Összes felhasználó
          </RouterLink>

          <RouterLink
            to="/profile"
            class="menu-dropdown-link"
            @click="closeUserMenu"
          >
            Saját adatok
          </RouterLink>

          <button
            type="button"
            class="menu-dropdown-link menu-dropdown-btn"
            @click="logoutHandler"
          >
            Kijelentkezés
          </button>
        </div>
      </div>

      <RouterLink v-else to="/login" class="menu-logout">Bejelentkezés</RouterLink>
    </div>
  </nav>
</template>

<script>
import { mapActions, mapState } from "pinia";
import { useUserLoginLogoutStore } from "@/stores/userLoginLogoutStore";

export default {
  name: "MenuBar",
  data() {
    return {
      isUserMenuOpen: false,
    };
  },
  computed: {
    ...mapState(useUserLoginLogoutStore, ["isLoggedIn", "userName", "role"]),
    isAdmin() {
      return this.role === 1;
    },
  },
  methods: {
    ...mapActions(useUserLoginLogoutStore, ["logout"]),
    toggleUserMenu() {
      this.isUserMenuOpen = !this.isUserMenuOpen;
    },
    closeUserMenu() {
      this.isUserMenuOpen = false;
    },
    onDocumentClick(event) {
      if (!this.isUserMenuOpen) return;
      if (this.$refs.userMenu && !this.$refs.userMenu.contains(event.target)) {
        this.closeUserMenu();
      }
    },
    logoutHandler() {
      this.closeUserMenu();
      this.logout().catch(() => {
        console.log("Kijelentkezési hiba");
      });
      this.$router.replace("/login");
    },
  },
  mounted() {
    document.addEventListener("click", this.onDocumentClick);
  },
  beforeUnmount() {
    document.removeEventListener("click", this.onDocumentClick);
  },
};
</script>

<style scoped>
.menu {
  position: relative;
  z-index: 1000;
  padding: 14px 20px;
  background: rgba(0, 0, 0, 0.58);
  backdrop-filter: blur(6px);
  border-bottom: 1px solid rgba(180, 210, 224, 0.22);
  color: #ffffff;
}

.menu-links {
  min-width: 0;
}

.menu-auth {
  min-width: 0;
}

.user-menu {
  position: relative;
}

.menu-user-btn {
  cursor: pointer;
}

.menu-dropdown {
  position: absolute;
  top: calc(100% + 8px);
  right: 0;
  min-width: 170px;
  border: 1px solid rgba(180, 210, 224, 0.28);
  border-radius: 10px;
  background: rgba(8, 23, 32, 0.92);
  backdrop-filter: blur(6px);
  padding: 6px;
  z-index: 1100;
}

.menu-dropdown-link {
  display: block;
  width: 100%;
  text-align: left;
  padding: 8px 10px;
  border-radius: 7px;
  color: #eaf6fb;
  text-decoration: none;
  background: transparent;
}

.menu-dropdown-link:hover {
  background: rgba(216, 238, 248, 0.12);
}

.menu-dropdown-btn {
  border: 0;
  background: transparent;
  cursor: pointer;
}

.menu-user {
  padding: 0.3rem 0.65rem;
  border-radius: 999px;
  background: rgba(8, 28, 40, 0.72);
  border: 1px solid rgba(155, 190, 205, 0.4);
  color: #eef7fb;
  font-weight: 600;
  font-size: 0.9rem;
}

.menu-logout {
  border: 1px solid rgba(220, 235, 243, 0.8);
  background: transparent;
  color: #eef7fb;
  border-radius: 8px;
  padding: 0.25rem 0.6rem;
  cursor: pointer;
  text-decoration: none;
}

.menu-logout:hover {
  background: rgba(238, 247, 251, 0.12);
}

.menu a {
  color: #fff;
  text-decoration: none;
}

.menu a.router-link-active {
  color: #d9eef9;
  text-shadow: 0 0 12px rgba(180, 226, 245, 0.3);
}
</style>
