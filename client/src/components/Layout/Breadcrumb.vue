<template>
  <nav v-if="breadcrumbs.length > 0" class="breadcrumb-wrapper" aria-label="Breadcrumb">
    <ul class="breadcrumb-modern">
      <li v-for="(crumb, index) in breadcrumbs" :key="`${crumb.path}-${index}`">
        <router-link v-if="index < breadcrumbs.length - 1" :to="crumb.path">
          {{ crumb.label }}
        </router-link>

        <span v-else class="active">{{ crumb.label }}</span>
      </li>
    </ul>
  </nav>
</template>

<script>
export default {
  name: "BreadcrumbBar",
  computed: {
    breadcrumbs() {
      return this.$route.matched
        .filter((routeRecord) => routeRecord.meta?.breadcrumb)
        .map((routeRecord) => ({
          label: routeRecord.meta.breadcrumb,
          path: routeRecord.path,
        }));
    },
  },
};
</script>

<style scoped>
.breadcrumb-wrapper {
  max-width: 1150px;
  margin: 12px auto 0;
  padding: 10px 14px;
  border-radius: 12px;
  border: 1px solid rgba(210, 232, 241, 0.2);
  background: rgba(7, 19, 27, 0.62);
  backdrop-filter: blur(8px);
}

.breadcrumb-modern {
  margin: 0;
  padding: 0;
  display: flex;
  gap: 12px;
  list-style: none;
  align-items: center;
  flex-wrap: wrap;
}

.breadcrumb-modern li {
  display: inline-flex;
  align-items: center;
  gap: 12px;
  color: #dcebf2;
}

.breadcrumb-modern li + li::before {
  content: "/";
  color: rgba(214, 230, 238, 0.65);
}

.breadcrumb-modern a {
  color: #dcebf2;
  text-decoration: none;
}

.breadcrumb-modern a:hover {
  color: #ffffff;
}

.active {
  color: #ffffff;
  font-weight: 600;
}
</style>