import { mount } from "@vue/test-utils";
import { describe, expect, it } from "vitest";
import { nextTick } from "vue";
import UserLogin from "@/components/Forms/UserLogin.vue";

describe("UserLogin.vue komponens tesztek", () => {
  it("alapból a bejelentkezési nézetet jeleníti meg", () => {
    const wrapper = mount(UserLogin);

    expect(wrapper.find(".login-title").text()).toBe("Bejelentkezés");
    expect(wrapper.find("#name").exists()).toBe(false);
    expect(wrapper.text()).toContain("Belépés");
  });

  it("érvényes adatokkal logIn eseményt küld", async () => {
    const wrapper = mount(UserLogin);

    await wrapper.find("#email").setValue("  pecas@example.com  ");
    await wrapper.find('input[type="password"]').setValue("titok");
    await wrapper.find("form").trigger("submit.prevent");

    expect(wrapper.emitted("logIn")).toBeTruthy();
    expect(wrapper.emitted("logIn")[0][0]).toEqual({
      email: "pecas@example.com",
      password: "titok",
    });
  });

  it("regisztráció módban register eseményt küld, siker után visszaáll bejelentkezésre", async () => {
    const wrapper = mount(UserLogin);

    await wrapper.findAll(".action-btn")[1].trigger("click");
    await wrapper.find("#name").setValue("Peca Béla");
    await wrapper.find("#email").setValue("bela@example.com");
    await wrapper.find('input[type="password"]').setValue("1234");
    await wrapper.find("form").trigger("submit.prevent");

    expect(wrapper.emitted("register")).toBeTruthy();

    const payload = wrapper.emitted("register")[0][0];
    expect(payload.user).toEqual({
      name: "Peca Béla",
      email: "bela@example.com",
      password: "1234",
    });

    payload.done(true);
    await nextTick();

    expect(wrapper.find(".login-title").text()).toBe("Bejelentkezés");
    expect(wrapper.find("#name").exists()).toBe(false);
    expect(wrapper.find("#email").element.value).toBe("");
  });

  it("gépeléskor törli a szerveroldali email hibát", async () => {
    const wrapper = mount(UserLogin);

    wrapper.vm.setServerErrors({ email: ["Ez az email már foglalt."] }, "Hiba");
    await nextTick();

    expect(wrapper.text()).toContain("Ez az email már foglalt.");

    await wrapper.find("#email").setValue("uj@email.hu");

    expect(wrapper.vm.serverErrors.email).toBeUndefined();
    expect(wrapper.vm.serverMessage).toBe("");
  });
});
