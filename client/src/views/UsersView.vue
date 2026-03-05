<template>
  <section class="users-wrap users-view">
    <header class="users-head">
      <div class="title-row">
        <h1>{{ pageTitle }}</h1>
        <div class="count-wrap">
          <i v-if="loading" class="bi bi-hourglass-split"></i>
          <p>({{ sortedItems.length }})</p>
        </div>
      </div>

      <div class="search-wrap">
        <input
          v-model.trim="searchTerm"
          type="text"
          class="users-search"
          placeholder="Keresés név, email vagy szerepkör szerint..."
        />
      </div>
    </header>

    <div class="users-table-card">
      <div v-if="sortedItems.length > 0" class="users-controls">
        <SetSelectedPerPage
          v-model="selectedPerPage"
          label="Sor/oldal:"
          :options="[5, 10, 20, 50]"
          all-label="Összes"
        />

        <Pagination
          :current-page="currentPage"
          :last-page="lastPage"
          @change="changePage"
        />
      </div>

      <GenericTable
        v-if="paginatedItems.length > 0"
        :items="paginatedItems"
        :columns="tableColumns"
        :useCollectionStore="useCollectionStore"
        :sort-column="sortColumnLocal"
        :sort-direction="sortDirectionLocal"
        :cButtonVisible="false"
        :uButtonVisible="true"
        :dButtonVisible="true"
        :pButtonVisible="false"
        @sort="sortHandler"
        @delete="openDeleteConfirm"
        @update="openEditModal"
      />

      <p v-else class="empty-state">Nincs találat</p>
    </div>

    <ConfirmModal
      :isOpenConfirmModal="isDeleteConfirmOpen"
      title="Törlés megerősítése"
      :message="deleteMessage"
      cancel="Mégse"
      confirm="Törlés"
      @cancel="cancelDelete"
      @confirm="confirmDelete"
    />

    <Modal
      ref="editUserModal"
      title="Felhasználó módosítása"
      yes="Mentés"
      no="Mégse"
      @yesEvent="saveEditedUser"
    >
      <div class="row g-3 users-edit-modal">
        <div v-if="editError" class="col-12">
          <div class="alert alert-danger py-2 mb-0">{{ editError }}</div>
        </div>

        <div class="col-12">
          <label for="edit-name" class="form-label">Név</label>
          <input
            id="edit-name"
            v-model.trim="editUser.name"
            type="text"
            class="form-control"
            minlength="2"
            required
          />
        </div>

        <div class="col-12">
          <label for="edit-email" class="form-label">Email</label>
          <input
            id="edit-email"
            v-model.trim="editUser.email"
            type="email"
            class="form-control"
            required
          />
        </div>

        <div class="col-12">
          <label for="edit-role" class="form-label">Szerepkör</label>
          <select
            id="edit-role"
            v-model.number="editUser.role"
            class="form-select"
            :disabled="isEditingOwnAdmin"
            required
          >
            <option :value="1">Admin</option>
            <option :value="2">Horgász</option>
          </select>
          <div v-if="isEditingOwnAdmin" class="form-text text-info-emphasis mt-1">
            A saját admin szerepkör nem módosítható.
          </div>
        </div>
      </div>
    </Modal>
  </section>
</template>

<script>
import { mapActions, mapState } from "pinia";
import { useUserStore } from "@/stores/userStore";
import { useUserLoginLogoutStore } from "@/stores/userLoginLogoutStore";
import GenericTable from "@/components/Table/GenericTable.vue";
import Pagination from "@/components/Pagination/Pagination.vue";
import SetSelectedPerPage from "@/components/Pagination/SetSelectedPerPage.vue";
import ConfirmModal from "@/components/Confirm/ConfirmModal.vue";
import Modal from "@/components/Modal/Modal.vue";

export default {
  name: "UsersView",
  components: {
    ConfirmModal,
    GenericTable,
    Modal,
    Pagination,
    SetSelectedPerPage,
  },
  watch: {
    filteredItems() {
      this.ensureValidPage();
    },
    lastPage() {
      this.ensureValidPage();
    },
  },
  data() {
    return {
      pageTitle: "Összes felhasználó",
      searchTerm: "",
      currentPage: 1,
      selectedPerPage: 10,
      sortColumnLocal: "id",
      sortDirectionLocal: "asc",
      isDeleteConfirmOpen: false,
      pendingDeleteId: null,
      editError: "",
      editUser: {
        id: null,
        name: "",
        email: "",
        role: 2,
      },
      editUserOriginal: {
        name: "",
        email: "",
        role: 2,
      },
      tableColumns: [
        { key: "id", label: "ID", debug: 2 },
        { key: "name", label: "User név", debug: 2 },
        { key: "email", label: "Email", debug: 2 },
        { key: "role", label: "Szerepkör", debug: 2 },
      ],
      useCollectionStore: useUserStore,
    };
  },
  computed: {
    ...mapState(useUserStore, ["items", "loading"]),
    ...mapState(useUserLoginLogoutStore, ["item"]),
    isEditingOwnAdmin() {
      return Number(this.item?.id) === Number(this.editUser.id) && Number(this.item?.role) === 1;
    },
    filteredItems() {
      const term = this.searchTerm.toLowerCase();
      if (!term) {
        return this.items;
      }

      return this.items.filter((item) => {
        const roleText = Number(item.role) === 1 ? "admin" : "horgász";

        return (
          String(item.name || "").toLowerCase().includes(term) ||
          String(item.email || "").toLowerCase().includes(term) ||
          String(item.role || "").toLowerCase().includes(term) ||
          roleText.includes(term)
        );
      });
    },
    sortedItems() {
      const list = [...this.filteredItems];
      const key = this.sortColumnLocal;
      const directionMultiplier = this.sortDirectionLocal === "asc" ? 1 : -1;

      return list.sort((a, b) => {
        let valueA = a?.[key];
        let valueB = b?.[key];

        if (key === "id" || key === "role") {
          valueA = Number(valueA || 0);
          valueB = Number(valueB || 0);
          return (valueA - valueB) * directionMultiplier;
        }

        valueA = String(valueA || "").toLowerCase();
        valueB = String(valueB || "").toLowerCase();
        return valueA.localeCompare(valueB, "hu") * directionMultiplier;
      });
    },
    effectivePerPage() {
      if (this.selectedPerPage >= 1000000) {
        return Math.max(1, this.sortedItems.length);
      }
      return Math.max(1, Number(this.selectedPerPage) || 10);
    },
    lastPage() {
      return Math.max(1, Math.ceil(this.sortedItems.length / this.effectivePerPage));
    },
    paginatedItems() {
      if (!this.sortedItems.length) {
        return [];
      }

      const start = (this.currentPage - 1) * this.effectivePerPage;
      const end = start + this.effectivePerPage;
      return this.sortedItems.slice(start, end);
    },
    deleteMessage() {
      if (!this.pendingDeleteId) {
        return "Biztosan törölni szeretnéd ezt a felhasználót?";
      }

      const user = this.items.find((item) => item.id === this.pendingDeleteId);
      if (!user) {
        return `Biztosan törölni szeretnéd a felhasználót (ID: ${this.pendingDeleteId})?`;
      }

      return `Biztosan törölni szeretnéd a felhasználót (${user.name})?`;
    },
  },
  methods: {
    ...mapActions(useUserStore, {
      deleteUser: "delete",
      getAll: "getAll",
      updateUser: "update",
    }),
    sortHandler(column) {
      if (this.sortColumnLocal === column) {
        this.sortDirectionLocal = this.sortDirectionLocal === "asc" ? "desc" : "asc";
      } else {
        this.sortColumnLocal = column;
        this.sortDirectionLocal = "asc";
      }
      this.currentPage = 1;
    },
    changePage(page) {
      this.currentPage = page;
      this.ensureValidPage();
    },
    openDeleteConfirm(id) {
      this.pendingDeleteId = id;
      this.isDeleteConfirmOpen = true;
    },
    cancelDelete() {
      this.pendingDeleteId = null;
      this.isDeleteConfirmOpen = false;
    },
    async confirmDelete() {
      const id = this.pendingDeleteId;
      if (!id) {
        this.cancelDelete();
        return;
      }

      try {
        await this.deleteUser(id);
      } finally {
        this.cancelDelete();
        this.ensureValidPage();
      }
    },
    openEditModal(id) {
      const user = this.items.find((item) => item.id === id);
      if (!user) {
        return;
      }

      this.editUser = {
        id: user.id,
        name: String(user.name || ""),
        email: String(user.email || ""),
        role: Number(user.role) === 1 ? 1 : 2,
      };
      this.editUserOriginal = {
        name: String(user.name || ""),
        email: String(user.email || ""),
        role: Number(user.role) === 1 ? 1 : 2,
      };
      this.editError = "";

      this.$nextTick(() => {
        this.$refs.editUserModal?.show();
      });
    },
    async saveEditedUser(done) {
      if (!this.editUser.id) {
        this.editError = "Hiányzó felhasználó azonosító.";
        done(false);
        return;
      }

      this.editError = "";
      const payload = {};

      const nextName = String(this.editUser.name || "").trim();
      const nextEmail = String(this.editUser.email || "").trim();
      const nextRole = Number(this.editUser.role || 2);

      const prevName = String(this.editUserOriginal.name || "").trim();
      const prevEmail = String(this.editUserOriginal.email || "").trim();
      const prevRole = Number(this.editUserOriginal.role || 2);

      if (nextName !== prevName) {
        payload.name = nextName;
      }
      if (nextEmail !== prevEmail) {
        payload.email = nextEmail;
      }
      if (!this.isEditingOwnAdmin && nextRole !== prevRole) {
        payload.role = nextRole;
      }

      if (Object.keys(payload).length === 0) {
        done(true);
        return;
      }

      try {
        await this.updateUser(this.editUser.id, payload);
        await this.getAll();
        done(true);
      } catch (error) {
        this.editError = this.getErrorMessage(error, "A módosítás nem sikerült.");
        done(false);
      }
    },
    getErrorMessage(error, fallback) {
      const responseData = error?.response?.data;
      const message = responseData?.message;
      if (typeof message === "string" && message.trim()) {
        return message;
      }

      const errors = responseData?.errors;
      if (errors && typeof errors === "object") {
        const firstError = Object.values(errors)?.[0];
        if (Array.isArray(firstError) && firstError[0]) {
          return String(firstError[0]);
        }
        if (typeof firstError === "string") {
          return firstError;
        }
      }

      return fallback;
    },
    ensureValidPage() {
      if (this.currentPage < 1) {
        this.currentPage = 1;
      }
      if (this.currentPage > this.lastPage) {
        this.currentPage = this.lastPage;
      }
    },
  },
  async mounted() {
    await this.getAll();
    this.ensureValidPage();
  },
};
</script>
