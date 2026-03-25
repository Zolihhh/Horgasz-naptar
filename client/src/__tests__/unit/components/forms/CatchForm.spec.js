import { mount } from "@vue/test-utils";
import { describe, expect, it } from "vitest";
import CatchForm from "@/components/Forms/CatchForm.vue";

function createWrapper(props = {}) {
  return mount(CatchForm, {
    props: {
      newCatch: {
        catchLogId: null,
        specieId: null,
        lureId: null,
        weight: 2.5,
        length: 45,
        catchTime: "2026-03-25T08:30",
      },
      userCatchLogs: [{ id: 1, fishing_start: "2026-03-20", fishing_end: "2026-03-21" }],
      species: [{ id: 3, fish_name: "Ponty" }],
      lures: [{ id: 5, lure: "Wobbler" }],
      saving: false,
      getCatchLogLabel: (log) => `Napló #${log.id}`,
      ...props,
    },
  });
}

describe("CatchForm.vue komponens tesztek", () => {
  it("megjeleníti a select mezőket és az opciókat", () => {
    const wrapper = createWrapper();

    const selects = wrapper.findAll("select");
    expect(selects).toHaveLength(3);
    expect(wrapper.text()).toContain("Ponty");
    expect(wrapper.text()).toContain("Wobbler");
    expect(wrapper.text()).toContain("Napló #1");
  });

  it("submit eseményt küld űrlapküldéskor", async () => {
    const wrapper = createWrapper();

    await wrapper.find("form").trigger("submit.prevent");

    expect(wrapper.emitted("submit")).toBeTruthy();
    expect(wrapper.emitted("submit")).toHaveLength(1);
  });

  it("napló nélkül letiltja a mentést és figyelmeztetést mutat", () => {
    const wrapper = createWrapper({ userCatchLogs: [] });

    expect(wrapper.find('button[type="submit"]').element.disabled).toBe(true);
    expect(wrapper.text()).toContain("Előbb hozz létre fogási naplót");
  });

  it("mentés közben a gomb szövege változik", () => {
    const wrapper = createWrapper({ saving: true });

    expect(wrapper.find('button[type="submit"]').text()).toContain("Mentés...");
  });
});
