<template>
  <div class="app-shell">
    <nav class="menu">
      <div class="menu-links">
        <RouterLink to="/">Fooldal</RouterLink>
        <RouterLink to="/weather">Idojaras</RouterLink>
        <RouterLink to="/catches">Fogasok</RouterLink>
      </div>

      <div class="menu-auth">
        <span class="menu-user">
          {{ isLoggedIn ? userName : "Nincs bejelentkezve" }}
        </span>
        <button
          v-if="isLoggedIn"
          type="button"
          class="menu-logout"
          @click="logoutHandler"
        >
          Kijelentkezes
        </button>
      </div>
    </nav>

    <main class="app-main">
      <RouterView />
    </main>

    <FooterBar />
  </div>
</template>

<script>
import { mapActions, mapState } from "pinia";
import { useUserLoginLogoutStore } from "@/stores/userLoginLogoutStore";
import FooterBar from "@/components/Layout/Footer.vue";

export default {
  name: "App",
  components: {
    FooterBar,
  },
  computed: {
    ...mapState(useUserLoginLogoutStore, ["isLoggedIn", "userName"]),
  },
  methods: {
    ...mapActions(useUserLoginLogoutStore, ["logout"]),
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

<style>
body {
  margin: 0;
  font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
  color: #fff;
}

.app-shell {
  position: relative;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  isolation: isolate;
}

.app-shell::before {
  content: "";
  position: fixed;
  inset: 0;
  background: url("@/assets/2.jpg") center/cover no-repeat fixed;
  filter: blur(6px);
  transform: scale(1.04);
  z-index: -2;
}

.app-shell::after {
  content: "";
  position: fixed;
  inset: 0;
  background: linear-gradient(
    180deg,
    rgba(4, 10, 14, 0.5),
    rgba(3, 8, 12, 0.6)
  );
  z-index: -1;
}

.app-main {
  flex: 1;
}

.menu {
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
