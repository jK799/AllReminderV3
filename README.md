# AllReminderV3

AllReminderV3 to nowoczesna aplikacja webowa typu **full-stack (SPA + REST API)**, stworzona w ramach przedmiotu **ZTPAI**.  
Aplikacja umożliwia użytkownikowi zarządzanie:
- pojazdami,
- urządzeniami,
- dokumentami,
- serwisami,
- przypomnieniami.

Projekt spełnia komplet wymagań projektowych określonych przez prowadzącego.

---

## 1. Cel projektu

Celem projektu było zaprojektowanie i zaimplementowanie kompletnej aplikacji webowej:
- z wyraźnym podziałem na backend i frontend,
- z poprawnie zaprojektowaną bazą danych w 3 postaci normalnej (3NF),
- z autoryzacją użytkowników,
- z realną komunikacją frontend–backend przez REST API,
- z nowoczesnym i responsywnym interfejsem użytkownika,
- z czytelną i spójną historią repozytorium Git.

---

## 2. Architektura aplikacji

Aplikacja została zaprojektowana w architekturze **SPA + REST API**.

### Backend
- udostępnia REST API,
- realizuje logikę biznesową,
- waliduje dane,
- zarządza bazą danych,
- obsługuje uwierzytelnianie użytkowników.

### Frontend
- aplikacja typu Single Page Application,
- komunikuje się wyłącznie z API,
- nie ma bezpośredniego dostępu do bazy danych,
- obsługuje routing, widoki oraz stany aplikacji.

Komunikacja odbywa się w formacie **JSON over HTTP**.

---

## 3. Zastosowane technologie

### Backend
- Laravel 12
- PHP 8.2+
- Laravel Sanctum (Bearer Token)
- Eloquent ORM
- SQLite
- REST API

### Frontend
- Vue 3 (Composition API)
- Vite
- Tailwind CSS
- Axios
- Vue Router

### Uzasadnienie doboru technologii
Laravel zapewnia szybkie i bezpieczne tworzenie API oraz czytelną architekturę backendu.  
Vue 3 z Composition API umożliwia modularną i skalowalną logikę frontendową.  
Tailwind CSS pozwala na spójny, nowoczesny i responsywny interfejs użytkownika.  
SQLite upraszcza konfigurację środowiska lokalnego.

---

## 4. Baza danych

Baza danych została zaprojektowana zgodnie z zasadami **3NF**:
- brak redundancji danych,
- jednoznaczne relacje,
- logiczny podział encji.

W bazie znajduje się minimum **30 rekordów testowych**.

### Tabele:
- users
- vehicles
- devices
- documents
- reminders
- services

---

## 5. Diagram ERD

```mermaid
erDiagram
  USERS {
    int id PK
    string name
    string email
    datetime created_at
    datetime updated_at
  }

  VEHICLES {
    int id PK
    int user_id FK
    string name
    string make
    string model
    string year
    string vin
    string license_plate
    date purchase_date
    text notes
    datetime created_at
    datetime updated_at
  }

  DEVICES {
    int id PK
    int user_id FK
    string name
    string brand
    string model
    string serial_number
    date purchase_date
    text notes
    datetime created_at
    datetime updated_at
  }

  SERVICES {
    int id PK
    int user_id FK
    int vehicle_id FK
    int device_id FK
    string title
    text description
    date last_done_at
    date next_due_at
    int interval_value
    string interval_unit
    boolean is_active
    datetime created_at
    datetime updated_at
  }

  REMINDERS {
    int id PK
    int user_id FK
    int vehicle_id FK
    int device_id FK
    string title
    text description
    datetime due_at
    datetime remind_at
    datetime completed_at
    boolean is_active
    datetime created_at
    datetime updated_at
  }

  DOCUMENTS {
    int id PK
    int user_id FK
    string title
    text notes
    string original_name
    string path
    string mime_type
    int size
    datetime created_at
    datetime updated_at
  }

  USERS ||--o{ VEHICLES : owns
  USERS ||--o{ DEVICES : owns
  USERS ||--o{ DOCUMENTS : uploads
  USERS ||--o{ SERVICES : manages
  USERS ||--o{ REMINDERS : manages

  VEHICLES ||--o{ SERVICES : has
  DEVICES ||--o{ SERVICES : has
  VEHICLES ||--o{ REMINDERS : has
  DEVICES ||--o{ REMINDERS : has

---

## 4. Baza danych