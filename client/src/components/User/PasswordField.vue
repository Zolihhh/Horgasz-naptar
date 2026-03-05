<template>
  <div class="password-field">
    <label v-if="label" class="field-label" :for="labelId">{{ label }}:</label>

    <div class="password-row" :class="{ 'password-row-invalid': showRequiredError || !!serverErrors?.password }">
      <input
        :id="labelId"
        :ref="inputRef"
        :type="showPassword ? 'text' : 'password'"
        class="password-input"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        required
      />

      <button
        class="toggle-visibility-btn"
        type="button"
        @click="showPassword = !showPassword"
        :aria-label="showPassword ? 'Jelszó elrejtése' : 'Jelszó megjelenítése'"
      >
        <svg
          v-if="!showPassword"
          xmlns="http://www.w3.org/2000/svg"
          width="18"
          height="18"
          fill="currentColor"
          viewBox="0 0 16 16"
          aria-hidden="true"
        >
          <path
            d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.12 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"
          />
          <path
            d="M8 5.5A2.5 2.5 0 1 0 8 10.5a2.5 2.5 0 0 0 0-5m-4 2.5a4 4 0 1 1 8 0 4 4 0 0 1-8 0"
          />
        </svg>
        <svg
          v-else
          xmlns="http://www.w3.org/2000/svg"
          width="18"
          height="18"
          fill="currentColor"
          viewBox="0 0 16 16"
          aria-hidden="true"
        >
          <path
            d="M13.359 11.238 15.5 13.379l-.708.707-2.13-2.13A8.7 8.7 0 0 1 8 13.5C3 13.5 0 8 0 8a13.1 13.1 0 0 1 3.278-3.893L1.146 1.975l.708-.707 13.213 13.213zM11.635 10.514l-1.246-1.246a2.5 2.5 0 0 1-3.657-3.657L5.51 4.389A12 12 0 0 0 1.173 8c.058.087.122.183.195.288C2.338 9.68 4.688 12.5 8 12.5a7.7 7.7 0 0 0 3.635-.986"
          />
          <path
            d="m11.297 8.883-1.41-1.41a1.5 1.5 0 0 1-2.36-2.36l-1.41-1.41A4 4 0 0 1 12 8c0 .3-.033.593-.094.883zM8 3.5c3.312 0 5.662 2.82 6.632 4.212.073.105.137.201.195.288q-.335.5-.758.985l.719.718A13 13 0 0 0 16 8s-3-5.5-8-5.5a8.3 8.3 0 0 0-2.873.506l.779.779A7.3 7.3 0 0 1 8 3.5"
          />
        </svg>
      </button>
    </div>

    <div v-if="showRequiredError" class="field-error">
      {{ passwordErrorMessage || "A jelszó kötelező" }}
    </div>
    <div v-if="serverErrors?.password" class="field-error">
      {{ serverErrors.password[0] }}
    </div>
  </div>
</template>

<script>
export default {
  props: {
    modelValue: { type: String, default: "" },
    label: { type: String, default: "Jelszó" },
    inputRef: { type: String, default: "" },
    labelId: { type: String, default: "" },
    passwordErrorMessage: { type: String, default: "" },
    showRequiredError: { type: Boolean, default: false },
    serverErrors: { type: Object, default: () => ({}) },
  },
  data() {
    return {
      showPassword: false,
    };
  },
};
</script>

<style scoped>
.password-field {
  margin: 0 0 0.95rem;
}

.field-label {
  display: block;
  margin-bottom: 0.55rem;
  color: #e6f4ff;
  font-size: 1.05rem;
  font-weight: 600;
}

.password-row {
  display: flex;
  align-items: center;
  gap: 0.45rem;
}

.password-input {
  flex: 1;
  height: 42px;
  border-radius: 8px;
  border: 1px solid rgba(122, 197, 237, 0.28);
  background: linear-gradient(90deg, rgba(6, 18, 31, 0.95), rgba(4, 13, 24, 0.9));
  color: #f4f9ff;
  padding: 0 0.85rem;
  outline: none;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.password-input:focus {
  border-color: rgba(131, 219, 255, 0.92);
  box-shadow: 0 0 0 3px rgba(72, 163, 211, 0.24);
}

.toggle-visibility-btn {
  width: 52px;
  min-width: 52px;
  height: 42px;
  border: 1px solid rgba(109, 193, 236, 0.86);
  background: linear-gradient(90deg, rgba(3, 12, 23, 0.96), rgba(6, 19, 34, 0.95));
  color: #eef9ff;
  border-radius: 10px;
  box-shadow: inset 0 0 0 1px rgba(203, 242, 255, 0.08), 0 0 14px rgba(42, 147, 204, 0.24);
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.toggle-visibility-btn:hover,
.toggle-visibility-btn:focus {
  border-color: rgba(145, 223, 255, 0.95);
  box-shadow: inset 0 0 0 1px rgba(227, 250, 255, 0.12), 0 0 20px rgba(88, 188, 238, 0.36);
}

.password-row-invalid .password-input,
.password-row-invalid .toggle-visibility-btn {
  border-color: rgba(255, 120, 120, 0.75);
}

.field-error {
  margin-top: 0.35rem;
  color: #ff9d9d;
  font-size: 0.88rem;
}
</style>
