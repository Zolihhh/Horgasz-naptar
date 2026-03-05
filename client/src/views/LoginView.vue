<template>
  <section class="login-page">
    <UserLogin
      ref="userLoginForm"
      @logIn="loginHandler"
      @register="registerHandler"
    />
  </section>
</template>

<script>
import { mapActions } from "pinia";
import { useUserLoginLogoutStore } from "@/stores/userLoginLogoutStore";
import { UserLogin } from "@/components/Forms";

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
        this.$refs.userLoginForm?.clearLoginError();
        this.$router.push("/");
      } catch (error) {
        const message =
          error?.response?.data?.message || "Hibás email vagy jelszó.";
        this.$refs.userLoginForm?.setLoginError(message);
      }
    },
    async registerHandler({ user, done }) {
      try {
        await this.register(user);
        this.$refs.userLoginForm?.clearServerErrors();
        done(true);
      } catch (error) {
        const status = error?.response?.status;
        const data = error?.response?.data || {};

        if (status === 422) {
          this.$refs.userLoginForm?.setServerErrors(
            data.errors || {},
            data.message || "Ellenőrizd a mezőket.",
          );
        } else {
          this.$refs.userLoginForm?.setServerErrors(
            {},
            data.message || "Regisztrációs hiba történt.",
          );
        }

        done(false);
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
