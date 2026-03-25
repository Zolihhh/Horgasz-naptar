import { beforeEach, describe, expect, it, vi } from "vitest";
import { createPinia, setActivePinia } from "pinia";
import { useUserLoginLogoutStore } from "@/stores/userLoginLogoutStore";
import { useToastStore } from "@/stores/toastStore";
import service from "@/api/userLoginLogoutService";

vi.mock("@/api/userLoginLogoutService", () => ({
  default: {
    login: vi.fn(),
    register: vi.fn(),
    logout: vi.fn(),
    getMeRefresh: vi.fn(),
    updateMe: vi.fn(),
    updatePassword: vi.fn(),
  },
}));

describe("UserLoginLogoutStore műveletek", () => {
  beforeEach(() => {
    localStorage.clear();
    setActivePinia(createPinia());
    vi.clearAllMocks();
  });

  it("login siker esetén elmenti a felhasználót és a localStorage-t", async () => {
    const store = useUserLoginLogoutStore();
    const loginData = {
      email: "pecas@example.com",
      password: "titok",
    };
    const mockUser = {
      data: {
        id: 1,
        name: "Peca Béla",
        email: "pecas@example.com",
        role: 1,
        token: "abc123",
      },
    };

    service.login.mockResolvedValue(mockUser);

    const result = await store.login(loginData);

    expect(result).toBe(true);
    expect(service.login).toHaveBeenCalledWith(loginData);
    expect(store.item.name).toBe("Peca Béla");
    expect(store.token).toBe("abc123");
    expect(store.userNameWithRole).toBe("Peca Béla: Admin");
    expect(JSON.parse(localStorage.getItem("user_data")).email).toBe("pecas@example.com");
    expect(store.loading).toBe(false);
  });

  it("register siker esetén toastot mutat", async () => {
    const store = useUserLoginLogoutStore();
    const toastStore = useToastStore();
    const toastSpy = vi.spyOn(toastStore, "show");

    service.register.mockResolvedValue({ data: { ok: true } });

    const result = await store.register({
      name: "Peca Béla",
      email: "pecas@example.com",
      password: "1234",
    });

    expect(result).toBe(true);
    expect(service.register).toHaveBeenCalledTimes(1);
    expect(toastStore.messages).toContain("Sikeres regisztráció");
    expect(toastSpy).toHaveBeenCalledWith("Success");
    expect(store.loading).toBe(false);
  });

  it("logout siker esetén törli a felhasználót és toastot mutat", async () => {
    const store = useUserLoginLogoutStore();
    const toastStore = useToastStore();
    const toastSpy = vi.spyOn(toastStore, "show");

    localStorage.setItem(
      "user_data",
      JSON.stringify({
        id: 1,
        name: "Peca Béla",
        email: "pecas@example.com",
        role: 2,
        token: "tok123",
      }),
    );
    store.item = JSON.parse(localStorage.getItem("user_data"));

    service.logout.mockResolvedValue({ data: { ok: true } });

    const result = await store.logout();

    expect(result).toBe(true);
    expect(service.logout).toHaveBeenCalledTimes(1);
    expect(store.item).toBe(null);
    expect(localStorage.getItem("user_data")).toBe(null);
    expect(toastStore.messages).toContain("Sikeres kijelentkezés");
    expect(toastSpy).toHaveBeenCalledWith("Success");
    expect(store.loading).toBe(false);
  });

  it("getMeRefresh frissíti a tárolt felhasználó adatait", async () => {
    const store = useUserLoginLogoutStore();
    store.item = {
      id: 1,
      name: "Régi Név",
      email: "regi@example.com",
      role: 2,
      token: "tok123",
    };

    service.getMeRefresh.mockResolvedValue({
      data: {
        data: {
          name: "Új Név",
          email: "uj@example.com",
        },
      },
    });

    const result = await store.getMeRefresh();

    expect(result).toBe(true);
    expect(service.getMeRefresh).toHaveBeenCalledTimes(1);
    expect(store.item.name).toBe("Új Név");
    expect(store.item.email).toBe("uj@example.com");
    expect(JSON.parse(localStorage.getItem("user_data")).name).toBe("Új Név");
    expect(store.loading).toBe(false);
  });

  it("canAccess helyesen ellenőrzi a szerepköröket", () => {
    const store = useUserLoginLogoutStore();

    expect(store.canAccess()).toBe(true);
    expect(store.canAccess([1, 2])).toBe(false);

    store.item = {
      id: 1,
      name: "Peca Béla",
      email: "pecas@example.com",
      role: 2,
      token: "tok123",
    };

    expect(store.canAccess([1])).toBe(false);
    expect(store.canAccess([2])).toBe(true);
  });
});

describe("UserLoginLogoutStore hibaág tesztek", () => {
  beforeEach(() => {
    localStorage.clear();
    setActivePinia(createPinia());
    vi.clearAllMocks();
  });

  it("login hiba esetén elmenti a hibát és továbbdobja", async () => {
    const store = useUserLoginLogoutStore();
    const error = {
      response: {
        status: 401,
        data: { message: "Hibás belépési adatok." },
      },
    };

    service.login.mockRejectedValue(error);

    await expect(
      store.login({ email: "rossz@example.com", password: "hiba" }),
    ).rejects.toEqual(error);

    expect(store.error).toStrictEqual(error);
    expect(store.loading).toBe(false);
  });

  it("updatePassword hiba esetén elmenti a hibát és loading false lesz", async () => {
    const store = useUserLoginLogoutStore();
    const error = {
      response: {
        status: 422,
        data: { message: "A jelszó túl rövid." },
      },
    };

    service.updatePassword.mockRejectedValue(error);

    await expect(
      store.updatePassword({ current_password: "123", password: "12" }),
    ).rejects.toEqual(error);

    expect(store.error).toStrictEqual(error);
    expect(store.loading).toBe(false);
  });
});
