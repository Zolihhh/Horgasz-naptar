<template>
  <section class="profile-wrap">
    <header class="profile-head">
      <h1>Sajat adatok</h1>
      <p>A bejelentkezett felhasznalo adatainak modositasa.</p>
    </header>

    <p v-if="!isLoggedIn" class="status-text">A profil modositashoz jelentkezz be.</p>

    <template v-else>
      <form class="profile-card" @submit.prevent="saveProfile">
        <h2>Alap adatok</h2>
        <label>
          Nev
          <input v-model.trim="form.name" type="text" required />
        </label>

        <label>
          Email
          <input v-model.trim="form.email" type="email" required />
        </label>

        <button type="submit" class="primary-btn" :disabled="saving">
          {{ saving ? "Mentes..." : "Adatok mentese" }}
        </button>
      </form>

      <form class="profile-card password-card" @submit.prevent="savePassword">
        <h2>Jelszo modositas</h2>
        <label>
          Jelenlegi jelszo
          <input v-model="passwordForm.oldpassword" type="password" required />
        </label>

        <label>
          Uj jelszo
          <input v-model="passwordForm.newpassword" type="password" required />
        </label>

        <label>
          Uj jelszo megerositese
          <input v-model="passwordForm.newpassword_confirmation" type="password" required />
        </label>

        <button type="submit" class="primary-btn" :disabled="savingPassword">
          {{ savingPassword ? "Mentes..." : "Jelszo mentese" }}
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
        console.log("Profil mentesi hiba");
      } finally {
        this.saving = false;
      }
    },
    async savePassword() {
      this.savingPassword = true;
      try {
        await this.updatePassword(this.passwordForm);
        this.passwordForm.oldpassword = "";
        this.passwordForm.newpassword = "";
        this.passwordForm.newpassword_confirmation = "";
      } catch (error) {
        console.log("Jelszo mentesi hiba");
      } finally {
        this.savingPassword = false;
      }
    },
  },
};
</script>

<style scoped>
.profile-wrap {
  max-width: 760px;
  margin: 0 auto;
  padding: 28px 20px 40px;
}

.profile-head {
  margin-bottom: 16px;
  padding: 16px 18px;
  border-radius: 16px;
  border: 1px solid rgba(210, 232, 241, 0.24);
  background: linear-gradient(135deg, rgba(6, 18, 26, 0.78), rgba(16, 45, 61, 0.55));
  backdrop-filter: blur(8px);
}

.profile-head h1 {
  margin: 0;
}

.profile-head p {
  margin: 6px 0 0;
  color: #d6e6ee;
}

.profile-card {
  display: grid;
  gap: 12px;
  padding: 16px;
  border-radius: 16px;
  border: 1px solid rgba(210, 232, 241, 0.22);
  background: linear-gradient(145deg, rgba(7, 16, 24, 0.74), rgba(8, 29, 41, 0.58));
  backdrop-filter: blur(8px);
}

.password-card {
  margin-top: 14px;
}

.profile-card h2 {
  margin: 0;
  font-size: 1.1rem;
  color: #e6f2f8;
}

.profile-card label {
  display: grid;
  gap: 6px;
}

.profile-card input {
  border: 1px solid rgba(194, 224, 236, 0.35);
  border-radius: 8px;
  padding: 8px 10px;
  background: rgba(5, 14, 20, 0.56);
  color: #fff;
}

.profile-card input:focus {
  outline: none;
  border-color: rgba(214, 238, 248, 0.85);
  box-shadow: 0 0 0 3px rgba(173, 223, 243, 0.16);
}

.primary-btn {
  justify-self: start;
  border: 1px solid rgba(194, 224, 236, 0.62);
  border-radius: 10px;
  background: linear-gradient(145deg, rgba(10, 31, 44, 0.88), rgba(16, 54, 73, 0.78));
  color: #fff;
  font-weight: 600;
  letter-spacing: 0.01em;
  padding: 9px 14px;
  cursor: pointer;
}

.status-text {
  color: #e7f2f8;
}
</style>
