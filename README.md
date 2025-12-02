# Financial App

Aplikasi manajemen keuangan pribadi berbasis web yang dibangun dengan Laravel 10. Mendukung pencatatan transaksi, manajemen akun, pelacakan paylater/cicilan, dan fitur berbagi akun dalam grup keluarga.

## Fitur Utama

- **Dashboard** - Ringkasan keuangan dengan grafik dan statistik
- **Transaksi** - Catat pemasukan, pengeluaran, dan transfer antar akun
- **Multi Akun** - Kelola berbagai jenis akun (Cash, Bank, Paylater)
- **Kategori** - Kategorisasi transaksi dengan ikon dan warna kustom
- **Paylater & Cicilan** - Pelacakan transaksi paylater dengan pembayaran cicilan
- **Laporan** - Laporan keuangan dengan filter periode dan export
- **Grup Keluarga** - Berbagi akses akun dengan anggota keluarga (view-only)
- **Receipt Analyzer** - Analisis struk belanja (memerlukan API eksternal)

## Tech Stack

- **Backend**: PHP 8.1+, Laravel 10
- **Frontend**: Blade, Tailwind CSS, Alpine.js
- **Database**: PostgreSQL
- **Build Tool**: Vite

## Requirements

- PHP >= 8.1
- Composer
- Node.js >= 16
- PostgreSQL

## Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/username/financial-app.git
cd financial-app
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

Edit file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=financial_app
DB_USERNAME=postgres
DB_PASSWORD=
```

### 4. Database Migration

```bash
php artisan migrate
```

### 5. Build Assets

```bash
# Development
npm run dev

# Production
npm run build
```

### 6. Jalankan Server

```bash
php artisan serve
```

Akses aplikasi di `http://localhost:8000`

## Struktur Database

| Tabel | Deskripsi |
|-------|-----------|
| users | Data pengguna |
| groups | Grup keluarga untuk berbagi akun |
| accounts | Akun keuangan (cash, bank, paylater) |
| categories | Kategori transaksi |
| transactions | Catatan transaksi |
| paylater_transactions | Transaksi paylater |
| installments | Cicilan pembayaran |

## API Endpoints

Aplikasi menyediakan REST API untuk integrasi mobile/external:

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/api/dashboard` | Data dashboard |
| GET | `/api/accounts` | Daftar akun |
| GET | `/api/transactions` | Daftar transaksi |
| GET | `/api/categories` | Daftar kategori |
| GET | `/api/reports` | Data laporan |

Autentikasi menggunakan API token di header: `Authorization: Bearer {token}`

## License

[MIT License](LICENSE)
