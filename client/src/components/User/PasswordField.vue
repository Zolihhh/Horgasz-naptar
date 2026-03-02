<template>
  <div class="mb-3">
    <label v-if="label" class="form-label" :for="labelId">{{ label }}: </label>
    <div class="input-group">
      <input
        :id="labelId"
        :ref="inputRef"
        :type="showPassword ? 'text' : 'password'"
        class="form-control"
        :class="{ 'is-invalid': showRequiredError }"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        required
      />
      <button
        class="btn btn-outline-secondary ms-1 toggle-visibility-btn"
        type="button"
        @click="showPassword = !showPassword"
        :aria-label="showPassword ? 'Jelszo elrejtese' : 'Jelszo megjelenitese'"
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

      <div v-if="showRequiredError" class="invalid-feedback d-block">
        {{ passwordErrorMessage || "A jelszo kotelezo" }}
      </div>
      <div v-if="serverErrors?.password" class="invalid-feedback d-block">
        {{ serverErrors.password[0] }}
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    modelValue: { type: String, default: "" },
    label: { type: String, default: "Jelszo" },
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
.toggle-visibility-btn {
  min-width: 46px;
  border: 1px solid rgba(101, 186, 229, 0.75);
  background: rgba(7, 12, 18, 0.82);
  color: #eef7ff;
  border-radius: 10px;
  box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.06),
    0 0 14px rgba(76, 170, 220, 0.18);
}

.toggle-visibility-btn:hover,
.toggle-visibility-btn:focus {
  background: rgba(11, 18, 27, 0.95);
  color: #ffffff;
  border-color: rgba(123, 206, 246, 0.9);
  box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.09),
    0 0 18px rgba(89, 189, 238, 0.3);
}
</style>
