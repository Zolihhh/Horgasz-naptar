<template>
  <div class="home-wrap">
    <div class="hero">
      <h1>Modern Horgasz Naplo</h1>
      <p>Fogasok es idojaras egy helyen</p>
    </div>

    <div v-if="!isLoggedIn" class="login-theme-wrap">
      <UserLogin @logIn="loginHandler" @register="registerHandler" />
    </div>
  </div>
</template>

<script>
import { mapActions, mapState } from "pinia";
import { useUserLoginLogoutStore } from "@/stores/userLoginLogoutStore";
import UserLogin from "@/components/User/UserLogin.vue";

export default {
  name: "HomeView",
  components: {
    UserLogin,
  },
  computed: {
    ...mapState(useUserLoginLogoutStore, ["isLoggedIn"]),
  },
  methods: {
    ...mapActions(useUserLoginLogoutStore, ["login", "register"]),
    async loginHandler(user) {
      try {
        await this.login(user);
      } catch (error) {
        console.log("Bejelentkezesi hiba");
      }
    },
    async registerHandler({ user, done }) {
      try {
        await this.register(user);
        done(true);
      } catch (error) {
        done(false);
        console.log("Regisztracios hiba");
      }
    },
  },
};
</script>

<style scoped>
.home-wrap {
  min-height: calc(100vh - 90px);
  display: flex;
  flex-direction: column;
  align-items: center;
}

.hero {
  height: 42vh;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  backdrop-filter: blur(5px);
  text-align: center;
}

.hero h1 {
  margin: 0;
  font-size: clamp(2rem, 4vw, 3.1rem);
  font-weight: 700;
  letter-spacing: 0.03em;
}

.hero p {
  margin-top: 0.75rem;
  color: #d8e6ee;
}

.login-theme-wrap {
  width: 100%;
  display: flex;
  justify-content: center;
  padding: 0 1rem 2rem;
}

.login-theme-wrap :deep(.card) {
  width: min(92vw, 640px) !important;
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 22px;
  overflow: hidden;
  background: rgba(8, 15, 22, 0.62);
  backdrop-filter: blur(10px);
  box-shadow: 0 20px 45px rgba(0, 0, 0, 0.38);
}

.login-theme-wrap :deep(.card-header) {
  padding: 1.5rem 1.5rem 0.5rem;
  background: transparent !important;
  color: #ffffff !important;
  border-bottom: 0;
  font-weight: 700;
  font-size: 2rem;
  letter-spacing: 0.01em;
}

.login-theme-wrap :deep(.card-body) {
  padding: 0.25rem 1.5rem 1.5rem;
  background: transparent;
  color: #eef7fb;
}

.login-theme-wrap :deep(.form-label) {
  color: #f3f9fc;
  font-size: 1.15rem;
}

.login-theme-wrap :deep(.form-control) {
  border-color: rgba(255, 255, 255, 0.2);
  background: rgba(8, 15, 22, 0.52);
  color: #eef7fb;
}

.login-theme-wrap :deep(.form-control:focus) {
  border-color: rgba(255, 255, 255, 0.42);
  box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.12);
}

.login-theme-wrap :deep(.btn-success) {
  background-color: rgba(255, 255, 255, 0.12);
  border-color: rgba(255, 255, 255, 0.38);
  color: #ffffff;
}

.login-theme-wrap :deep(.btn-success:hover) {
  background-color: rgba(255, 255, 255, 0.2);
  border-color: rgba(255, 255, 255, 0.52);
  color: #ffffff;
}

.login-theme-wrap :deep(.btn-primary) {
  background-color: rgba(255, 255, 255, 0.06);
  border-color: rgba(255, 255, 255, 0.32);
  color: #ffffff;
}

.login-theme-wrap :deep(.btn-primary:hover) {
  background-color: rgba(255, 255, 255, 0.14);
  border-color: rgba(255, 255, 255, 0.46);
  color: #ffffff;
}

.login-theme-wrap :deep(.invalid-feedback) {
  color: #ffd3d3;
}
</style>
