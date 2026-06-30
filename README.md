# ✈️ Airline Voucher Seat Assignment

A full-stack web application for assigning 3 random unique voucher seats per flight.

Built with **React** (frontend) + **Laravel 11** (backend) + **SQLite** (database).

---

## Architecture

```
project/
├── frontend/          # React + Vite
│   └── src/
│       ├── App.jsx               # Main form + flow logic
│       ├── api.js                # Axios API client
│       └── components/
│           └── SeatCard.jsx      # Seat display component
├── backend/           # Laravel 11
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   │   └── VoucherController.php
│   │   │   ├── Requests/
│   │   │   │   ├── CheckVoucherRequest.php
│   │   │   │   └── GenerateVoucherRequest.php
│   │   │   └── Resources/
│   │   │       └── VoucherResource.php
│   │   ├── Models/
│   │   │   └── Voucher.php
│   │   └── Services/
│   │       └── SeatGeneratorService.php
│   ├── database/migrations/
│   ├── routes/api.php
│   └── tests/Feature/VoucherTest.php
├── docker-compose.yml
└── README.md
```

---

## Prerequisites

- **PHP** 8.2 or higher
- **Composer** 2.x
- **Node.js** 20.x and **npm** 9.x
- **SQLite** (usually pre-installed on Linux/macOS; on Windows install from https://sqlite.org)
- *(Optional)* **Docker** + **Docker Compose**

---

## Quick Start (Manual)

### 1. Backend Setup

```bash
cd backend

# Install PHP dependencies
composer install

# Configure environment
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set the **absolute path** to your SQLite database file:

```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/project/backend/database/vouchers.db
```

> **Windows example:** `DB_DATABASE=C:/projects/airline-voucher/backend/database/vouchers.db`

```bash
# Create the SQLite DB file
mkdir -p database
touch database/vouchers.db   # on Windows: type nul > database\vouchers.db

# Run migrations
php artisan migrate

# Start the backend server
php artisan serve
# API available at: http://localhost:8000
```

---

### 2. Frontend Setup

```bash
cd frontend

# Install Node dependencies
npm install

# Start the dev server
npm run dev
# App available at: http://localhost:5173
```

The Vite dev server proxies `/api/*` requests to `http://localhost:8000`, so no additional CORS configuration is needed during development.

---

## Quick Start (Docker)

```bash
# From the project root
docker-compose up --build
```

| Service  | URL                    |
|----------|------------------------|
| Frontend | http://localhost:5173  |
| Backend  | http://localhost:8000  |

---

## Running Tests

```bash
cd backend
php artisan test
```

Test coverage includes:
- `POST /api/check` — returns `false` when no voucher exists
- `POST /api/check` — returns `true` when voucher already exists
- `POST /api/check` — validates required fields
- `POST /api/generate` — creates voucher with valid seats (Airbus 320)
- `POST /api/generate` — returns 409 on duplicate flight + date
- `POST /api/generate` — validates required fields
- `POST /api/generate` — rejects invalid aircraft type
- `POST /api/generate` — ATR seat validation (rows 1-18, cols A/C/D/F)
- `POST /api/generate` — Boeing 737 Max seat validation

---

## API Reference

### `POST /api/check`

Check if vouchers already exist for a flight on a given date.

**Request:**
```json
{
  "flightNumber": "GA102",
  "date": "2025-07-12"
}
```

**Response:**
```json
{ "exists": false }
```

---

### `POST /api/generate`

Generate 3 unique random seats and persist to database.

**Request:**
```json
{
  "name": "Sarah",
  "id": "98123",
  "flightNumber": "GA102",
  "date": "2025-07-12",
  "aircraft": "Airbus 320"
}
```

**Response (201):**
```json
{
  "data": {
    "success": true,
    "seats": ["3B", "7C", "14D"],
    "details": {
      "crewName": "Sarah",
      "crewId": "98123",
      "flightNumber": "GA102",
      "flightDate": "2025-07-12",
      "aircraftType": "Airbus 320"
    }
  }
}
```

**Response (409) — duplicate:**
```json
{
  "success": false,
  "message": "Vouchers have already been generated for this flight and date."
}
```

---

## Seat Layout Reference

| Aircraft      | Rows | Columns       | Total Seats |
|---------------|------|---------------|-------------|
| ATR           | 1–18 | A, C, D, F    | 72          |
| Airbus 320    | 1–32 | A, B, C, D, E, F | 192      |
| Boeing 737 Max| 1–32 | A, B, C, D, E, F | 192      |

---

## Tech Stack

| Layer    | Technology                          |
|----------|-------------------------------------|
| Frontend | React 18, Vite 5, Axios             |
| Backend  | Laravel 11, PHP 8.2+                |
| Database | SQLite                              |
| Testing  | PHPUnit (via `php artisan test`)    |
| Docker   | Docker Compose (optional)           |
