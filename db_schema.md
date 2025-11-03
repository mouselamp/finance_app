# 📘 Database Schema — Aplikasi Manajemen Keuangan Pribadi

> Dokumen ini menjelaskan struktur tabel database yang digunakan untuk aplikasi manajemen keuangan berbasis Laravel 7.

---

## 🧩 1. Tabel: `users`
Menyimpan data pengguna (untuk login/auth).

| Kolom | Tipe Data | Keterangan |
|--------|------------|------------|
| id | BIGINT UNSIGNED (PK) | Auto increment |
| name | VARCHAR(100) | Nama pengguna |
| email | VARCHAR(100) | Email unik |
| password | VARCHAR(255) | Password (bcrypt) |
| remember_token | VARCHAR(100) | Token login |
| created_at | TIMESTAMP |  |
| updated_at | TIMESTAMP |  |

---

## 💰 2. Tabel: `accounts`
Menyimpan sumber dana (contoh: Cash, Bank BCA, Shopee Paylater, dsb.)

| Kolom | Tipe Data | Keterangan |
|--------|------------|------------|
| id | BIGINT UNSIGNED (PK) | Auto increment |
| user_id | BIGINT UNSIGNED (FK → users.id) | Pemilik akun |
| name | VARCHAR(100) | Nama akun (contoh: Bank BCA, Dompet Cash, Kredivo) |
| type | ENUM('cash','bank','paylater') | Jenis akun |
| balance | DECIMAL(15,2) | Saldo terakhir |
| note | TEXT | Catatan tambahan |
| created_at | TIMESTAMP |  |
| updated_at | TIMESTAMP |  |

---

## 🏷️ 3. Tabel: `categories`
Kategori transaksi, misalnya: Makan, Transportasi, Gaji, Cicilan, dll.

| Kolom | Tipe Data | Keterangan |
|--------|------------|------------|
| id | BIGINT UNSIGNED (PK) | Auto increment |
| user_id | BIGINT UNSIGNED (FK → users.id) | Pemilik kategori |
| name | VARCHAR(100) | Nama kategori |
| type | ENUM('income','expense') | Jenis kategori |
| created_at | TIMESTAMP |  |
| updated_at | TIMESTAMP |  |

---

## 💵 4. Tabel: `transactions`
Mencatat setiap transaksi keuangan.

| Kolom | Tipe Data | Keterangan |
|--------|------------|------------|
| id | BIGINT UNSIGNED (PK) | Auto increment |
| user_id | BIGINT UNSIGNED (FK → users.id) | Pemilik transaksi |
| account_id | BIGINT UNSIGNED (FK → accounts.id) | Akun sumber atau tujuan |
| category_id | BIGINT UNSIGNED (FK → categories.id, nullable) | Kategori transaksi |
| type | ENUM('income','expense','transfer','paylater_payment') | Jenis transaksi |
| date | DATE | Tanggal transaksi |
| amount | DECIMAL(15,2) | Nominal transaksi |
| note | TEXT | Catatan tambahan |
| related_account_id | BIGINT UNSIGNED (nullable) | Untuk transfer antar akun |
| created_at | TIMESTAMP |  |
| updated_at | TIMESTAMP |  |

🧠 **Catatan:**
- Jika `type = 'transfer'`, maka `account_id` adalah sumber, dan `related_account_id` adalah tujuan.
- Jika `type = 'paylater_payment'`, maka nominalnya akan *mengurangi* saldo sumber dana dan *menambah* saldo akun paylater.

---

## 🧾 5. Tabel: `paylater_transactions`
Detail transaksi paylater, baik yang dibayar penuh atau dicicil.

| Kolom | Tipe Data | Keterangan |
|--------|------------|------------|
| id | BIGINT UNSIGNED (PK) | Auto increment |
| user_id | BIGINT UNSIGNED (FK → users.id) | Pemilik transaksi |
| account_id | BIGINT UNSIGNED (FK → accounts.id, type='paylater') | Akun paylater |
| date | DATE | Tanggal pembelian |
| total_amount | DECIMAL(15,2) | Total tagihan |
| payment_type | ENUM('full','installment') | Jenis pembayaran |
| tenor | TINYINT (nullable) | Lama cicilan (bulan) |
| monthly_amount | DECIMAL(15,2) | Nominal per bulan |
| note | TEXT | Catatan transaksi |
| created_at | TIMESTAMP |  |
| updated_at | TIMESTAMP |  |

---

## 💳 6. Tabel: `installments`
Untuk mencatat setiap cicilan paylater yang harus dibayar.

| Kolom | Tipe Data | Keterangan |
|--------|------------|------------|
| id | BIGINT UNSIGNED (PK) | Auto increment |
| paylater_transaction_id | BIGINT UNSIGNED (FK → paylater_transactions.id) | Transaksi induk |
| due_date | DATE | Tanggal jatuh tempo |
| amount | DECIMAL(15,2) | Jumlah cicilan |
| status | ENUM('unpaid','paid') | Status pembayaran |
| paid_at | DATE (nullable) | Tanggal dibayar |
| created_at | TIMESTAMP |  |
| updated_at | TIMESTAMP |  |

---

## 🔄 7. Relasi antar tabel (summary)

| Entitas | Relasi | Keterangan |
|----------|---------|------------|
| users → accounts | 1:N | Satu user bisa punya banyak akun |
| users → categories | 1:N | Kategori per pengguna |
| users → transactions | 1:N | Transaksi per pengguna |
| accounts → transactions | 1:N | Setiap transaksi terkait akun |
| transactions → categories | N:1 | Opsional (bisa null) |
| paylater_transactions → accounts | N:1 | Akun paylater |
| paylater_transactions → installments | 1:N | Tiap cicilan milik satu transaksi paylater |

---

## 💡 Catatan Desain

- `balance` di tabel `accounts` dapat disinkronisasi otomatis melalui event atau command (misal: setiap insert transaksi).
- Transfer antar akun cukup satu baris transaksi (bukan dua entri terpisah), dengan `related_account_id`.
- Paylater dapat dimasukkan ke laporan saldo (utang bertambah jika `payment_type = 'installment'`).

---

## 📊 Rencana Pengembangan Lanjutan

| Fitur | Status | Catatan |
|--------|--------|----------|
| Multi-user support | ✅ | Sudah ada `user_id` |
| Paylater installment | ✅ | Penuh & cicilan |
| Laporan bulanan | ⏳ | Akan dihitung berdasarkan `transactions` |
| Export CSV / Excel | ⏳ | Pakai `maatwebsite/excel` |
| Sinkronisasi saldo otomatis | ⏳ | Event-based balance recalculation |
| Notifikasi jatuh tempo | ⏳ | Bisa ditambahkan cron job sederhana |

---

## 🧮 Contoh Skema Singkat

```text
User (1)
 ├── Accounts (Cash, Bank, Paylater)
 ├── Categories (Gaji, Makan, Transport)
 ├── Transactions
 │     ├── Income → Bank
 │     ├── Expense → Cash
 │     ├── Transfer → Bank → Cash
 │     └── Paylater Payment → Bank → Paylater
 └── Paylater Transactions
        ├── Full Payment
        └── Installment (dengan beberapa cicilan)
