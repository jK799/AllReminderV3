# AllReminderV3

# AllReminderV3

AllReminderV3 to nowoczesna aplikacja webowa typu **full-stack**, służąca do zarządzania:
- pojazdami,
- urządzeniami,
- dokumentami,
- przypomnieniami i serwisami.

Projekt został wykonany jako aplikacja **SPA + REST API** i spełnia wymagania projektowe z przedmiotu **ZTPAI**.

---

## 📌 Cel projektu

Celem projektu było stworzenie kompletnej aplikacji:
- z wyraźnym podziałem backend / frontend,
- z poprawnie zaprojektowaną bazą danych (3NF),
- z autoryzacją użytkowników,
- z nowoczesnym interfejsem użytkownika,
- z czytelną historią repozytorium Git.

---

## 🧱 Architektura aplikacji

Aplikacja została podzielona na dwie niezależne warstwy:

### Backend
- REST API
- odpowiedzialny za logikę biznesową, walidację danych i dostęp do bazy

### Frontend
- Single Page Application (SPA)
- komunikuje się wyłącznie przez API
- brak bezpośredniego dostępu do bazy danych

Komunikacja odbywa się przez **JSON over HTTP**.

---

## 🛠️ Technologie

### Backend
- **Laravel 12**
- PHP 8.2+
- Laravel Sanctum (tokeny Bearer)
- Eloquent ORM
- SQLite
- REST API

### Frontend
- **Vue 3 (Composition API)**
- **Vite**
- **Tailwind CSS**
- Axios
- Vue Router

### Uzasadnienie wyboru technologii
- Laravel umożliwia szybkie tworzenie bezpiecznego i czytelnego API.
- Vue 3 + Composition API zapewnia modularność i skalowalność frontendowej logiki.
- Tailwind CSS pozwala na spójny, nowoczesny i responsywny interfejs.
- SQLite upraszcza konfigurację środowiska lokalnego.

---

## 🗄️ Baza danych

Baza danych została zaprojektowana zgodnie z zasadami **3 postaci normalnej (3NF)**:
- brak redundancji danych,
- jednoznaczne relacje między tabelami,
- logiczny podział encji.

### Główne tabele:
- `users`
- `vehicles`
- `devices`
- `documents`
- `documentables` (relacja polymorficzna)
- `reminders`
- `services`

Baza danych zawiera **minimum 30 rekordów testowych**.

---

## 🔐 Uwierzytelnianie i autoryzacja

- rejestracja użytkownika,
- logowanie użytkownika,
- token Bearer zapisywany w `localStorage`,
- ochrona endpointów backendu,
- guardy tras w Vue Router,
- automatyczne odtwarzanie sesji po odświeżeniu strony.

---

## 🔌 API

Backend udostępnia REST API:
- zgodne ze standardami HTTP,
- poprawne statusy odpowiedzi (`200`, `201`, `401`, `403`, `422`),
- walidacja danych po stronie serwera.

Przykładowe endpointy:
- `POST /api/login`
- `POST /api/register`
- `GET /api/vehicles`
- `POST /api/devices`
- `POST /api/documents/upload`
- `GET /api/reminders`

---

## 🖥️ Frontend (UX/UI)

- aplikacja responsywna (desktop / mobile),
- nowoczesny, czytelny design,
- dashboard z kafelkami statystyk,
- listy pojazdów i urządzeń,
- formularze CRUD,
- upload dokumentów,
- obsługa stanów `loading` i `error`,
- czytelna nawigacja.

---

## 🚀 Uruchomienie projektu

### Backend

```bash
git clone https://github.com/jK799/AllReminderV3.git
cd AllReminderV3

composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve

http://localhost:8000

### Frontend
npm install
npm run dev

http://localhost:5173

### Struktura projektu
app/
 └── Http/Controllers/Api

resources/
 ├── js/
 │   ├── views/
 │   ├── components/
 │   ├── composables/
 │   ├── services/
 │   └── router.js
 └── css/

database/
 ├── migrations/
 └── database.sqlite
