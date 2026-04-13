# Horgasz Naptar

Ez a projekt egy horgaszattal kapcsolatos naplozo webalkalmazas. A felhasznalo fogasi naplokat, fogasokat, halfajokat, csalikat es helyszineket tud kezelni, valamint kulon idojaras oldal is tartozik az alkalmazashoz.

## Fo funkciok

- regisztracio es bejelentkezes
- felhasznaloi szerepkorok kezelese
- fogasi naplok letrehozasa
- fogasok rogzitese
- halfajok, csalik es helyszinek kezelese
- idojaras oldal
- kepfeltoltes a backend oldalon

## Technologiak

- frontend: Vue 3, Vite, Pinia, Vue Router, Axios, Bootstrap
- backend: Laravel 12, PHP 8.2, Sanctum
- adatbazis: MySQL
- teszteles: Vitest, Cypress, PHPUnit

## Mit kell letolteni?

Ahhoz, hogy a kod es a weboldal fusson, ezekre lesz szukseg:

- Node.js `20.19+` vagy `22.12+`
- npm
- PHP `8.2+`
- Composer
- MySQL
- ajanlott helyi szerverkornyezet: XAMPP, WAMP vagy Laragon
- egy modern bongeszo

Megjegyzes: a frontend build nalunk lefutott, de a Vite figyelmeztetett, hogy a jelenlegi `Node.js 20.17.0` mar alacsonyabb a javasolt verzional. Emiatt erdemes `20.19+` verziot telepiteni.

## Projekt szerkezet

- `client/`: Vue frontend
- `server/`: Laravel backend
- `AdatbazisBackup.sql`: adatbazis mentes
- `Tesztek.md`: a tesztek leirasa
- projekt dokumentacio: a kulon dokumentacios Markdown fajl

## Telepites es futtatas

### 1. Backend inditasa

Lepj be a backend mappaba:

```bash
cd server
```

Telepitsd a PHP fuggosegeket:

```bash
composer install
```

Ha szukseges, hozd letre az `.env` fajlt:

```bash
cp .env.example .env
php artisan key:generate
```

Allitsd be az adatbazist a `server/.env` fajlban. A projektben MySQL van hasznalva, a tesztkonfigban a kovetkezo adatbazisnev szerepel:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=Fishing_Log
DB_USERNAME=root
DB_PASSWORD=
```

Ezutan ket lehetoseged van:

1. importalod az `AdatbazisBackup.sql` fajlt MySQL-be
2. vagy lefuttatod a migraciokat es seedereket, ha ugy szeretned feltolteni az adatbazist

Peldak:

```bash
php artisan migrate
php artisan db:seed
```

Majd inditsd el a Laravel szervert:

```bash
php artisan serve
```

Alapertelmezett backend cim:

```text
http://localhost:8000
http://localhost:8000/api
```

### 2. Frontend inditasa

Lepj be a frontend mappaba:

```bash
cd client
```

Telepitsd a JavaScript csomagokat:

```bash
npm install
```

Fejlesztoi modban a frontend API cime a `client/.env.development` fajlban:

```env
VITE_API_URL=http://localhost:8000/api
```

Inditsd el a fejlesztoi szervert:

```bash
npm run dev
```

Alapertelmezett frontend cim:

```text
http://localhost:5173
```

## Weboldal build

Ha a weboldalt buildelni szeretned:

```bash
cd client
npm run build
```

A jelenlegi `client/.env` fajl szerint a build ide kerul:

```env
VITE_BUILD_DIR=C:/wamp64/www/proba
VITE_WEB_DIR=/proba/
```

Ha nalad masik webszerver vagy mappa van, akkor ezeket az ertekeket modositsd a sajat gepedhez.

## Tesztek futtatasa

### Frontend unit tesztek

```bash
cd client
npm run test:unit -- --run
```

### Frontend e2e tesztek

```bash
cd client
npm run test:e2e
```

Az e2e teszt `http://localhost:4173` cimen fut.

### Backend tesztek

```bash
cd server
php artisan test
```

## Koddreszletek

### Frontend API beallitas

```js
const apiClient = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
  headers: {
    Accept: "application/json",
    "Content-Type": "application/json",
  },
});
```

Ez teszi lehetove, hogy a frontend a `.env` alapjan a megfelelo backend API-hoz kapcsolodjon.

### Vedett oldalak a frontend routerben

```js
{
  path: "/catches",
  name: "catches",
  component: () => import("@/views/CatchView.vue"),
  beforeEnter: [checkIfNotLogged],
  meta: {
    title: () => "Fogasok",
    breadcrumb: "Fogasok",
    roles: [1, 2],
  },
}
```

Itt latszik, hogy bizonyos oldalak csak bejelentkezett felhasznaloknak erhetoek el, es szerepkor alapjan is vedettek.

### Backend API route pelda

```php
Route::get('/species', [SpecieController::class, 'index']);
Route::get('/species/{id}', [SpecieController::class, 'show']);
Route::post('/species', [SpecieController::class, 'store'])
    ->middleware(['auth:sanctum', 'ability:species:post']);
```

Ez a resz mutatja, hogy a backend REST API-n keresztul kezeli a halfajok listazasat, lekereset es letrehozasat.

## Ellenorzott allapot

Az aktualis munkakornyezetben ezeket ellenoriztuk:

- a frontend unit tesztek lefutottak: `9` tesztfajl, `39` sikeres teszt
- a backend tesztek lefutottak: `63` sikeres teszt, `87` assertion
- a frontend build sikeresen lefutott

Reszletesebb tesztleiras a [Tesztek.md](Tesztek.md) fajlban van.
