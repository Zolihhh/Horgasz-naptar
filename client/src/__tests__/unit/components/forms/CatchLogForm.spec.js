import { mount } from "@vue/test-utils";
import { describe, expect, it } from "vitest";
import CatchLogForm from "@/components/Forms/CatchLogForm.vue";

function createWrapper(props = {}) {
  return mount(CatchLogForm, {
    props: {
      newCatchLog: {
        locationid: null,
        fishing_start: "2026-03-20",
        fishing_end: "2026-03-21",
        comment: "",
      },
      locationSearchTerm: "",
      filteredLocations: [
        { id: 1, location_name: "Tisza-tó" },
        { id: 2, location_name: "Balaton" },
      ],
      savingLog: false,
      getLocationName: (id) => (id === 1 ? "Tisza-tó" : "Balaton"),
      ...props,
    },
  });
}

describe("CatchLogForm.vue komponens tesztek", () => {
  it("megjeleníti a helyszíneket és az alap mezőket", () => {
    const wrapper = createWrapper();

    expect(wrapper.text()).toContain("Víz keresése");
    expect(wrapper.findAll("select option")).toHaveLength(3);
    expect(wrapper.find('input[type="date"]').exists()).toBe(true);
  });

  it("frissíti a keresési kifejezést eseményen keresztül", async () => {
    const wrapper = createWrapper();

    await wrapper.find('input[placeholder="Víz névre szűrés..."]').setValue("Tisza");

    expect(wrapper.emitted("update:locationSearchTerm")).toBeTruthy();
    expect(wrapper.emitted("update:locationSearchTerm")[0]).toEqual(["Tisza"]);
  });

  it("submit eseményt küld űrlapküldéskor", async () => {
    const wrapper = createWrapper();

    await wrapper.find("form").trigger("submit.prevent");

    expect(wrapper.emitted("submit")).toBeTruthy();
    expect(wrapper.emitted("submit")).toHaveLength(1);
  });

  it("mentés közben letiltja a gombot", () => {
    const wrapper = createWrapper({ savingLog: true });

    expect(wrapper.find('button[type="submit"]').element.disabled).toBe(true);
    expect(wrapper.text()).toContain("Mentés...");
  });
});
