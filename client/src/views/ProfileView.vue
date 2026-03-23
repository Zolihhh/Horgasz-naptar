<template>
  <section class="profile-wrap profile-view">
    <header class="profile-head">
      <h1>Saját adatok</h1>
      <p>A bejelentkezett felhasználó adatainak módosítása.</p>
    </header>

    <p v-if="!isLoggedIn" class="status-text">A profil módosításhoz jelentkezz be.</p>

    <template v-else>
      <form class="profile-card" @submit.prevent="saveProfile">
        <h2>Alap adatok</h2>
        <label>
          Név
          <input v-model.trim="form.name" type="text" required />
        </label>

        <label>
          Email
          <input v-model.trim="form.email" type="email" required />
        </label>

        <button type="submit" class="primary-btn" :disabled="saving">
          {{ saving ? "Mentés..." : "Adatok mentése" }}
        </button>
      </form>

      <form class="profile-card password-card" @submit.prevent="savePassword">
        <h2>Jelszó módosítás</h2>
        <p
          v-if="passwordMessage.text"
          class="form-message"
          :class="passwordMessage.type === 'success' ? 'form-message-success' : 'form-message-error'"
        >
          {{ passwordMessage.text }}
        </p>

        <label>
          Jelenlegi jelszó
          <input v-model="passwordForm.oldpassword" type="password" required />
        </label>

        <label>
          Új jelszó
          <input v-model="passwordForm.newpassword" type="password" required />
        </label>

        <label>
          Új jelszó megerősítése
          <input v-model="passwordForm.newpassword_confirmation" type="password" required />
        </label>

        <button type="submit" class="primary-btn" :disabled="savingPassword">
          {{ savingPassword ? "Mentés..." : "Jelszó mentése" }}
        </button>
      </form>
    </template>
  </section>
</template>

<script>
import { mapActions, mapState } from "pinia";
import { useUserLoginLogoutStore } from "@/stores/userLoginLogoutStore";

export default {
  name: "ProfileView",
  data() {
    return {
      saving: false,
      savingPassword: false,
      form: {
        name: "",
        email: "",
      },
      passwordForm: {
        oldpassword: "",
        newpassword: "",
        newpassword_confirmation: "",
      },
      passwordMessage: {
        text: "",
        type: "success",
      },
    };
  },
  computed: {
    ...mapState(useUserLoginLogoutStore, ["isLoggedIn", "item"]),
  },
  watch: {
    item: {
      handler() {
        this.fillFromStore();
      },
      deep: true,
    },
  },
  mounted() {
    this.fillFromStore();
  },
  methods: {
    ...mapActions(useUserLoginLogoutStore, ["updateMe", "updatePassword"]),
    fillFromStore() {
      this.form.name = this.item?.name || "";
      this.form.email = this.item?.email || "";
    },
    async saveProfile() {
      this.saving = true;
      try {
        await this.updateMe({
          name: this.form.name,
          email: this.form.email,
        });
      } catch (error) {
        console.log("Profil mentési hiba");
      } finally {
        this.saving = false;
      }
    },
    async savePassword() {
      this.savingPassword = true;
      this.passwordMessage.text = "";
      try {
        await this.updatePassword(this.passwordForm);
        this.passwordForm.oldpassword = "";
        this.passwordForm.newpassword = "";
        this.passwordForm.newpassword_confirmation = "";
        this.passwordMessage = {
          text: "A jelszó módosítása sikeres volt.",
          type: "success",
        };
      } catch (error) {
        this.passwordMessage = {
          text: this.getPasswordErrorMessage(error),
          type: "error",
        };
      } finally {
        this.savingPassword = false;
      }
    },
    getPasswordErrorMessage(error) {
      const responseData = error?.response?.data;
      const fieldErrors = responseData?.errors;

      if (fieldErrors?.oldpassword?.length) {
        return fieldErrors.oldpassword[0];
      }

      if (fieldErrors?.newpassword?.length) {
        return fieldErrors.newpassword[0];
      }

      return responseData?.message || "A jelszó módosítása nem sikerült.";
    },
  },
};
</script>
