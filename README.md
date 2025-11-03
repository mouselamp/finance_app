# 💰 Personal Finance Manager

Aplikasi web **manajemen keuangan pribadi** berbasis **Laravel** dengan fitur lengkap untuk mengelola pemasukan, pengeluaran, transfer, dan sistem paylater cicilan.

---

## 🌟 Fitur Utama

### 🔐 Autentikasi & Keamanan
- **Login & Register** dengan Laravel UI
- **Middleware Protection** untuk semua halaman keuangan
- **User-specific Data** - setiap user hanya bisa melihat data miliknya

### 💵 Manajemen Transaksi
- **Multi-type Transactions**: Income, Expense, Transfer antar akun
- **Categorization**: Organisir transaksi dengan kategori kustom
- **Real-time Balance**: Update otomatis saldo akun
- **Transaction History**: Riwayat lengkap semua transaksi
- **Advanced Filtering**: Filter berdasarkan tanggal, kategori, akun, tipe

### 💳 Sistem Paylater
- **Paylater Accounts**: Support untuk akun kredit (Shopee PayLater, Kredivo, dll)
- **Cicilan System**:
  - Bayar penuh atau cicilan (1-12 bulan)
  - Tracking status pembayaran
  - Overdue notifications
  - Automatic balance updates
- **Payment Management**: Bayar cicilan dari akun lain

### 🏦 Multi-Account Management
- **Account Types**: Cash, Bank, E-Wallet, Paylater
- **Balance Tracking**: Monitor saldo real-time semua akun
- **Account Details**: Informasi lengkap dan transaksi per akun
- **Account Limits**: Set limit untuk akun paylater

### 📊 Dashboard & Analytics
- **Financial Overview**: Ringkasan total saldo, pemasukan, pengeluaran
- **Visual Charts**: Grafik interaktif dengan Chart.js
- **Recent Transactions**: Monitor transaksi terbaru
- **Quick Actions**: Akses cepat tambah transaksi
- **Statistics**: Statistik detail per kategori dan periode

### 📱 Modern UI/UX
- **Responsive Design**: Optimal di desktop dan mobile
- **Dark Mode Support**: Interface yang nyaman di mata
- **Real-time Updates**: Update tanpa refresh halaman
- **Modern Cards**: Design bersih dengan Tailwind CSS
- **Interactive Elements**: Smooth transitions dan micro-interactions

---

## 🛠️ Teknologi

### Backend
- **PHP 7.3+**
- **Laravel 8.x**
- **MySQL/MariaDB**
- **RESTful API**
- **Eloquent ORM**

### Frontend
- **Blade Templates**
- **Tailwind CSS**
- **JavaScript (Vanilla)**
- **Font Awesome Icons**
- **Chart.js** untuk visualisasi data

---

## 📋 Requirements

- PHP 7.3 atau higher
- Composer
- MySQL/MariaDB
- Web server (Apache/Nginx)
- Node.js & NPM (untuk development assets)

---

## 🚀 Instalasi

### 1. Clone Repository
```bash
git clone https://github.com/mouselamp/finance_app.git
cd finance_app
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

### 4. Konfigurasi Database
Edit file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=financial_app
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Database Migration
```bash
php artisan migrate
```

### 6. Link Storage
```bash
php artisan storage:link
```

### 7. Build Assets
```bash
npm run dev
# atau untuk production
npm run build
```

### 8. Start Application
```bash
php artisan serve
# atau akses langsung ke folder /public
```

---

## 📖 Panduan Penggunaan

### 🏠 Dashboard
- **Overview**: Lihat ringkasan keuangan Anda
- **Quick Actions**: Tambah transaksi cepat
- **Recent Transactions**: Monitor transaksi terbaru
- **Statistics**: Grafik pemasukan/pengeluaran

### 💸 Transaksi

#### Menambah Income
1. Klik "Tambah Transaksi" → "Pemasukan"
2. Pilih akun tujuan
3. Masukkan jumlah dan kategori
4. Tambah catatan (opsional)
5. Klik "Simpan"

#### Menambah Expense
1. Klik "Tambah Transaksi" → "Pengeluaran"
2. Pilih akun sumber
3. **Untuk Paylater**: Pilih akun paylater untuk opsi cicilan
4. Masukkan jumlah dan kategori
5. **Paylater Options**:
   - **Bayar Penuh**: Lunas sekaligus
   - **Cicilan**: Pilih jangka waktu (1-12 bulan)
6. Klik "Simpan"

#### Transfer Antar Akun
1. Klik "Transfer"
2. Pilih akun sumber dan tujuan
3. Masukkan jumlah transfer
4. Tambah catatan
5. Klik "Transfer"

### 🏦 Manajemen Akun

#### Membuat Akun Baru
1. Menu "Akun" → "Tambah Akun"
2. **Informasi Akun**:
   - Nama akun (contoh: BNI, Gopay, Cash)
   - Tipe akun: Cash, Bank, E-Wallet, Paylater
3. **Untuk Paylater**: Set limit kredit
4. Klik "Simpan"

#### Mengelola Paylater
- **Buat Transaksi**: Pilih akun paylater saat membuat expense
- **Bayar Cicilan**: Menu "Paylater" → pilih transaksi → "Bayar Cicilan"
- **Pilih cicilan** yang akan dibayar
- **Pilih metode pembayaran** dari akun lain
- **Konfirmasi pembayaran**

### 🏷️ Kategori
1. Menu "Kategori" → "Tambah Kategori"
2. Masukkan nama kategori
3. Pilih tipe (Income/Expense)
4. Tambah catatan (opsional)

---

## 💡 Tips Penggunaan

### Budget Management
- **Track Expenses**: Monitor pengeluaran harian/mingguan
- **Category Analysis**: Lihat pengeluaran per kategori
- **Set Goals**: Alokasikan budget per kategori

### Paylater Best Practices
- **Monitor Due Dates**: Perhatikan jatuh tempo cicilan
- **Pay On Time**: Hindari denda keterlambatan
- **Balance Usage**: Jangan melebihi 80% limit

### Financial Planning
- **Monthly Review**: Review keuangan bulanan
- **Saving Goals**: Set target tabungan
- **Emergency Fund**: Siapkan dana darurat

---

## 🎯 Kategori Umum

| Jenis | Contoh Kategori |
|-------|-----------------|
| **Pemasukan** | Gaji, Bonus, Freelance, Penjualan, Hadiah |
| **Pengeluaran** | Makan, Transport, Tagihan, Belanja, Hiburan |
| **Investasi** | Saham, Reksadana, Crypto |
| **Kesehatan** | Asuransi, Obat-obatan, Dokter |
| **Edukasi** | Kursus, Buku, Seminar |

---

## 🏛️ Akun Payment

| Tipe Akun | Contoh |
|-----------|--------|
| **Cash** | Dompet, Cash di rumah |
| **Bank** | BCA, Mandiri, BNI, BRI |
| **E-Wallet** | Gopay, OVO, Dana, ShopeePay |
| **Paylater** | Shopee PayLater, Kredivo, Akulaku |

---

## 🔧 Konfigurasi

### Environment Variables
```env
# Application
APP_NAME="Financial Management"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=financial_app

# Currency
DEFAULT_CURRENCY=IDR
CURRENCY_SYMBOL=Rp
```

---

## 📊 API Endpoints

### Authentication
- `POST /api/login` - User login
- `GET /api/auth/me` - Get current user

### Transactions
- `GET /api/transactions` - List transactions
- `POST /api/transactions` - Create transaction
- `GET /api/transactions/statistics` - Get statistics

### Accounts
- `GET /api/accounts` - List accounts
- `POST /api/accounts` - Create account
- `PUT /api/accounts/{id}` - Update account

---

## 🚨 Troubleshooting

### Common Issues

#### 1. Blank Page
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### 2. Database Connection
- Check `.env` configuration
- Verify database is running
- Check credentials

#### 3. Assets Not Loading
```bash
npm run build
php artisan storage:link
```

---

## 📝 Development

### Database Migrations
```bash
php artisan make:migration create_transactions_table
php artisan migrate
```

### Controllers
- `TransactionController` - Manage transactions
- `AccountController` - Manage accounts
- `CategoryController` - Manage categories
- `PaylaterController` - Manage paylater transactions

### Models
- `Transaction` - Transaction model
- `Account` - Account model
- `Category` - Category model
- `PaylaterTransaction` - Paylater specific transactions
- `Installment` - Installment payments

---

## 🤝 Kontribusi

1. Fork the repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 👨‍💻 Author

**Your Name** - [GitHub Profile](https://github.com/username)

---

**© 2024 Financial Management App. All rights reserved.**