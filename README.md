# 🎯 Local Interactive Raffle Engine (Undian Jalan Sehat)

Aplikasi berbasis web untuk manajemen dan visualisasi pengundian kupon **Jalan Sehat / Raffle Event** berbasis *Offline-First*. Aplikasi ini dirancang murni untuk dijalankan di jaringan lokal (LAN) tanpa ketergantungan koneksi internet, dengan pencegahan duplikasi pemenang secara atomic, fitur multi-winner batch draw, dan efek visual panggung 3D yang memukau untuk penonton di lapangan.

---

## 🌟 Fitur Utama (Key Features)

- **Dual-Screen Realtime Sync (Single Server, Dual View):**
  - `/admin` &rarr; Panel kontrol interaktif untuk panitia (akses via laptop/tablet/smartphone admin).
  - `/display` &rarr; Layar proyektor/LED panggung untuk penonton (akses via browser fullscreen).
  - Sinkronisasi instan **0ms** menggunakan `BroadcastChannel`, `localStorage` event, serta fallback JSON polling ultra-ringan (`/api/raffle/state` < 2ms).

- **🎰 60 FPS Casino Jackpot Slot Machine Reel:**
  - Animasi perputaran gulungan 3D ala kasino 60 FPS (*GPU Hardware-Accelerated* `transform: translateY`) dengan efek *motion blur* dan pengereman *cubic-bezier elastic bounce* saat digit terkunci.

- **⏱️ Penguncian Digit Bertahap & Setting Kecepatan (Jackpot Speed Control):**
  - Animasi penguncian per-digit bertahap (Digit 1 &rarr; Digit 2 &rarr; Digit 3 &rarr; Digit 4) disertai bunyi ketukan mekanis.
  - Opsi Kecepatan Penguncian di Admin Panel: `⚡ Cepat (150ms)`, `🎯 Normal (350ms)`, `🎭 Dramatis (700ms)`, dan `🔥 Super Dramatis (1.2s)`.

- **🔢 Multi-Winner Batch Drawing (1 s/d 5 Pemenang Sekaligus):**
  - Memungkinkan admin mengundi **1, 2, 3, 4, atau 5 pemenang** secara bersamaan dalam 1 kali putaran (`DB::transaction` atomic).
  - Tata letak *responsive grid* simetris (maksimal 3 kartu per baris): 4 pemenang tampil 2x2 grid, 5 pemenang tampil 3 top + 2 bottom centered.

- **📋 MC Monitor Table (Pemenang Terkini & Dianulir):**
  - Layar `/display` dilengkapi tabel khusus MC di sebelah kanan panggung:
    - **Pemenang Sah:** Kartu Emas Menyala + Nomor Kupon Emas.
    - **Pemenang Dianulir:** Kartu Merah + Text Coret (`line-through`) + Badge `🚫 ANULIR`.
  - Dilengkapi mekanisme *Anti-Spoiler Buffer* (data pemenang baru di-commit ke tabel MC tepat saat animasi gulungan panggung selesai).

- **🚫 Anti-Duplicate & Validasi Kuota Atomic:**
  - Pengecekan otomatis di backend query builder (`Coupon::eligible()`). Kupon yang sudah pernah menang atau dianulir tidak akan diundi kembali.

- **📱 100% Mobile & Touch Friendly:**
  - Tampilan responsif di smartphone, tablet, laptop, dan layar proyektor panggung.

- **🔊 100% Offline Audio Synthesizer & Confetti:**
  - Efek suara ticking & fanfare kemenangan menggunakan Web Audio API murni (tanpa file audio eksternal) dan `canvas-confetti`.

---

## 🛠️ Tech Stack

- **Backend Framework:** Laravel (PHP 8.2+)
- **Frontend Engine:** Livewire 3 & Alpine.js
- **Styling:** Tailwind CSS & Google Fonts (*Outfit*, *Plus Jakarta Sans*, *JetBrains Mono*)
- **Database:** SQLite (`database/database.sqlite`) - *Offline-First Local Storage*
- **Build Tool:** Vite

---

## 🗄️ Database Schema

| Table | Column | Type | Modifiers / Description |
| :--- | :--- | :--- | :--- |
| **`prizes`** | `id` | BigIncrements | Primary Key |
| | `name` | String | Nama Hadiah (Contoh: "Sepeda Gunung") |
| | `quota` | Integer | Total Kuota Hadiah |
| | `image_path` | String | Nullable (Path foto hadiah) |
| **`coupons`** | `id` | BigIncrements | Primary Key |
| | `coupon_number` | String | Unique (Contoh: "JLS-0001") |
| | `name` | String | Nullable (Nama peserta) |
| **`winners`** | `id` | BigIncrements | Primary Key |
| | `coupon_id` | ForeignId | Constrained to `coupons` |
| | `prize_id` | ForeignId | Constrained to `prizes` |
| | `status` | Enum | `['valid', 'annulled']` (Default: `valid`) |

---

## 🚀 Panduan Instalasi & Pengoperasian

### Prasyarat System:
- PHP >= 8.2 (dengan ekstensi `pdo_sqlite` aktif)
- Composer
- Node.js & NPM

### Langkah Instalasi:

1. **Clone repository:**
   ```bash
   git clone https://github.com/user/raffle-app.git
   cd raffle-app
   ```

2. **Install dependensi PHP & Node.js:**
   ```bash
   composer install
   npm install
   ```

3. **Setup environment & database SQLite:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *(Pastikan `.env` menggunakan `SESSION_DRIVER=file` dan `CACHE_STORE=file` untuk performa SQLite terbaik)*

4. **Migrasi database & jalankan seeder:**
   ```bash
   php artisan migrate:fresh --seed
   ```
   *Seeder otomatis memasukkan 15 unit hadiah real & 1.000 kupon peserta (`JLS-0001` s/d `JLS-1000`).*

5. **Build asset frontend:**
   ```bash
   npm run build
   ```

6. **Jalankan server lokal:**
   ```bash
   php artisan serve --host=0.0.0.0 --port=8000
   ```

7. **Akses Aplikasi:**
   - **Admin Control Panel:** `http://localhost:8000/admin`
   - **Layar Display Proyektor:** `http://localhost:8000/display`

---

## 🧪 Automated Testing

Aplikasi ini dilengkapi dengan automated test suite lengkap untuk menguji ketersediaan endpoint, flow `RaffleService`, validasi kuota, pengundian batch multi-winner, dan sinkronisasi state.

Jalankan pengujian dengan perintah:
```bash
php artisan test
```

Hasil Pengujian:
```text
Tests:    15 passed (59 assertions)
Duration: 0.65s
```

---

## 📄 Licences

Aplikasi ini dikembangkan di bawah lisensi MIT.
