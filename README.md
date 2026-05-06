# Horgász Naptár
Ez a projekt egy horgászattal kapcsolatos naplózó webalkalmazás. A felhasználó fogási naplókat, fogásokat, halfajokat, csalikat és helyszíneket tud kezelni, valamint külön időjárás oldal is tartozik az alkalmazáshoz.

## Fő funkciók
- regisztráció és bejelentkezés
- felhasználói szerepkörök kezelése
- fogási naplók létrehozása
- fogások rögzítése
- halfajok, csalik és helyszínek kezelése
- időjárás oldal
- képfeltöltés a backend oldalon

## Technológiák
- frontend: Vue 3, Vite, Pinia, Vue Router, Axios, Bootstrap
- backend: Laravel 12, PHP 8.2, Sanctum
- adatbázis: MySQL
- tesztelés: Vitest, Cypress, PHPUnit

## Mit kell letölteni?
Ahhoz, hogy a kód és a weboldal fusson, ezekre lesz szükség:
- Node.js `20.19+` vagy `22.12+`
- npm
- PHP `8.2+`
- Composer
- MySQL
- ajánlott helyi szerverkörnyezet: XAMPP, WAMP vagy Laragon
- egy modern böngésző
Megjegyzés: a frontend build nálunk lefutott, de a Vite figyelmeztetett, hogy a jelenlegi `Node.js 20.17.0` már alacsonyabb a javasolt verziónál. Emiatt érdemes `20.19+` verziót telepíteni.

## Projekt szerkezet
- `client/`: Vue frontend
- `server/`: Laravel backend
- `AdatbazisBackup.sql`: adatbázis mentés
- `Tesztek.md`: a tesztek leírása
- projekt dokumentáció: a külön dokumentációs Markdown fájl

## Telepítés és futtatás
### 1. Backend indítása
Lépj be a backend mappába:
```bash
cd server
```

Telepítsd a PHP függőségeket:
```bash
composer install
```

Ha szükséges, hozd létre az `.env` fájlt:
```bash
cp .env.example .env
php artisan key:generate
```

Állítsd be az adatbázist a `server/.env` fájlban. A projektben MySQL van használva, a tesztkonfigban a következő adatbázisnév szerepel:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=Fishing_Log
DB_USERNAME=root
DB_PASSWORD=
```

Ezután két lehetőséged van:
1. importálod az `AdatbazisBackup.sql` fájlt MySQL-be
2. vagy lefuttatod a migrációkat és seedereket, ha úgy szeretnéd feltölteni az adatbázist
Példák:
```bash
php artisan migrate
php artisan db:seed
```

Majd indítsd el a Laravel szervert:
```bash
php artisan serve
```

Alapértelmezett backend cím:
```text
http://localhost:8000
http://localhost:8000/api
```

### 2. Frontend indítása
Lépj be a frontend mappába:
```bash
cd client
```

Telepítsd a JavaScript csomagokat:
```bash
npm install
```

Fejlesztői módban a frontend API címe a `client/.env.development` fájlban:
```env
VITE_API_URL=http://localhost:8000/api
```

Indítsd el a fejlesztői szervert:
```bash
npm run dev
```

Alapértelmezett frontend cím:
```text
http://localhost:5173
```

## Weboldal build
Ha a weboldalt buildelni szeretnéd:
```bash
cd client
npm run build
```

A jelenlegi `client/.env` fájl szerint a build ide kerül:
```env
VITE_BUILD_DIR=C:/wamp64/www/proba
VITE_WEB_DIR=/proba/
```
Ha nálad másik webszerver vagy mappa van, akkor ezeket az értékeket módosítsd a saját gépedhez.

## Tesztek futtatása
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

Az e2e teszt `http://localhost:4173` címen fut.
### Backend tesztek
```bash
cd server
php artisan test
```

## Kódrészletek
### Frontend API beállítás
```js
const apiClient = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
  headers: {
    Accept: "application/json",
    "Content-Type": "application/json",
  },
});
```
Ez teszi lehetővé, hogy a frontend a `.env` alapján a megfelelő backend API-hoz kapcsolódjon.

### Védett oldalak a frontend routerben
```js
{
  path: "/catches",
  name: "catches",
  component: () => import("@/views/CatchView.vue"),
  beforeEnter: [checkIfNotLogged],
  meta: {
    title: () => "Fogások",
    breadcrumb: "Fogások",
    roles: [1, 2],
  },
}
```
Itt látszik, hogy bizonyos oldalak csak bejelentkezett felhasználóknak érhetőek el, és szerepkör alapján is védettek.

### Backend API route példa
```php
Route::get('/species', [SpecieController::class, 'index']);
Route::get('/species/{id}', [SpecieController::class, 'show']);
Route::post('/species', [SpecieController::class, 'store'])
    ->middleware(['auth:sanctum', 'ability:species:post']);
```
Ez a rész mutatja, hogy a backend REST API-n keresztül kezeli a halfajok listázását, lekérését és létrehozását.

## Ellenőrzött állapot
Az aktuális munkakörnyezetben ezeket ellenőriztük:
- a frontend unit tesztek lefutottak: `9` tesztfájl, `39` sikeres teszt
- a backend tesztek lefutottak: `63` sikeres teszt, `87` assertion
- a frontend build sikeresen lefutott
Részletesebb tesztleírás a [Tesztek.md](Tesztek.md) fájlban van.
