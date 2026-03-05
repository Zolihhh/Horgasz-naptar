<template>
  <header class="header-bar pt-2 pt-md-3 px-3 px-md-4">
    <div class="header-inner">
      <h1 class="header-title">{{ resolvedTitle }}</h1>
    </div>
  </header>
</template>

<script>
export default {
  name: "HeaderBar",
  computed: {
    resolvedTitle() {
      const title = this.$route?.meta?.title;
      if (typeof title === "function") {
        return title(this.$route);
      }
      if (typeof title === "string" && title.trim()) {
        return title;
      }
      return "Oldal";
    },
  },
};
</script>

<style scoped>
.header-bar {
  padding: 0;
}

.header-inner {
  max-width: 1150px;
  margin: 0 auto;
  padding: 12px 18px;
  border-radius: 14px;
  border: 1px solid rgba(210, 232, 241, 0.24);
  background: linear-gradient(145deg, rgba(8, 17, 25, 0.8), rgba(11, 34, 47, 0.6));
  backdrop-filter: blur(7px);
  box-shadow: 0 12px 28px rgba(0, 0, 0, 0.2);
}


.header-title {
  margin: 0;
  line-height: 1.2;
  font-size: clamp(1.2rem, 3vw, 1.8rem);
  color: #ffffff;
}
</style>
