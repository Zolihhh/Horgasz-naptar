<template>
  <div class="login-shell">
    <form class="login-panel" @submit.prevent="handleSubmit" novalidate>
      <h1 class="login-title">Bejelentkezes</h1>

      <div v-if="registerMode" class="field-block">
        <label for="name" class="field-label">Neved:</label>
        <input
          id="name"
          v-model="user.name"
          type="text"
          class="field-input"
          :class="{ 'field-input-invalid': showNameError }"
          required
        />
        <div v-if="showNameError" class="field-error">A nev kotelezo</div>
      </div>

      <div class="field-block">
        <label for="email" class="field-label">Email cimed:</label>
        <input
          id="email"
          v-model="user.email"
          type="email"
          class="field-input"
          :class="{ 'field-input-invalid': showEmailError }"
          required
        />
        <div v-if="showEmailError" class="field-error">Az email ures, vagy helytelen</div>
      </div>

      <PasswordField
        :modelValue="user.password"
        @update:modelValue="onPasswordChange"
        :label="'Jelszavad'"
        :showRequiredError="showPasswordError"
        :passwordErrorMessage="'A jelszo kotelezo'"
      />

      <div class="action-row">
        <button type="submit" class="action-btn">Login</button>
        <button type="button" class="action-btn" @click="handleRegisterClick">
          {{ registerMode ? "Regisztracio kuldese" : "Regisztracio" }}
        </button>
      </div>
    </form>
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
.login-shell {
  width: min(820px, 100%);
}

.login-panel {
  width: 100%;
  padding: clamp(1.4rem, 2vw, 2rem);
  border-radius: 26px;
  border: 1px solid rgba(126, 201, 241, 0.22);
  background:
    radial-gradient(circle at 18% 18%, rgba(40, 123, 162, 0.18), transparent 58%),
    linear-gradient(115deg, rgba(14, 27, 42, 0.88), rgba(8, 16, 30, 0.86));
  backdrop-filter: blur(10px);
  box-shadow: 0 20px 42px rgba(0, 0, 0, 0.45), inset 0 1px 0 rgba(214, 242, 255, 0.1);
  color: #f1f8ff;
}

.login-title {
  margin: 0 0 1.05rem;
  font-size: clamp(1.85rem, 3vw, 2.45rem);
  font-weight: 800;
  color: #ffffff;
}

.field-block {
  margin: 0 0 0.95rem;
}

.field-label {
  display: block;
  margin-bottom: 0.55rem;
  color: #e6f4ff;
  font-size: 1.05rem;
  font-weight: 600;
}

.field-input {
  width: 100%;
  height: 42px;
  border-radius: 8px;
  border: 1px solid rgba(122, 197, 237, 0.28);
  background: linear-gradient(90deg, rgba(6, 18, 31, 0.95), rgba(4, 13, 24, 0.9));
  color: #f4f9ff;
  padding: 0 0.85rem;
  outline: none;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.field-input:focus {
  border-color: rgba(131, 219, 255, 0.92);
  box-shadow: 0 0 0 3px rgba(72, 163, 211, 0.24);
}

.field-input-invalid {
  border-color: rgba(255, 120, 120, 0.75);
  box-shadow: 0 0 0 2px rgba(255, 120, 120, 0.14);
}

.field-error {
  margin-top: 0.35rem;
  color: #ff9d9d;
  font-size: 0.88rem;
}

.action-row {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 0.85rem;
  margin-top: 1.3rem;
}

.action-btn {
  height: 42px;
  border-radius: 999px;
  border: 1px solid rgba(109, 193, 236, 0.86);
  background: linear-gradient(90deg, rgba(3, 12, 23, 0.96), rgba(6, 19, 34, 0.95));
  color: #eef9ff;
  font-size: 1.08rem;
  font-weight: 700;
  box-shadow: inset 0 0 0 1px rgba(203, 242, 255, 0.08), 0 0 14px rgba(42, 147, 204, 0.24);
  transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
}

.action-btn:hover,
.action-btn:focus {
  border-color: rgba(145, 223, 255, 0.95);
  box-shadow: inset 0 0 0 1px rgba(227, 250, 255, 0.12), 0 0 20px rgba(88, 188, 238, 0.36);
  transform: translateY(-1px);
}

@media (max-width: 640px) {
  .login-panel {
    border-radius: 20px;
    padding: 1.1rem 1rem 1.2rem;
  }

  .action-row {
    grid-template-columns: 1fr;
  }
}
</style>
