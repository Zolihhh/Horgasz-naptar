import { beforeEach, describe, expect, it, vi } from "vitest";
import { mount } from "@vue/test-utils";
import { createPinia, setActivePinia } from "pinia";
import RegistrationView from "@/views/RegistrationView.vue";
import { useUserStore } from "@/stores/userStore";

describe("RegistrationView 422 hibakezelés", () => {
  beforeEach(() => {
    setActivePinia(createPinia());
    vi.clearAllMocks();
    vi.spyOn(console, "log").mockImplementation(() => {});
  });

  it("422-es hiba esetén átadja a szerverhibákat a regisztrációs formnak", async () => {
    const pinia = createPinia();
    setActivePinia(pinia);

    const store = useUserStore();
    const error422 = {
      response: {
        status: 422,
        data: {
          errors: {
            name: ["A név mező kötelező."],
            email: ["Ez az email már foglalt."],
          },
        },
      },
    };

    vi.spyOn(store, "createUser").mockRejectedValue(error422);

    const wrapper = mount(RegistrationView, {
      global: {
        plugins: [pinia],
        stubs: {
          ToastContainer: true,
        },
      },
    });

    const setServerErrorsSpy = vi.spyOn(wrapper.vm.$refs.form, "setServerErrors");
    const done = vi.fn();

    await wrapper.vm.handlerCreateUser({
      data: {
        name: "",
        email: "foglalt@example.com",
        password: "1234",
      },
      done,
    });

    expect(setServerErrorsSpy).toHaveBeenCalledWith(error422.response.data.errors);
    expect(done).toHaveBeenCalledWith(false);
  });
});
