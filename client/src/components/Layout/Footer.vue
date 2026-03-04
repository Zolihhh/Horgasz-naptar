<template>
  <footer class="site-footer">
    <div class="footer-inner">
      <p class="brand">Horgász Naptár</p>
      <p class="meta">&copy; {{ year }} Minden jog fenntartva.</p>
    </div>
  </footer>
</template>

<script>
export default {
  name: "FooterBar",
  computed: {
    year() {
      return new Date().getFullYear();
    },
  },
};
</script>

<style scoped>
.site-footer {
  margin-top: 34px;
  padding: 0 20px 24px;
}

.footer-inner {
  max-width: 1150px;
  margin: 0 auto;
  padding: 16px 22px;
  border-radius: 14px;
  border: 1px solid rgba(210, 232, 241, 0.2);
  background: linear-gradient(
    145deg,
    rgba(8, 17, 25, 0.8),
    rgba(11, 34, 47, 0.62)
  );
  color: #eef7fb;
  backdrop-filter: blur(7px);
  box-shadow: 0 12px 28px rgba(0, 0, 0, 0.24);
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}

.brand {
  margin: 0;
  color: #ffffff;
  font-weight: 700;
  letter-spacing: 0.04em;
}

.meta {
  margin: 0;
  color: #d6e6ee;
  text-align: right;
}
</style>
