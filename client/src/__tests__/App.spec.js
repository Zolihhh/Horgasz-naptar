import { describe, it, expect } from "vitest";
import { flushPromises, mount } from "@vue/test-utils";
import { createPinia } from "pinia";
import { createMemoryHistory, createRouter } from "vue-router";
import App from "../App.vue";

async function mountAppAt(path) {
  const router = createRouter({
    history: createMemoryHistory(),
    routes: [
      {
        path: "/",
        component: { template: "<div>Főoldal tartalom</div>" },
        meta: { title: () => "Főoldal" },
      },
      {
        path: "/weather",
        component: { template: "<div>Időjárás tartalom</div>" },
        meta: { title: () => "Időjárás" },
      },
      {
        path: "/about",
        component: { template: "<div>Rólunk tartalom</div>" },
        meta: { title: () => "Rólunk", hideHeader: true },
      },
      {
        path: "/catches",
        component: { template: "<div>Fogások</div>" },
        meta: { title: () => "Fogások" },
      },
      {
        path: "/login",
        component: { template: "<div>Bejelentkezés</div>" },
        meta: { title: () => "Bejelentkezés", hideHeader: true },
      },
    ],
  });

  router.push(path);
  await router.isReady();

  const wrapper = mount(App, {
    global: {
      plugins: [router, createPinia()],
    },
  });

  await flushPromises();
  return wrapper;
}

describe("App", () => {
  it("renderelődik a szükséges függőségekkel", async () => {
    const wrapper = await mountAppAt("/");

    expect(wrapper.exists()).toBe(true);
    expect(wrapper.text()).toContain("Főoldal");
  });

  it("a fejléc megjelenik a normál oldalakon", async () => {
    const wrapper = await mountAppAt("/weather");

    expect(wrapper.find(".header-bar").exists()).toBe(true);
    expect(wrapper.find(".header-title").text()).toContain("Időjárás");
  });

  it("nem jeleníti meg a fejlécet a hideHeader meta esetén", async () => {
    const wrapper = await mountAppAt("/about");

    expect(wrapper.find(".header-bar").exists()).toBe(false);
  });
});
