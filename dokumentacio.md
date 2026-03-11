# Horgasz Naptar

## 1. Projektcim es rovid bemutatas
**Projekt neve:** Horgasz Naptar  
**Projekt celja:** Egy olyan webalkalmazas keszitese, amellyel a horgaszok egy helyen tudjak kezelni a fogasi naploikat, fogasaikat, valamint idojaras alapjan tudjanak tervezni.

Ez a projekt egy full-stack alkalmazas:
- frontend: Vue 3 + Pinia + Vue Router + Bootstrap
- backend: Laravel + REST API + Sanctum token alapu hitelesites
- adatbazis: MySQL/MariaDB (migraciokkal kezelt schema)

## 2. A problema, amit megoldunk
A gyakorlatban a horgaszati adatok sokszor szetszortan vannak:
- papir alapu feljegyzesek,
- kulon jegyzetek telefonban,
- idojaras kulon alkalmazasban.

A projekt celja az volt, hogy ezeket egy rendszerbe szervezzuk:
- felhasznaloi fiok,
- fogasi naplok helyszinnel es idotartammal,
- fogasok rogzitese (halfaj, csali, suly, hossz, ido),
- idojaras elorejelzes es varos valasztas.

## 3. Funkcionalis kovetelmenyek (mit tud az alkalmazas)
1. Regisztracio, bejelentkezes, kijelentkezes
2. Szerepkorok kezelese (admin, horgasz)
3. Fogasi naplo CRUD
4. Fogas CRUD
5. Halfaj/csali/helyszin adatok kezelese
6. Idojaras oldal varos alapu elorejelzessel
7. Felhasznaloi profil oldal

## 4. Technologiai dontesek es indoklas
### Frontend
- **Vue 3**: komponens alapu, gyors fejlesztes
- **Pinia**: allapotkezeles, API adatok kozponti kezelese
- **Vue Router**: vedett oldalak, szerepkor alapu navigacio
- **Bootstrap**: gyors, konzisztens UI elemek

### Backend
- **Laravel**: gyors API fejlesztes, request validacio, migration rendszer
- **Sanctum**: token alapu auth es ability jogosultsagok
- **Controller + Request pattern**: tiszta felelossegi korok

### Adatbazis
- Migraciokkal verziokezelt schema
- Foreign key kapcsolatok az adatintegritas miatt
- Egyedi kulcsok (pl. duplikalt naplozas vedelme)

## 5. Adatmodell roviden
Fo tablakk:
1. `users`
2. `locations`
3. `species`
4. `lures`
5. `catch_logs`
6. `fish_catches`
7. `cities` (idojaras varosvalasztashoz)

Kapcsolatok:
- egy felhasznalonak tobb fogasi naploja lehet
- egy fogasi naplohoz tobb fogas tartozhat
- egy fogas kapcsolodik halfajhoz es csalihoz

## 6. Megvalositas menete (hogyan keszult)
### 6.1 Backend API kiepitese
1. Migraciok letrehozasa
2. Modellek es kontrollerek letrehozasa
3. REST vegpontok kialakitasa (`api.php`)
4. Request validaciok bevezetese
5. Egységes JSON valaszstruktura

### 6.2 Jogosultsag es biztonsag
1. Bejelentkezeskor Sanctum token generalas
2. Ability alapu vedekezes a route-okon
3. Catch log es fish catch vegpontok vedelme auth middleware-rel
4. Felhasznalo csak a sajat naploit/fogasait eri el
5. `userid` szerveroldali beallitas (kliens nem manipulalhatja)

### 6.3 Frontend fejlesztes
1. Router es vedett oldalak
2. Pinia store-ok API hivasokhoz
3. Komponens alapu oldalfelbontas
4. Bootstrap alapu urlapok, gombok, modalok
5. Fogas oldal: letrehozas, modositas, torles, megerosito modal

### 6.4 Idojaras modul
1. Varosok betoltese backendrol
2. Kivalasztott varos koordinatai alapjan lekerdezes
3. Napi elorejelzes kartya + osszegzes megjelenites

## 7. Fontosabb fejlesztesi javitasok a projekt soran
1. Catch log API jogosultsagok szigoritasra kerultek
2. Sajat adathozzaferes kikenszeritese a backendben
3. Hibas validacios szabalyok javitasa (UpdateCatchLogRequest)
4. Dupla API frissitesek csokkentese a store logikaban
5. Torles funkcio bevezetese fogasra es naplora is
6. Megerosito modal (Bootstrap alapu) bevezetese
7. DB mezomeret igazitas (`fish_catches.weight` -> `decimal(5,2)`)
8. Hossz mezopontossag igazitas (`fish_catches.length` -> `decimal(5,1)`)

## 8. Minosegbiztositas es teszteles
### Backend
- `php -l` szintaxis ellenorzes tobb fajlon
- Laravel unit tesztek futtatasa:
  - `CatchLogsTest`
  - `FishCatchesTest`

### Frontend
- `npm run build` futtatas
- komponens szintu ellenorzesek
- oldalak vegigkattintasa manualisan

## 9. Miert tekintheto jo megoldasnak
1. Valos problema megoldasa (adatok egy helyen)
2. Tiszta architektura (kulon frontend-backend)
3. Biztonsagosabb API (auth + ability + sajat adatok szurese)
4. Jo skalahatosag (modularis komponensek, store-ok, migraciok)
5. Konnyen tovabbfejlesztheto
