<template>
  <div>
    <nav class="menu">
      <div class="menu-links">
        <RouterLink to="/">Fooldal</RouterLink>
        <RouterLink to="/weather">Idojaras</RouterLink>
        <RouterLink to="/catches">Fogasok</RouterLink>
      </div>

      <div class="menu-auth">
        <span class="menu-user">
          {{ isLoggedIn ? userName : 'Nincs bejelentkezve' }}
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

    <RouterView />
  </div>
</template>

<script>
import { mapActions, mapState } from "pinia";
import { useUserLoginLogoutStore } from "@/stores/userLoginLogoutStore";

export default {
  name: "App",
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
<<<<<<< HEAD
body {
  margin: 0;
  font-family: Arial;
  background: url("@/assets/1.jpg") center/cover no-repeat fixed;
  color: white;
=======
body{
margin:0;
font-family:Arial;
background: url('@/assets/2.jpg') center/cover no-repeat fixed;
color:white;
>>>>>>> e5d8f817265460f8c5eb5effce783bfe253611e1
}

.menu {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  padding: 14px 20px;
  background: rgba(0, 0, 0, 0.6);
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

a {
  color: white;
  text-decoration: none;
}
</style>
