# Tesztek
Ez a fájl összefoglalja, hogy milyen teszteket használtunk a projektben, és mit ellenőriznek.

## Lefuttatott tesztek
### Frontend unit tesztek
Parancs:
```bash
cd client
npm run test:unit -- --run
```

Eredmény:
- `9` tesztfájl
- `39` sikeres teszt
- minden frontend unit teszt átment

Ezek a tesztek a következőket ellenőrzik:
- az `App.vue` alap renderelését
- a header megjelenését vagy elrejtését a route meta alapján
- a bejelentkező és regisztrációs oldalak hibakezelését
- az űrlapkomponensek küldését, tiltott állapotát és gombszövegeit
- a Pinia store-ok CRUD és auth logikáját
- a szerepkör alapján történő jogosultság-ellenőrzést

### Backend tesztek
Parancs:
```bash
cd server
php artisan test
```

Eredmény:
- `63` sikeres teszt
- `87` assertion
- minden backend teszt átment
Ezek a tesztek a következőket ellenőrzik:
- az adatbázis táblák létezését
- az oszlopok létezését
- az oszloptípusok helyességét
- a `lures` tábla egyedi megszorítását
- csali módosítási és törlési folyamatot
- alap feature választ, például sikeres HTTP választ

### Frontend build ellenőrzés
Parancs:
```bash
cd client
npm run build
```

Eredmény:
- a build sikeresen lefutott
- a Vite figyelmeztetett, hogy ajánlott a `Node.js 20.19+`

## Meglévő tesztfajták
### 1. Frontend unit tesztek
Főbb fájlok:
- `client/src/__tests__/App.spec.js`
- `client/src/__tests__/unit/views/LoginView.spec.js`
- `client/src/__tests__/unit/views/RegistrationView.spec.js`
- `client/src/__tests__/unit/components/forms/UserLogin.spec.js`
- `client/src/__tests__/unit/components/forms/CatchForm.spec.js`
- `client/src/__tests__/unit/components/forms/CatchLogForm.spec.js`
- `client/src/__tests__/unit/stores/useUserLoginLogoutStore.spec.js`
- `client/src/__tests__/unit/stores/useSpecieStore.spec.js`
- `client/src/__tests__/unit/stores/useCatchLogStore.spec.js`

Mit néznek ezek a tesztek:
- helyes komponens renderelés
- form submit és állapotkezelés
- login, logout, register folyamatok
- localStorage frissítés
- szerverhibák kezelése
- store műveletek sikeres és hibás ága

### 2. Backend schema tesztek
Főbb fájlok:
- `server/tests/Unit/DatabaseTest.php`
- `server/tests/Unit/SpeciesTest.php`
- `server/tests/Unit/LocationsTest.php`
- `server/tests/Unit/CatchLogsTest.php`
- `server/tests/Unit/FishCatchesTest.php`

Mit néznek ezek a tesztek:
- a `users`, `locations`, `species`, `lures`, `catch_logs`, `fish_catches` táblák léteznek-e
- a fontos oszlopok megvannak-e
- az oszlopok típusa megfelel-e az elvárt szerkezetnek

### 3. Backend funkcionális tesztek
Főbb fájlok:
- `server/tests/Unit/LuresTest.php`
- `server/tests/Feature/ExampleTest.php`
- `server/tests/Feature/ProductApiTesssst.php`

Mit néznek ezek a tesztek:
- a `lure` mező egyedi-e
- egy csali módosítása működik-e
- egy csali törlése működik-e
- az alkalmazás ad-e sikeres választ az alap kérésen

### 4. Frontend e2e teszt
Fájl:
- `client/cypress/e2e/example.cy.js`

Mit néz:
- a weboldal gyökéroldalának megnyitását

Parancs:
```bash
cd client
npm run test:e2e
```
Megjegyzés: ezt a tesztet most nem futtattuk le, csak a unit teszteket és a buildet ellenőriztük.

## Kódrészletek a tesztekből
### Frontend komponens teszt példa

```js
it("submit eseményt küld űrlapküldéskor", async () => {
  const wrapper = createWrapper();

  await wrapper.find("form").trigger("submit.prevent");

  expect(wrapper.emitted("submit")).toBeTruthy();
  expect(wrapper.emitted("submit")).toHaveLength(1);
});
```
Ez azt ellenőrzi, hogy a fogás rögzítéséhez tartozó űrlap tényleg küld-e eseményt.

### Frontend auth store teszt példa

```js
it("login siker esetén elmenti a felhasználót és a localStorage-t", async () => {
  const result = await store.login(loginData);

  expect(result).toBe(true);
  expect(store.token).toBe("abc123");
  expect(JSON.parse(localStorage.getItem("user_data")).email).toBe("pecas@example.com");
});
```
Ez a teszt azt ellenőrzi, hogy sikeres login után a user adatai tényleg elmentődnek.
 
### Backend adatbázis teszt példa

```php
#[DataProvider('speciesColumnsProvider')]
public function test_species_columns_types(
    string $table,
    string $column,
    string $expectedType
): void {
    $this->assertTrue(Schema::hasColumn($table, $column));
    $type = $this->normalizeType(Schema::getColumnType($table, $column));
    $this->assertEquals($expectedType, $type);
}
```
Ez a rész az adatbázis schema helyességét ellenőrzi.

### Backend funkcionális teszt példa

```php
public function test_it_can_delete_a_lure(): void
{
    $lure = Lure::create(['lure' => 'DeleteMe']);
    $lure->delete();

    $this->assertDatabaseMissing($this->table, [
        'id' => $lure->id,
    ]);
}
```
Ez azt ellenőrzi, hogy egy csali törlése után az adat tényleg eltűnik az adatbázisból.
