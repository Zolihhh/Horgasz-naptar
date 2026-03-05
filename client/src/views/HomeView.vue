<template>
  <section class="home-wrap home-view">
    <header class="hero">
      <p class="eyebrow">Horgász Naptár</p>
      <h1>Átlátható kezelés a fogásokhoz és az időjáráshoz</h1>
      <p class="hero-text">
        Egy felületen követheted a fogási adatokat, helyszíneket és a következő napok várható időjárását.
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
      <p v-else class="feature-empty">Nincs megjeleníthető halkép.</p>
    </section>
  </section>
</template>

<script>
import { Carousel } from "bootstrap";
import { useSpecieStore } from "@/stores/specieStore";

const API_BASE = (import.meta.env.VITE_API_URL || "").replace(/\/api\/?$/, "");

export default {
  name: "HomeView",
  data() {
    return {
      featuredSpecies: [],
      specieStore: useSpecieStore(),
      carouselInstance: null,
    };
  },
  computed: {
    speciesPages() {
      const pageSize = 4;
      const pages = [];
      for (let i = 0; i < this.featuredSpecies.length; i += pageSize) {
        pages.push(this.featuredSpecies.slice(i, i + pageSize));
      }
      return pages;
    },
  },
  async mounted() {
    await this.fetchFeaturedSpecies();
    this.$nextTick(() => {
      this.initCarousel();
    });
  },
  beforeUnmount() {
    if (this.carouselInstance) {
      this.carouselInstance.dispose();
      this.carouselInstance = null;
    }
  },
  methods: {
    initCarousel() {
      const el = document.getElementById("fishCardsCarousel");
      if (!el) return;

      if (this.carouselInstance) {
        this.carouselInstance.dispose();
      }

      this.carouselInstance = new Carousel(el, {
        interval: 5000,
        pause: false,
        ride: "carousel",
        wrap: true,
      });

      this.carouselInstance.cycle();
    },
    async fetchFeaturedSpecies() {
      try {
        await this.specieStore.getAll();
        const species = Array.isArray(this.specieStore.items) ? this.specieStore.items : [];
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
      if (key.includes("ponty")) return "Népszerű békés hal, melegebb vizekben aktív.";
      if (key.includes("csuka")) return "Ragadozó hal, gyakran a parti növényzetnél áll.";
      if (key.includes("harcsa")) return "Nagy testű ragadozó, inkább este mozog.";
      if (key.includes("süllő")) return "Érzékeny ragadozó, tisztább vízben eredményes.";
      if (key.includes("balin")) return "Gyors ragadozó, felszín közelében támad.";
      return "Magyar vizekben gyakran fogott, értékes halfaj.";
    },
  },
};
</script>
