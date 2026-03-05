import { createRouter, createWebHistory } from "vue-router";
import HomeView from "@/views/HomeView.vue";
import { useUserLoginLogoutStore } from "@/stores/userLoginLogoutStore";
import { useToastStore } from "@/stores/toastStore";

function checkIfNotLogged() {
  const storeAuth = useUserLoginLogoutStore();
  if (!storeAuth.isLoggedIn) {
    return "/login";
  }
}

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/",
      name: "home",
      component: HomeView,
      meta: {
        title: () => "Főoldal",
        breadcrumb: "Főoldal",
      },
    },
    {
      path: "/about",
      name: "about",
      component: () => import("@/views/AboutView.vue"),
      meta: {
        title: () => "Rólunk",
        breadcrumb: "Rólunk",
      },
    },
    {
      path: "/weather",
      name: "weather",
      component: () => import("@/views/WeatherView.vue"),
      meta: {
        title: () => "Időjárás",
        breadcrumb: "Időjárás",
      },
    },
    {
      path: "/catches",
      name: "catches",
      component: () => import("@/views/CatchView.vue"),
      beforeEnter: [checkIfNotLogged],
      meta: {
        title: () => "Fogások",
        breadcrumb: "Fogások",
        roles: [1, 2],
      },
    },
    {
      path: "/profile",
      name: "profile",
      component: () => import("@/views/ProfileView.vue"),
      beforeEnter: [checkIfNotLogged],
      meta: {
        title: () => "Profil",
        breadcrumb: "Profil",
        roles: [1, 2],
      },
    },
    {
      path: "/users",
      name: "users",
      component: () => import("@/views/UsersView.vue"),
      beforeEnter: [checkIfNotLogged],
      meta: {
        title: () => "Felhasználók",
        breadcrumb: "Felhasználók",
        roles: [1],
      },
    },
    {
      path: "/login",
      name: "login",
      component: () => import("@/views/LoginView.vue"),
      meta: {
        title: () => "Bejelentkezés",
        breadcrumb: "Bejelentkezés",
      },
    },
    {
      path: "/registration",
      name: "registration",
      component: () => import("@/views/RegistrationView.vue"),
      meta: {
        title: () => "Regisztráció",
        breadcrumb: "Regisztráció",
      },
    },
    {
      path: "/:pathMatch(.*)*",
      name: "NotFound",
      component: () => import("@/views/404.vue"),
      meta: {
        title: () => "404",
        breadcrumb: "",
      },
    },
  ],
});

const DYNAMIC_IMPORT_RELOAD_KEY = "dynamic-import-reload-path";

router.beforeEach((to, from, next) => {
  const titleResolver = to.meta?.title;
  document.title = `Horgász Naptár - ${typeof titleResolver === "function" ? titleResolver(to) : "Oldal"}`;

  const requiredRoles = to.meta?.roles;
  const userStore = useUserLoginLogoutStore();

  if (userStore.canAccess(requiredRoles)) {
    next();
  } else {
    if (!userStore.isLoggedIn) {
      next({ path: "/login" });
    } else {
      const toastStore = useToastStore();
      toastStore.messages.push("Ehhez az oldalhoz nincs jogod!");
      toastStore.show("Error");
      next("/");
    }
  }
});

router.afterEach(() => {
  sessionStorage.removeItem(DYNAMIC_IMPORT_RELOAD_KEY);
});

router.onError((error, to) => {
  const message = String(error?.message || "");
  const isDynamicImportError =
    message.includes("Failed to fetch dynamically imported module") ||
    message.includes("Importing a module script failed");

  if (!isDynamicImportError) {
    return;
  }

  const targetPath = to?.fullPath || window.location.pathname;
  const alreadyRetriedPath = sessionStorage.getItem(DYNAMIC_IMPORT_RELOAD_KEY);

  if (alreadyRetriedPath === targetPath) {
    return;
  }

  sessionStorage.setItem(DYNAMIC_IMPORT_RELOAD_KEY, targetPath);
  window.location.assign(targetPath);
});

export default router;
