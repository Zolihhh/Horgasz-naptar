<template>
  <section class="login-page">
    <UserLogin @logIn="loginHandler" @register="registerHandler" />
  </section>
</template>

<script>
import { mapActions } from "pinia";
import { useUserLoginLogoutStore } from "@/stores/userLoginLogoutStore";
import UserLogin from "@/components/User/UserLogin.vue";

export default {
  name: "LoginView",
  components: {
    UserLogin,
  },
  methods: {
    ...mapActions(useUserLoginLogoutStore, ["login", "register"]),
    async loginHandler(user) {
      try {
        await this.login(user);
        this.$router.push("/");
      } catch (error) {
        console.log("Bejelentkezési hiba");
      }
    },
    async registerHandler({ user, done }) {
      try {
        await this.register(user);
        done(true);
      } catch (error) {
        done(false);
        console.log("Regisztrációs hiba");
      }
    },
  },
};
</script>

<style scoped>
.login-page {
  min-height: calc(100vh - 150px);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px 18px 34px;
}
</style>
