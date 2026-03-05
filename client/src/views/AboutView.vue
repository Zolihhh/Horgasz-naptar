<template>
  <section class="about-view">
    <header class="about-hero">
      <p class="about-eyebrow">Horgász Naptár</p>
      <h1>Rólunk</h1>
      <p class="about-lead">
        Ez az alkalmazás azért készült, hogy a fogási naplózás és az időjárás követése egy gyors,
        átlátható felületen történjen.
      </p>
    </header>

    <section class="about-grid">
      <article class="about-card">
        <h2>Mit tud az alkalmazás?</h2>
        <ul>
          <li>Fogások rögzítése és visszakeresése egy helyen.</li>
          <li>Időjárási adatok megjelenítése a tervezéshez.</li>
          <li>Felhasználói és szerepkör-kezelés admin felületen.</li>
        </ul>
      </article>

      <article class="about-card">
        <h2>Kinek szól?</h2>
        <ul>
          <li>Horgászoknak, akik szeretnék nyomon követni a fogásaikat.</li>
          <li>Adminoknak, akik kezelik a felhasználókat és adatokat.</li>
          <li>Mindenkinek, aki egyszerű és gyors felületet keres.</li>
        </ul>
      </article>
    </section>

    <section class="about-card meta-card">
      <h2>Technikai adatok</h2>
      <div class="meta-grid">
        <div class="meta-item">
          <span>Alkalmazás neve</span>
          <strong>{{ appTitle }}</strong>
        </div>
        <div class="meta-item">
          <span>Verzió</span>
          <strong>{{ appVersion }}</strong>
        </div>
        <div class="meta-item">
          <span>API cím</span>
          <strong>{{ apiUrl }}</strong>
        </div>
        <div class="meta-item">
          <span>Környezet</span>
          <strong>{{ mode }}</strong>
        </div>
      </div>
    </section>
  </section>
</template>

<script>
export default {
  name: "AboutView",
  computed: {
    appTitle() {
      return import.meta.env.VITE_APP_TITLE || "Horgász Naptár";
    },
    appVersion() {
      return import.meta.env.VITE_APP_VER || "ismeretlen";
    },
    apiUrl() {
      return import.meta.env.VITE_API_URL || "nincs beállítva";
    },
    mode() {
      return import.meta.env.MODE || "ismeretlen";
    },
  },
};
</script>

<style scoped>
.about-view {
  max-width: 1150px;
  margin: 0 auto;
  padding: 28px 20px 40px;
  color: #ffffff;
}

.about-hero {
  border-radius: 20px;
  border: 1px solid rgba(210, 232, 241, 0.22);
  background: linear-gradient(130deg, rgba(7, 21, 30, 0.82), rgba(16, 45, 61, 0.58));
  backdrop-filter: blur(8px);
  padding: clamp(22px, 4.5vw, 40px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
}

.about-eyebrow {
  margin: 0 0 10px;
  text-transform: uppercase;
  letter-spacing: 0.14em;
  font-size: 0.75rem;
  color: #bad7e4;
}

.about-hero h1 {
  margin: 0;
  font-size: clamp(1.9rem, 4vw, 2.9rem);
  line-height: 1.12;
}

.about-lead {
  margin: 12px 0 0;
  max-width: 70ch;
  color: #d8e6ee;
}

.about-grid {
  margin-top: 16px;
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 14px;
}

.about-card {
  border: 1px solid rgba(202, 227, 238, 0.2);
  background: linear-gradient(145deg, rgba(8, 17, 25, 0.78), rgba(11, 34, 47, 0.62));
  backdrop-filter: blur(7px);
  padding: 16px;
  border-radius: 14px;
  box-shadow: 0 14px 32px rgba(0, 0, 0, 0.2);
}

.about-card h2 {
  margin: 0 0 10px;
  font-size: 1.15rem;
  color: #ffffff;
}

.about-card ul {
  margin: 0;
  padding-left: 18px;
  color: #d6e6ee;
}

.about-card li + li {
  margin-top: 6px;
}

.meta-card {
  margin-top: 14px;
}

.meta-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 10px;
}

.meta-item {
  border: 1px solid rgba(194, 224, 236, 0.2);
  border-radius: 10px;
  padding: 10px 12px;
  background: rgba(5, 14, 20, 0.45);
}

.meta-item span {
  display: block;
  color: #c7dce7;
  font-size: 0.84rem;
  margin-bottom: 2px;
}

.meta-item strong {
  color: #ffffff;
  word-break: break-word;
}

@media (max-width: 860px) {
  .about-grid,
  .meta-grid {
    grid-template-columns: 1fr;
  }
}
</style>
