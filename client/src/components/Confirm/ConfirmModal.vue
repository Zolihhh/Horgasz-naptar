<template>
  <Transition name="modal-fade">
    <div v-if="isOpenConfirmModal" class="confirm-root">
      <div class="modal-backdrop fade show confirm-backdrop"></div>

      <div class="modal fade show d-block confirm-modal" tabindex="-1" @click.self="$emit('cancel')">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content confirm-content shadow-lg">
            <div class="modal-header confirm-header">
              <h5 class="modal-title">{{ title }}</h5>
              <button
                type="button"
                class="btn-close"
                aria-label="Bezárás"
                @click="$emit('cancel')"
              ></button>
            </div>

            <div class="modal-body confirm-body">
              <p class="mb-0">{{ message }}</p>
            </div>

            <div class="modal-footer confirm-footer">
              <button
                type="button"
                class="btn modal-btn-cancel"
                @click="$emit('cancel')"
              >
                {{ cancel }}
              </button>
              <button
                type="button"
                class="btn modal-btn-save"
                @click="$emit('confirm')"
              >
                {{ confirm }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script>
export default {
  props: {
    isOpenConfirmModal: Boolean,
    title: { type: String, default: "Megerősítés" },
    message: {
      type: String,
      default: "Biztosan törölni szeretnéd ezt az elemet?",
    },
    cancel: { type: String, default: "Nem" },
    confirm: { type: String, default: "Igen" },
  },
};
</script>

<style scoped>
.confirm-root {
  position: fixed;
  inset: 0;
  z-index: 1200;
}

.confirm-modal {
  position: fixed;
  inset: 0;
  z-index: 1201;
}

.confirm-backdrop {
  background: rgba(4, 12, 16, 0.66);
  backdrop-filter: blur(3px);
}

.confirm-content {
  border: 1px solid rgba(194, 224, 236, 0.28);
  border-radius: 14px;
  background:
    radial-gradient(circle at 88% 10%, rgba(56, 139, 182, 0.16), transparent 42%),
    radial-gradient(circle at 18% 86%, rgba(43, 106, 136, 0.12), transparent 44%),
    linear-gradient(145deg, rgba(8, 17, 25, 0.95), rgba(10, 30, 41, 0.92));
  color: #eaf6fb;
  backdrop-filter: blur(10px);
  box-shadow: 0 22px 48px rgba(0, 0, 0, 0.36);
}

.confirm-header,
.confirm-footer {
  border-color: rgba(194, 224, 236, 0.22);
}

.confirm-header .modal-title {
  font-weight: 700;
  color: #f1fbff;
}

.confirm-body p {
  color: #eaf6fb;
}

.confirm-header .btn-close {
  filter: invert(1) grayscale(1) brightness(180%);
  opacity: 0.85;
}

.confirm-header .btn-close:hover {
  opacity: 1;
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