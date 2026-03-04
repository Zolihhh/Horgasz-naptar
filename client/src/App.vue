<template>
  <div class="app-shell">
    <MenuBar />

    <main class="app-main">
      <RouterView />
    </main>

    <FooterBar />
  </div>
</template>

<script>
import FooterBar from "@/components/Layout/Footer.vue";
import MenuBar from "@/components/Layout/Menu.vue";

export default {
  name: "App",
  components: {
    FooterBar,
    MenuBar,
  },
};
</script>

<style>
body {
  margin: 0;
  font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
  color: #fff;
}

.app-shell {
  position: relative;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  isolation: isolate;
}

.app-shell::before {
  content: "";
  position: fixed;
  inset: 0;
  background: url("@/assets/2.jpg") center/cover no-repeat fixed;
  filter: blur(6px);
  transform: scale(1.04);
  z-index: -2;
}

.app-shell::after {
  content: "";
  position: fixed;
  inset: 0;
  background: linear-gradient(
    180deg,
    rgba(4, 10, 14, 0.5),
    rgba(3, 8, 12, 0.6)
  );
  z-index: -1;
}

.app-main {
  flex: 1;
}
</style>
