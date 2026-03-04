<template>
  <nav class="menu">
    <div class="menu-links">
      <RouterLink to="/">Fooldal</RouterLink>
      <RouterLink to="/weather">Idojaras</RouterLink>
      <RouterLink to="/catches">Fogasok</RouterLink>
    </div>

    <div class="menu-auth">
      <div v-if="isLoggedIn" class="user-menu">
        <button type="button" class="menu-user menu-user-btn" @click="toggleUserMenu">
          {{ userName }}
        </button>
        <div v-if="userMenuOpen" class="menu-dropdown">
          <RouterLink
            v-if="isAdmin"
            to="/users"
            class="menu-dropdown-link"
            @click="closeUserMenu"
          >
            Osszes felhasznalo
          </RouterLink>
          <RouterLink
            to="/profile"
            class="menu-dropdown-link"
            @click="closeUserMenu"
          >
            Sajat adatok
          </RouterLink>
          <button type="button" class="menu-dropdown-link menu-dropdown-btn" @click="logoutHandler">
            Kijelentkezes
          </button>
        </div>
      </div>
      <RouterLink v-else to="/login" class="menu-logout">Bejelentkezes</RouterLink>
    </div>
  </nav>
</template>

<script>
import { mapActions, mapState } from "pinia";
import { useUserLoginLogoutStore } from "@/stores/userLoginLogoutStore";

export default {
  name: "MenuBar",
  computed: {
    ...mapState(useUserLoginLogoutStore, ["isLoggedIn", "userName", "role"]),
    isAdmin() {
      return this.role === 1;
    },
  },
  data() {
    return {
      userMenuOpen: false,
    };
  },
  watch: {
    isLoggedIn(newValue) {
      if (!newValue) {
        this.userMenuOpen = false;
      }
    },
    $route() {
      this.userMenuOpen = false;
    },
  },
  methods: {
    ...mapActions(useUserLoginLogoutStore, ["logout"]),
    toggleUserMenu() {
      this.userMenuOpen = !this.userMenuOpen;
    },
    closeUserMenu() {
      this.userMenuOpen = false;
    },
    async logoutHandler() {
      try {
        await this.logout();
      } catch (error) {
        console.log("Kijelentkezesi hiba");
      }
    },
  },
};
</script>

<style scoped>
.menu {
  position: relative;
  z-index: 1000;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  padding: 14px 20px;
  background: rgba(0, 0, 0, 0.58);
  backdrop-filter: blur(6px);
  border-bottom: 1px solid rgba(180, 210, 224, 0.22);
  color: #ffffff;
}

.menu-links {
  display: flex;
  align-items: center;
  gap: 20px;
}

.menu-auth {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  position: relative;
}

.user-menu {
  position: relative;
}

.menu-user-btn {
  cursor: pointer;
}

.menu-dropdown {
  position: absolute;
  top: calc(100% + 6px);
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

@media (max-width: 760px) {
  .menu {
    flex-direction: column;
    align-items: flex-start;
  }

  .menu-links {
    flex-wrap: wrap;
  }
}
</style>
