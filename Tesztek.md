# Tesztek

### Frontend unit tesztek

Parancs:

```bash
cd client
npm run test:unit -- --run
```

Eredmeny:

- `9` tesztfajl
- `39` sikeres teszt
- minden frontend unit teszt atment

Ezek a tesztek a kovetkezoket ellenorzik:
- az `App.vue` alap rendereleset
- a header megjeleneset vagy elrejteset a route meta alapjan
- a bejelentkezo es regisztracios oldalak hibakezeleset
- az urlapkomponensek kuldeset, tiltott allapotat es gombszovegeit
- a Pinia store-ok CRUD es auth logikajat
- a szerepkor alapjan torteno jogosultsag-ellenorzest

### Backend tesztek
Parancs:
```bash
cd server
php artisan test
```

Eredmeny:
- `63` sikeres teszt
- `87` assertion
- minden backend teszt atment

Ezek a tesztek a kovetkezoket ellenorzik:
- az adatbazis tablak letezeset
- az oszlopok letezeset
- az oszloptipusok helyesseget
- a `lures` tabla egyedi megszoritasat
- csali modositasi es torlesi folyamatot
- alap feature valaszt, peldaul sikeres HTTP valaszt

### Frontend build ellenorzes
Parancs:
```bash
cd client
npm run build
```

Eredmeny:
- a build sikeresen lefutott
- a Vite figyelmeztetett, hogy ajanlott a `Node.js 20.19+`

## Meglevo tesztfajtak
### 1. Frontend unit tesztek

Fobb fajlok:
- `client/src/__tests__/App.spec.js`
- `client/src/__tests__/unit/views/LoginView.spec.js`
- `client/src/__tests__/unit/views/RegistrationView.spec.js`
- `client/src/__tests__/unit/components/forms/UserLogin.spec.js`
- `client/src/__tests__/unit/components/forms/CatchForm.spec.js`
- `client/src/__tests__/unit/components/forms/CatchLogForm.spec.js`
- `client/src/__tests__/unit/stores/useUserLoginLogoutStore.spec.js`
- `client/src/__tests__/unit/stores/useSpecieStore.spec.js`
- `client/src/__tests__/unit/stores/useCatchLogStore.spec.js`

Mit neznek ezek a tesztek:
- helyes komponens rendereles
- form submit es allapotkezeles
- login, logout, register folyamatok
- localStorage frissites
- szerverhibak kezelese
- store muveletek sikeres es hibas aga

### 2. Backend schema tesztek
Fobb fajlok:
- `server/tests/Unit/DatabaseTest.php`
- `server/tests/Unit/SpeciesTest.php`
- `server/tests/Unit/LocationsTest.php`
- `server/tests/Unit/CatchLogsTest.php`
- `server/tests/Unit/FishCatchesTest.php`

Mit neznek ezek a tesztek:
- `users`, `locations`, `species`, `lures`, `catch_logs`, `fish_catches` tablak leteznek-e
- a fontos oszlopok megvannak-e
- az oszlopok tipusa megfelel-e az elvart szerkezetnek

### 3. Backend funkcionalis tesztek
Fobb fajlok:
- `server/tests/Unit/LuresTest.php`
- `server/tests/Feature/ExampleTest.php`
- `server/tests/Feature/ProductApiTesssst.php`

Mit neznek ezek a tesztek:
- a `lure` mezo egyedi-e
- egy csali modositasa mukodik-e
- egy csali torlese mukodik-e
- az alkalmazas ad-e sikeres valaszt az alap keresen

### 4. Frontend e2e teszt
Fajl:
- `client/cypress/e2e/example.cy.js`

Mit nez:
- a weboldal gyokeroldalanak megnyitasat

Parancs:
```bash
cd client
npm run test:e2e
```
Megjegyzes: ezt a tesztet most nem futtattuk le, csak a unit teszteket es a buildet ellenoriztuk.

## Kodreszletek a tesztekbol
### Frontend komponens teszt pelda

```js
it("submit esemenyt kuld urlapkuldeskor", async () => {
  const wrapper = createWrapper();

  await wrapper.find("form").trigger("submit.prevent");

  expect(wrapper.emitted("submit")).toBeTruthy();
  expect(wrapper.emitted("submit")).toHaveLength(1);
});
```
Ez azt ellenorzi, hogy a fogas rogzitesehez tartozo urlap tenyleg kuld-e esemenyt.

### Frontend auth store teszt pelda

```js
it("login siker eseten elmenti a felhasznalot es a localStorage-t", async () => {
  const result = await store.login(loginData);

  expect(result).toBe(true);
  expect(store.token).toBe("abc123");
  expect(JSON.parse(localStorage.getItem("user_data")).email).toBe("pecas@example.com");
});
```
Ez a teszt azt ellenorzi, hogy sikeres login utan a user adatai tenyleg elmentodnek.

### Backend adatbazis teszt pelda

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
Ez a resz az adatbazis schema helyesseget ellenorzi.

### Backend funkcionalis teszt pelda

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
Ez azt ellenorzi, hogy egy csali torlese utan az adat tenyleg eltunik az adatbazisbol.
