import { beforeEach, describe, expect, it, vi } from "vitest";
import { mount } from "@vue/test-utils";
import { createPinia, setActivePinia } from "pinia";
import LoginView from "@/views/LoginView.vue";
import { useUserLoginLogoutStore } from "@/stores/userLoginLogoutStore";

describe("LoginView 422 hibakezelés", () => {
  beforeEach(() => {
    setActivePinia(createPinia());
    vi.clearAllMocks();
  });

  it("422-es regisztrációs hiba esetén átadja a mezőhibákat a formnak", async () => {
    const pinia = createPinia();
    setActivePinia(pinia);

    const store = useUserLoginLogoutStore();
    const error422 = {
      response: {
        status: 422,
        data: {
          message: "A megadott adatok érvénytelenek.",
          errors: {
            email: ["Ez az email már foglalt."],
            password: ["A jelszó legalább 4 karakter legyen."],
          },
        },
      },
    };

    vi.spyOn(store, "register").mockRejectedValue(error422);

    const wrapper = mount(LoginView, {
      global: {
        plugins: [pinia],
      },
    });

    const setServerErrorsSpy = vi.spyOn(
      wrapper.vm.$refs.userLoginForm,
      "setServerErrors",
    );
    const done = vi.fn();

    await wrapper.vm.registerHandler({
      user: {
        name: "Teszt Elek",
        email: "teszt@example.com",
        password: "12",
      },
      done,
    });

    expect(setServerErrorsSpy).toHaveBeenCalledWith(
      error422.response.data.errors,
      error422.response.data.message,
    );
    expect(done).toHaveBeenCalledWith(false);
  });
});
