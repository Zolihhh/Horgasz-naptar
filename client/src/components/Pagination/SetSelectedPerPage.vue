<template>
  <div class="d-flex align-items-center p-0 ms-2">
    <label class="me-2" for="select-per-page">
      {{ label }}
    </label>

    <select
      id="select-per-page"
      v-model.number="selectedPerPage"
      class="form-select form-select-sm"
      style="width: auto"
    >
      <option v-if="includeAllOption" :value="allValue">{{ allLabel }}</option>
      <option
        v-for="(item, index) in normalizedOptions"
        :key="index"
        :value="item"
      >
        {{ item }}
      </option>
    </select>
  </div>
</template>

<script>
export default {
  name: "SetSelectedPerPage",
  props: {
    useCollectionStore: { type: Function, default: null },
    label: { type: String, default: "Sor/oldal:" },
    modelValue: { type: Number, default: null },
    options: {
      type: Array,
      default: () => [10, 20, 50],
    },
    includeAllOption: { type: Boolean, default: true },
    allValue: { type: Number, default: 1000000 },
    allLabel: { type: String, default: "Összes" },
  },
  data() {
    return {
      store: null,
      selectedPerPage: this.modelValue ?? 10,
    };
  },
  computed: {
    normalizedOptions() {
      if (this.store?.selectedPerPageList?.length) {
        return this.store.selectedPerPageList;
      }
      return this.options;
    },
  },
  watch: {
    modelValue(value) {
      if (value !== null && value !== this.selectedPerPage) {
        this.selectedPerPage = value;
      }
    },
    selectedPerPage(value) {
      const parsedValue = Number(value) || 10;

      if (this.store?.setSelectedPerPage) {
        this.store.setSelectedPerPage(parsedValue);
      }

      this.$emit("update:modelValue", parsedValue);
      this.$emit("change", parsedValue);
    },
  },
  created() {
    if (this.useCollectionStore) {
      this.store = this.useCollectionStore();

      if (this.store?.selectedPerPageList?.length) {
        this.selectedPerPage = this.store.selectedPerPageList[0];
      }
    } else if (this.modelValue === null) {
      this.selectedPerPage = this.options[0] ?? 10;
    }
  },
};
</script>
