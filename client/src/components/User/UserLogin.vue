<template>
  <div class="d-flex justify-content-center my-4">
    <div class="card" style="width: 26rem">
      <div class="card-header text-bg-primary">Bejelentkezes</div>
      <div class="card-body">
        <form class="login-form" @submit.prevent="handleSubmit" novalidate>
          <div v-if="registerMode" class="field-block">
            <label for="name" class="form-label">Nev:</label>
            <input
              id="name"
              v-model="user.name"
              type="text"
              class="form-control"
              :class="{ 'is-invalid': showNameError }"
              required
            />
            <div v-if="showNameError" class="invalid-feedback d-block">
              A nev kotelezo
            </div>
          </div>

          <div class="field-block">
            <label for="email" class="form-label">Email cimed:</label>
            <input
              id="email"
              v-model="user.email"
              type="email"
              class="form-control"
              :class="{ 'is-invalid': showEmailError }"
              required
            />
            <div v-if="showEmailError" class="invalid-feedback d-block">
              Az email ures, vagy helytelen
            </div>
          </div>

          <PasswordField
            :modelValue="user.password"
            @update:modelValue="onPasswordChange"
            :label="'Jelszavad'"
            :showRequiredError="showPasswordError"
            :passwordErrorMessage="'A jelszo kotelezo'"
          />

          <div class="action-row">
            <button type="submit" class="btn login-pill-btn">Login</button>
            <button type="button" class="btn login-pill-btn" @click="handleRegisterClick">
              {{ registerMode ? "Regisztracio kuldese" : "Regisztracio" }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import PasswordField from "./PasswordField.vue";

export default {
  name: "UserLogin",
  components: {
    PasswordField,
  },
  data() {
    return {
      user: {
        name: "",
        email: "",
        password: "",
      },
      submitted: false,
      registerMode: false,
    };
  },
  computed: {
    showNameError() {
      return this.registerMode && this.submitted && !this.user.name.trim();
    },
    isEmailValid() {
      return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.user.email);
    },
    showEmailError() {
      return this.submitted && !this.isEmailValid;
    },
    showPasswordError() {
      return this.submitted && (!this.user.password || this.user.password.trim() === "");
    },
  },
  methods: {
    onPasswordChange(value) {
      this.user.password = value;
    },
    handleSubmit() {
      this.submitted = true;
      if (this.showEmailError || this.showPasswordError) {
        return;
      }
      this.$emit("logIn", this.user);
    },
    handleRegisterClick() {
      if (!this.registerMode) {
        this.registerMode = true;
        this.submitted = false;
        return;
      }

      this.submitted = true;
      if (this.showNameError || this.showEmailError || this.showPasswordError) {
        return;
      }

      this.$emit("register", {
        user: {
          name: this.user.name.trim(),
          email: this.user.email.trim(),
          password: this.user.password,
        },
        done: (success) => {
          if (!success) {
            return;
          }
          this.registerMode = false;
          this.submitted = false;
          this.user.name = "";
        },
      });
    },
  },
};
</script>

<style scoped>
.login-form {
  display: flex;
  flex-direction: column;
  gap: 0.9rem;
}

.field-block {
  margin: 0;
}

.action-row {
  display: flex;
  gap: 0.7rem;
  margin-top: 0.2rem;
}

.action-row .btn {
  flex: 1;
}

.login-pill-btn {
  border: 1px solid rgba(101, 186, 229, 0.75);
  background: rgba(7, 12, 18, 0.82);
  color: #eef7ff;
  border-radius: 999px;
  font-weight: 700;
  letter-spacing: 0.01em;
  box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.06), 0 0 14px rgba(76, 170, 220, 0.18);
}

.login-pill-btn:hover,
.login-pill-btn:focus {
  background: rgba(11, 18, 27, 0.95);
  color: #ffffff;
  border-color: rgba(123, 206, 246, 0.9);
  box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.09), 0 0 18px rgba(89, 189, 238, 0.3);
}
</style>
