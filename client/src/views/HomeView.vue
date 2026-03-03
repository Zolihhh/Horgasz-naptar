<template>
  <section class="home-wrap">
    <header class="hero">
      <p class="eyebrow">Horgasz Naptar</p>
      <h1>Átlathato kezeles a fogasokhoz es idojarashoz</h1>
      <p class="hero-text">
        Egy feluleten kovetheted a fogasi adatokat, helyszineket es a kovetkezo napok varhato idojarasat.
      </p>
    </header>

    <section class="features">
      <div
        v-if="speciesPages.length"
        id="fishCardsCarousel"
        class="carousel slide"
        data-bs-ride="carousel"
        data-bs-interval="5000"
        data-bs-pause="false"
        data-bs-wrap="true"
      >
        <div class="carousel-inner">
          <div
            v-for="(page, pageIndex) in speciesPages"
            :key="`page-${pageIndex}`"
            class="carousel-item"
            :class="{ active: pageIndex === 0 }"
          >
            <div class="row g-3">
              <div v-for="fish in page" :key="fish.id" class="col-12 col-sm-6 col-lg-3">
                <article class="feature-card fish-card h-100">
                  <img
                    class="fish-image"
                    :src="getFishImageUrl(fish.photo)"
                    :alt="fish.fish_name"
                    loading="lazy"
                  />
                  <div class="fish-content">
                    <h2>{{ fish.fish_name }}</h2>
                    <p>{{ getFishHint(fish.fish_name) }}</p>
                  </div>
                </article>
              </div>
            </div>
          </div>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#fishCardsCarousel" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#fishCardsCarousel" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Next</span>
        </button>
      </div>
      <p v-else class="feature-empty">Nincs megjelenitheto halkep.</p>
    </section>

    <div v-if="!isLoggedIn" class="login-theme-wrap">
      <UserLogin @logIn="loginHandler" @register="registerHandler" />
    </div>
  </section>
</template>

<script>
import { mapActions, mapState } from "pinia";
import { useUserLoginLogoutStore } from "@/stores/userLoginLogoutStore";
import UserLogin from "@/components/User/UserLogin.vue";
import specieService from "@/api/specieService";

const API_BASE = (import.meta.env.VITE_API_URL || "").replace(/\/api\/?$/, "");

export default {
  name: "HomeView",
  components: {
    UserLogin,
  },
  computed: {
    ...mapState(useUserLoginLogoutStore, ["isLoggedIn"]),
    speciesPages() {
      const pageSize = 4;
      const pages = [];
      for (let i = 0; i < this.featuredSpecies.length; i += pageSize) {
        pages.push(this.featuredSpecies.slice(i, i + pageSize));
      }
      return pages;
    },
  },
  data() {
    return {
      featuredSpecies: [],
    };
  },
  async mounted() {
    await this.fetchFeaturedSpecies();
  },
  methods: {
    ...mapActions(useUserLoginLogoutStore, ["login", "register"]),
    async fetchFeaturedSpecies() {
      try {
        const response = await specieService.getAll();
        const species = Array.isArray(response?.data) ? response.data : [];
        this.featuredSpecies = species.filter((row) => row?.photo && row?.fish_name);
      } catch (error) {
        this.featuredSpecies = [];
      }
    },
    getFishImageUrl(photo) {
      const encoded = encodeURIComponent(photo || "");
      return `${API_BASE}/api/species/photo/${encoded}`;
    },
    getFishHint(name) {
      const key = (name || "").toLowerCase();
      if (key.includes("ponty")) return "Nepszeru bekes hal, melegebb vizekben aktiv.";
      if (key.includes("csuka")) return "Ragadozo hal, gyakran a parti novenyzetnel all.";
      if (key.includes("harcsa")) return "Nagy testu ragadozo, inkabb este mozog.";
      if (key.includes("sullo") || key.includes("süllő")) return "Erzekeny ragadozo, tisztabb vizben eredmenyes.";
      if (key.includes("balin")) return "Gyors ragadozo, felszin kozeleben tamad.";
      return "Magyar vizekben gyakran fogott, ertekes halfaj.";
    },
    async loginHandler(user) {
      try {
        await this.login(user);
      } catch (error) {
        console.log("Bejelentkezesi hiba");
      }
    },
    async registerHandler({ user, done }) {
      try {
        await this.register(user);
        done(true);
      } catch (error) {
        done(false);
        console.log("Regisztracios hiba");
      }
    },
  },
};
</script>

<style scoped>
.home-wrap {
  max-width: 1150px;
  margin: 0 auto;
  padding: 28px 20px 40px;
  color: #ffffff;
}

.hero {
  border-radius: 20px;
  border: 1px solid rgba(210, 232, 241, 0.22);
  background: linear-gradient(
    130deg,
    rgba(7, 21, 30, 0.82),
    rgba(16, 45, 61, 0.58)
  );
  backdrop-filter: blur(8px);
  padding: clamp(22px, 4.5vw, 42px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
}

.eyebrow {
  margin: 0 0 10px;
  text-transform: uppercase;
  letter-spacing: 0.14em;
  font-size: 0.75rem;
  color: #bad7e4;
}

.hero h1 {
  margin: 0;
  max-width: 18ch;
  font-size: clamp(1.9rem, 4vw, 3rem);
  line-height: 1.12;
  color: #ffffff;
}

.hero-text {
  margin: 14px 0 0;
  max-width: 62ch;
  color: #d8e6ee;
}

.features {
  margin-top: 16px;
}

.feature-card {
  border: 1px solid rgba(202, 227, 238, 0.2);
  background: linear-gradient(
    145deg,
    rgba(8, 17, 25, 0.78),
    rgba(11, 34, 47, 0.62)
  );
  backdrop-filter: blur(7px);
  padding: 16px;
  border-radius: 14px;
  box-shadow: 0 14px 32px rgba(0, 0, 0, 0.2);
}

.fish-card {
  padding: 0;
  overflow: hidden;
  max-width: 420px;
  margin: 0 auto;
}

.fish-image {
  width: 100%;
  height: 170px;
  object-fit: cover;
  display: block;
  background: rgba(9, 21, 28, 0.8);
}

.fish-content {
  padding: 14px 16px 16px;
}

.feature-card h2 {
  margin: 0 0 6px;
  font-size: 1.15rem;
  color: #ffffff;
}

.feature-card p {
  margin: 0;
  color: #d6e6ee;
}

.feature-empty {
  margin: 0;
  color: #d6e6ee;
}

.login-theme-wrap {
  width: 100%;
  display: flex;
  justify-content: center;
  padding: 20px 0 0;
}

.login-theme-wrap :deep(.card) {
  width: min(92vw, 640px) !important;
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 22px;
  overflow: hidden;
  background: rgba(8, 15, 22, 0.62);
  backdrop-filter: blur(10px);
  box-shadow: 0 20px 45px rgba(0, 0, 0, 0.38);
}

.login-theme-wrap :deep(.card-header) {
  padding: 1.5rem 1.5rem 0.5rem;
  background: transparent !important;
  color: #ffffff !important;
  border-bottom: 0;
  font-weight: 700;
  font-size: 2rem;
  letter-spacing: 0.01em;
}

.login-theme-wrap :deep(.card-body) {
  padding: 0.25rem 1.5rem 1.5rem;
  background: transparent;
  color: #eef7fb;
}

.login-theme-wrap :deep(.form-label) {
  color: #f3f9fc;
  font-size: 1.15rem;
}

.login-theme-wrap :deep(.form-control) {
  border-color: rgba(255, 255, 255, 0.2);
  background: rgba(8, 15, 22, 0.52);
  color: #eef7fb;
}

.login-theme-wrap :deep(.form-control:focus) {
  border-color: rgba(255, 255, 255, 0.42);
  box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.12);
}

.login-theme-wrap :deep(.btn-success) {
  background-color: rgba(255, 255, 255, 0.12);
  border-color: rgba(255, 255, 255, 0.38);
  color: #ffffff;
}

.login-theme-wrap :deep(.btn-success:hover) {
  background-color: rgba(255, 255, 255, 0.2);
  border-color: rgba(255, 255, 255, 0.52);
  color: #ffffff;
}

.login-theme-wrap :deep(.btn-primary) {
  background-color: rgba(255, 255, 255, 0.06);
  border-color: rgba(255, 255, 255, 0.32);
  color: #ffffff;
}

.login-theme-wrap :deep(.btn-primary:hover) {
  background-color: rgba(255, 255, 255, 0.14);
  border-color: rgba(255, 255, 255, 0.46);
  color: #ffffff;
}

.login-theme-wrap :deep(.invalid-feedback) {
  color: #ffd3d3;
}
</style>
