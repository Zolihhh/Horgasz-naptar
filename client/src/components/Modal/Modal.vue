<template>
  <Transition name="modal-fade">
    <div v-if="isOpen" class="modal-root">
      <div class="modal-backdrop fade show modal-theme-backdrop"></div>

      <div
        ref="modalElement"
        class="modal d-block modal-theme"
        tabindex="-1"
        role="dialog"
        @click.self="hide"
      >
        <div class="modal-dialog modal-dialog-centered" :class="modalSizeClass">
          <div class="modal-content">
            <form
              @submit.prevent="onClickYes"
              :class="{ 'was-validated': validated }"
              novalidate
            >
              <div class="modal-header">
                <h1 class="modal-title fs-5" :id="labelId">{{ title }}</h1>
                <button
                  type="button"
                  class="btn-close"
                  aria-label="Bezárás"
                  @click="onCancelClick"
                ></button>
              </div>

              <div class="modal-body">
                <slot></slot>
              </div>

              <div class="modal-footer">
                <button
                  v-if="no"
                  type="button"
                  class="btn modal-btn-cancel"
                  @click="onCancelClick"
                >
                  {{ no }}
                </button>
                <button
                  type="submit"
                  class="btn modal-btn-save"
                  @click="$event.target.blur()"
                >
                  {{ yes }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script>
export default {
  emits: ["yesEvent"],
  props: {
    title: { type: String, default: "Modális ablak" },
    yes: { type: String, default: "Mentés" },
    no: { type: String, default: "Mégsem" },
    modalSize: { type: String, default: "" },
  },
  data() {
    return {
      isOpen: false,
      validated: false,
      labelId: `modal-label-${Math.random().toString(36).slice(2, 9)}`,
    };
  },
  computed: {
    modalSizeClass() {
      return {
        "modal-sm": this.modalSize === "sm",
        "modal-lg": this.modalSize === "lg",
        "modal-xl": this.modalSize === "xl",
      };
    },
  },
  beforeUnmount() {
    this.unlockBodyScroll();
  },
  methods: {
    onClickYes(event) {
      const form = event.currentTarget;
      this.validated = true;

      if (form.checkValidity() === false) {
        return;
      }

      this.$emit("yesEvent", (success) => {
        if (success) {
          this.hide();
        }
      });
    },
    onCancelClick(event) {
      this.hide();
      event?.target?.blur?.();
    },
    show() {
      this.isOpen = true;
      this.validated = false;
      this.lockBodyScroll();
      this.$nextTick(() => {
        this.$refs.modalElement?.focus?.();
      });
    },
    hide() {
      this.isOpen = false;
      this.validated = false;
      this.unlockBodyScroll();
    },
    lockBodyScroll() {
      document.body.classList.add("modal-open");
      document.body.style.overflow = "hidden";
    },
    unlockBodyScroll() {
      document.body.classList.remove("modal-open");
      document.body.style.overflow = "";
    },
  },
};
</script>

<style scoped>
.modal-root {
  position: fixed;
  inset: 0;
  z-index: 1200;
}

.modal-theme {
  position: fixed;
  inset: 0;
  z-index: 1201;
}

.modal-theme-backdrop {
  background: rgba(4, 12, 16, 0.66);
  backdrop-filter: blur(3px);
}

.modal-theme .modal-content {
  border: 1px solid rgba(194, 224, 236, 0.28);
  border-radius: 14px;
  background:
    radial-gradient(circle at 85% 12%, rgba(50, 131, 175, 0.18), transparent 38%),
    radial-gradient(circle at 15% 88%, rgba(49, 112, 143, 0.14), transparent 44%),
    linear-gradient(145deg, rgba(8, 17, 25, 0.95), rgba(10, 30, 41, 0.92));
  color: #eaf6fb;
  backdrop-filter: blur(10px);
  box-shadow: 0 22px 48px rgba(0, 0, 0, 0.36);
}

.modal-theme .modal-header,
.modal-theme .modal-footer {
  border-color: rgba(194, 224, 236, 0.22);
}

.modal-theme .modal-title {
  color: #f1fbff;
  font-weight: 700;
}

.modal-theme .btn-close {
  filter: invert(1) grayscale(1) brightness(180%);
  opacity: 0.85;
}

.modal-theme .btn-close:hover {
  opacity: 1;
}

.modal-theme .modal-body :deep(.form-label) {
  color: #ecf7fc;
}

.modal-theme .modal-body :deep(.form-control),
.modal-theme .modal-body :deep(.form-select) {
  border: 1px solid rgba(194, 224, 236, 0.33);
  border-radius: 10px;
  background: rgba(5, 14, 20, 0.54);
  color: #f4fcff;
}

.modal-theme .modal-body :deep(.form-control:focus),
.modal-theme .modal-body :deep(.form-select:focus) {
  border-color: rgba(167, 220, 243, 0.85);
  box-shadow: 0 0 0 0.2rem rgba(143, 202, 229, 0.2);
}

.modal-theme .modal-body :deep(.alert) {
  border-radius: 10px;
}

.modal-btn-cancel {
  border: 1px solid rgba(194, 224, 236, 0.52);
  border-radius: 10px;
  background: rgba(5, 14, 20, 0.6);
  color: #eaf6fb;
  font-weight: 600;
}

.modal-btn-cancel:hover {
  background: rgba(17, 43, 57, 0.8);
  color: #fff;
}

.modal-btn-save {
  border: 1px solid rgba(248, 181, 181, 0.45);
  border-radius: 10px;
  background: linear-gradient(145deg, rgba(145, 33, 41, 0.93), rgba(171, 49, 58, 0.9));
  color: #fff;
  font-weight: 700;
}

.modal-btn-save:hover {
  background: linear-gradient(145deg, rgba(164, 38, 47, 0.95), rgba(185, 56, 65, 0.92));
}

.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.25s ease;
}

.modal-fade-enter-active .modal-dialog,
.modal-fade-leave-active .modal-dialog {
  transition: transform 0.25s ease-out;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}

.modal-fade-enter-from .modal-dialog,
.modal-fade-leave-to .modal-dialog {
  transform: translateY(-16px);
}
</style>
