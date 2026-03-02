<template>
  <div class="fishing-view">

    <!-- ===== FEJLÉC ===== -->
    <div class="page-header glass">

      <div class="header-left">
        <h1>🎣 {{ pageTitle }}</h1>
        <span class="record-count">({{ getItemsLength }} fogás)</span>
      </div>

      <div class="header-right">
        <!-- loading -->
        <i
          v-if="loading"
          class="bi bi-hourglass-split fs-4 loading-icon"
        ></i>

        <!-- új rekord -->
        <ButtonsCrudCreate
          v-if="!loading"
          @create="createHandler"
        />

        <!-- sor/oldal -->
        <SetSelectedPerPage
          :useCollectionStore="useCollectionStore"
        />

        <!-- lapozás -->
        <Pagination
          :useCollectionStore="useCollectionStore"
        />
      </div>

    </div>

    <!-- ===== TÁBLÁZAT ===== -->
    <div class="table-wrapper glass">
      <GenericTable
        v-if="items.length > 0"
        :items="items"
        :columns="tableColumns"
        :useCollectionStore="useCollectionStore"
        @delete="deleteHandler"
        @update="updateHandler"
        @create="createHandler"
        @sort="sortHandler"
      />
      <div v-else class="no-result">
        🎣 Még nincs rögzített fogás
      </div>
    </div>

    <!-- ===== FORM MODAL ===== -->
    <FormSport
      ref="form"
      :title="title"
      :item="item"
      @yesEventForm="yesEventFormHandler"
    />

    <!-- ===== CONFIRM MODAL ===== -->
    <ConfirmModal
      :isOpenConfirmModal="isOpenConfirmModal"
      @cancel="cancelHandler"
      @confirm="confirmHandler"
    />

  </div>
</template>
<style scoped>

.fishing-view {
  padding: 20px;
  color: white;
}

/* ===== FEJLÉC ===== */
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 25px;
  padding: 20px;
  border-radius: 20px;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 15px;
}

.header-left h1 {
  margin: 0;
  font-size: 2rem;
}

.record-count {
  opacity: 0.8;
}

.header-right {
  display: flex;
  align-items: center;
  gap: 15px;
}

.loading-icon {
  animation: spin 1s linear infinite;
}

/* ===== TÁBLÁZAT ===== */
.table-wrapper {
  padding: 20px;
  border-radius: 20px;
}

.no-result {
  text-align: center;
  padding: 40px;
  opacity: 0.7;
  font-size: 1.2rem;
}

/* ===== GLASS EFFECT ===== */
.glass {
  background: rgba(255,255,255,0.08);
  backdrop-filter: blur(15px);
  box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}

/* ===== ANIM ===== */
@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

</style>