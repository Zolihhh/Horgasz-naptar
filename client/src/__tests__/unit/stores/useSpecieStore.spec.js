import { beforeEach, describe, expect, it, vi } from "vitest";
import { createPinia, setActivePinia } from "pinia";
import { useSpecieStore } from "@/stores/specieStore";
import service from "@/api/specieService";

vi.mock("@/api/specieService", () => ({
  default: {
    getAll: vi.fn(),
    getById: vi.fn(),
    create: vi.fn(),
    update: vi.fn(),
    delete: vi.fn(),
  },
}));

describe("SpecieStore CRUD műveletek", () => {
  beforeEach(() => {
    setActivePinia(createPinia());
    vi.clearAllMocks();
  });

  it("clearItem visszaállítja az alapértelmezett halfajt", () => {
    const store = useSpecieStore();
    store.item = {
      id: 5,
      fish_name: "Ponty",
      photo: "ponty.jpg",
      description: "Teszt leírás",
    };

    store.clearItem();

    expect(store.item.id).toBe(0);
    expect(store.item.fish_name).toBe("");
    expect(store.item.photo).toBe("");
    expect(store.item.description).toBe("");
  });

  it("getAll sikeresen lekéri a halfajokat", async () => {
    const store = useSpecieStore();
    const mockSpecies = {
      data: [{ id: 1, fish_name: "Ponty", photo: "ponty.jpg", description: "Leírás" }],
    };
    service.getAll.mockResolvedValue(mockSpecies);

    await store.getAll();

    expect(service.getAll).toHaveBeenCalledTimes(1);
    expect(store.items).toHaveLength(1);
    expect(store.items[0].fish_name).toBe("Ponty");
    expect(store.loading).toBe(false);
  });

  it("getById betölt egy konkrét halfajt", async () => {
    const store = useSpecieStore();
    const mockSpecie = {
      data: { id: 4, fish_name: "Süllő", photo: "sullo.jpg", description: "Ragadozó" },
    };
    service.getById.mockResolvedValue(mockSpecie);

    await store.getById(4);

    expect(service.getById).toHaveBeenCalledWith(4);
    expect(store.item.fish_name).toBe("Süllő");
    expect(store.loading).toBe(false);
  });

  it("create létrehoz egy halfajt és frissíti a listát", async () => {
    const store = useSpecieStore();
    const newSpecie = {
      fish_name: "Csuka",
      photo: "csuka.jpg",
      description: "Új halfaj",
    };

    service.create.mockResolvedValue({ data: { id: 2, ...newSpecie } });
    service.getAll.mockResolvedValue({ data: [{ id: 2, ...newSpecie }] });

    const result = await store.create(newSpecie);

    expect(result).toBe(true);
    expect(service.create).toHaveBeenCalledWith(newSpecie);
    expect(service.getAll).toHaveBeenCalledTimes(1);
    expect(store.items[0].fish_name).toBe("Csuka");
  });

  it("update módosítja a halfajt és újratölti a listát", async () => {
    const store = useSpecieStore();
    const updatedData = {
      fish_name: "Amur",
      photo: "amur.jpg",
      description: "Módosított leírás",
    };

    service.update.mockResolvedValue({ data: { id: 3, ...updatedData } });
    service.getAll.mockResolvedValue({ data: [{ id: 3, ...updatedData }] });

    const result = await store.update(3, updatedData);

    expect(result).toBe(true);
    expect(service.update).toHaveBeenCalledWith(3, updatedData);
    expect(store.items[0].fish_name).toBe("Amur");
  });

  it("delete törli a halfajt és újratölti a listát", async () => {
    const store = useSpecieStore();

    service.delete.mockResolvedValue({});
    service.getAll.mockResolvedValue({ data: [] });

    const result = await store.delete(7);

    expect(result).toBe(true);
    expect(service.delete).toHaveBeenCalledWith(7);
    expect(store.items).toHaveLength(0);
  });
});

describe("SpecieStore hibaág tesztek", () => {
  beforeEach(() => {
    setActivePinia(createPinia());
    vi.clearAllMocks();
  });

  it("getAll hiba esetén elmenti a hibát és továbbdobja", async () => {
    const store = useSpecieStore();
    const error = new Error("Lekérdezési hiba");
    service.getAll.mockRejectedValue(error);

    await expect(store.getAll()).rejects.toThrow("Lekérdezési hiba");

    expect(store.error).toBe(error);
    expect(store.loading).toBe(false);
  });

  it("create hiba esetén elmenti a hibát és loading false lesz", async () => {
    const store = useSpecieStore();
    const error422 = {
      response: {
        status: 422,
        data: {
          message: "A megadott adatok érvénytelenek.",
          errors: {
            fish_name: ["Ez a halfaj már létezik."],
          },
        },
      },
    };

    service.create.mockRejectedValue(error422);

    await expect(
      store.create({
        fish_name: "Ponty",
        photo: "ponty.jpg",
        description: "",
      }),
    ).rejects.toEqual(error422);

    expect(store.error).toStrictEqual(error422);
    expect(store.loading).toBe(false);
  });

  it("delete hiba esetén elmenti a hibát és loading false lesz", async () => {
    const store = useSpecieStore();
    const error = {
      response: {
        status: 500,
        data: {
          message: "A halfaj törlése nem sikerült.",
        },
      },
    };

    service.delete.mockRejectedValue(error);

    await expect(store.delete(1)).rejects.toEqual(error);

    expect(store.error).toStrictEqual(error);
    expect(store.loading).toBe(false);
  });
});
