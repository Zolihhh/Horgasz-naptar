import { beforeEach, describe, expect, it, vi } from "vitest";
import { createPinia, setActivePinia } from "pinia";
import { useCatchLogStore } from "@/stores/catchLogStore";
import service from "@/api/catchLogService";

vi.mock("@/api/catchLogService", () => ({
  default: {
    getAll: vi.fn(),
    create: vi.fn(),
    delete: vi.fn(),
  },
}));

describe("CatchLogStore műveletek", () => {
  beforeEach(() => {
    setActivePinia(createPinia());
    vi.clearAllMocks();
  });

  it("getAll sikeresen lekéri a fogási naplókat", async () => {
    const store = useCatchLogStore();
    const mockLogs = {
      data: [
        {
          id: 1,
          locationid: 3,
          fishing_start: "2026-03-20",
          fishing_end: "2026-03-21",
          comment: "Szeles idő",
        },
      ],
    };

    service.getAll.mockResolvedValue(mockLogs);

    await store.getAll();

    expect(service.getAll).toHaveBeenCalledTimes(1);
    expect(store.items).toHaveLength(1);
    expect(store.items[0].comment).toBe("Szeles idő");
    expect(store.getItemsLength).toBe(1);
    expect(store.loading).toBe(false);
  });

  it("create sikeresen létrehoz egy fogási naplót", async () => {
    const store = useCatchLogStore();
    const payload = {
      locationid: 2,
      fishing_start: "2026-03-22",
      fishing_end: "2026-03-23",
      comment: "Reggeli peca",
    };
    const mockResponse = {
      data: {
        id: 5,
        ...payload,
      },
    };

    service.create.mockResolvedValue(mockResponse);

    const result = await store.create(payload);

    expect(service.create).toHaveBeenCalledWith(payload);
    expect(result).toEqual(mockResponse);
    expect(store.item.id).toBe(5);
    expect(store.item.comment).toBe("Reggeli peca");
    expect(store.loading).toBe(false);
  });

  it("delete sikeresen töröl egy fogási naplót", async () => {
    const store = useCatchLogStore();
    const mockResponse = {
      data: {
        message: "A napló törölve lett.",
      },
    };

    service.delete.mockResolvedValue(mockResponse);

    const result = await store.delete(9);

    expect(service.delete).toHaveBeenCalledWith(9);
    expect(result).toEqual(mockResponse);
    expect(store.item.message).toBe("A napló törölve lett.");
    expect(store.loading).toBe(false);
  });
});

describe("CatchLogStore hibaág tesztek", () => {
  beforeEach(() => {
    setActivePinia(createPinia());
    vi.clearAllMocks();
  });

  it("getAll hiba esetén elmenti a hibát és továbbdobja", async () => {
    const store = useCatchLogStore();
    const error = new Error("A fogási naplók betöltése nem sikerült.");

    service.getAll.mockRejectedValue(error);

    await expect(store.getAll()).rejects.toThrow("A fogási naplók betöltése nem sikerült.");

    expect(store.error).toBe(error);
    expect(store.loading).toBe(false);
  });

  it("create hiba esetén elmenti a hibát és loading false lesz", async () => {
    const store = useCatchLogStore();
    const error = {
      response: {
        status: 422,
        data: {
          message: "A megadott adatok érvénytelenek.",
        },
      },
    };

    service.create.mockRejectedValue(error);

    await expect(
      store.create({
        locationid: null,
        fishing_start: "",
        fishing_end: "",
        comment: "",
      }),
    ).rejects.toEqual(error);

    expect(store.error).toStrictEqual(error);
    expect(store.loading).toBe(false);
  });

  it("delete hiba esetén elmenti a hibát és loading false lesz", async () => {
    const store = useCatchLogStore();
    const error = {
      response: {
        status: 500,
        data: {
          message: "A törlés nem sikerült.",
        },
      },
    };

    service.delete.mockRejectedValue(error);

    await expect(store.delete(3)).rejects.toEqual(error);

    expect(store.error).toStrictEqual(error);
    expect(store.loading).toBe(false);
  });
});
