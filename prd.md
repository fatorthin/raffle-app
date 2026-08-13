# Product Requirements Document (PRD)

## Project Name: Local Interactive Raffle App (Undian Jalan Sehat)

### 1. Project Overview

Aplikasi berbasis web untuk manajemen dan visualisasi pengundian kupon "Jalan Sehat". Aplikasi ini dirancang untuk dijalankan murni secara lokal tanpa ketergantungan koneksi internet (_offline-first_ pada jaringan LAN), dengan fokus pada pencegahan duplikasi pemenang dan efek visual yang menarik untuk penonton di lapangan.

### 2. Tech Stack

- **Backend Framework:** Laravel (versi terbaru)
- **Frontend Framework:** Livewire 3 & Alpine.js
- **Styling:** Tailwind CSS
- **Database:** SQLite (`database.sqlite`) - **PENTING: Jangan gunakan layanan cloud atau koneksi eksternal.**
- **Libraries Tambahan:** `canvas-confetti` (via CDN/Local NPM) untuk animasi perayaan, HTML5 Audio API untuk _sound effects_.

### 3. Architecture & Core Concept

- **Single Server, Dual View:** Aplikasi berjalan di satu _local server_. Terdapat dua URL utama:
    - `/admin` -> Panel kontrol untuk panitia (diakses via laptop/tablet admin).
    - `/display` -> Layar proyektor/LED untuk penonton (diakses via browser yang di-_fullscreen_).
- **Event-Driven Synchronization:** Menggunakan Livewire Events (`wire:poll` atau _browser events_) untuk menyinkronkan _state_ antara aksi di `/admin` dan reaksi visual di `/display`.
- **Theatrical RNG:** Pengacakan asli (RNG) terjadi di backend dalam hitungan milidetik. Animasi berputar di frontend `/display` hanya teater/ilusi visual.

### 4. Database Schema

Gunakan penamaan bahasa Inggris untuk standar kode.

| Table     | Column          | Type       | Modifiers/Notes                          |
| :-------- | :-------------- | :--------- | :--------------------------------------- |
| `prizes`  | `id`            | ID         | Primary Key                              |
|           | `name`          | String     | Contoh: "Sepeda Gunung"                  |
|           | `quota`         | Integer    | Jumlah ketersediaan hadiah               |
|           | `image_path`    | String     | Nullable (Path foto hadiah)              |
| `coupons` | `id`            | ID         | Primary Key                              |
|           | `coupon_number` | String     | Unique (Contoh: "JLS-001")               |
|           | `name`          | String     | Nullable (Nama pemilik kupon)            |
| `winners` | `id`            | ID         | Primary Key                              |
|           | `coupon_id`     | Foreign ID | Constrained to `coupons`                 |
|           | `prize_id`      | Foreign ID | Constrained to `prizes`                  |
|           | `status`        | Enum       | `['valid', 'annulled']` Default: `valid` |

### 5. Features & Logic Requirements

#### A. Admin Panel (`/admin`)

- **Dashboard Utama:** Menampilkan metrik sisa kupon yang belum diundi dan sisa hadiah.
- **Manajemen Data (CRUD dasar):**
    - Upload data kupon massal via CSV atau generate otomatis dari range angka (misal: 00001 - 10000).
    - Input data hadiah dan kuotanya.
- **Raffle Controller (Fitur Utama Admin):**
    - _Dropdown/Card selection:_ Pilih `prize_id` yang akan diundi.
    - _Button Action:_ Tombol "Mulai Putar" (mengirim sinyal ke `/display` untuk memulai animasi) dan tombol "Stop & Tampilkan Pemenang".
    - _Action: Annul (Anulir):_ Jika pemenang tidak hadir, admin dapat menekan tombol "Anulir". Sistem mengupdate tabel `winners` status menjadi `annulled` dan kuota `prize` yang sedang diundi tetap/tidak berkurang.

#### B. Public Display (`/display`)

- **UI/UX:** _Dark mode_ dengan kontras tinggi (Background gelap, teks kuning/putih) agar terlihat jelas di luar ruangan (LED Screen). Layout berpusat (Center).
- **State Management (Alpine.js + Livewire):**
    - **Idle State:** Menampilkan nama/gambar hadiah yang sedang disiapkan oleh Admin.
    - **Rolling State:** Saat menerima _event_ "Mulai Putar" dari Admin, Alpine.js memicu fungsi `setInterval` yang menampilkan angka/huruf acak secara cepat di layar. Di belakang layar, Livewire sudah me-_request_ data pemenang asli dari database.
    - **Winner State:** Saat menerima _event_ "Stop" dari Admin, `setInterval` dihentikan. Layar menampilkan nomor kupon pemenang asli secara tebal, memicu `canvas-confetti`, dan memutar efek suara kemenangan.
- **Ticker/History:** Di bagian bawah layar (footer), terdapat teks berjalan (_marquee_) atau _list_ statis berisi riwayat 5-10 pemenang terakhir beserta hadiahnya.

#### C. Backend Logic (Strict Rules for AI)

1.  **Mencegah Duplikasi Pemenang:** Saat melakukan query pengacakan, _query builder_ **wajib** mengecualikan `coupon_id` yang sudah ada di tabel `winners`. Kupon dengan status `annulled` tidak boleh menang lagi, kupon tersebut dianggap hangus.
2.  **Validasi Kuota:** Jika kuota `prizes` sudah 0, API/Livewire method harus menolak proses pengundian.
3.  **Concurrency:** Karena dijalankan di _local network_ dan dioperasikan oleh 1-2 admin, tidak perlu menggunakan _queue_ yang kompleks. Transaksi database gunakan `DB::transaction` biasa saat menyimpan data pemenang untuk mencegah _race condition_ jika tombol terklik dua kali.

### 6. Development Phases (Suggested for Vibe Coding)

1.  **Phase 1:** Setup Laravel, SQLite, dan Migrations.
2.  **Phase 2:** Buat Seeder untuk _dummy prizes_ dan _dummy coupons_ (1.000 data).
3.  **Phase 3:** Bangun Livewire Component untuk halaman Admin Panel.
4.  **Phase 4:** Bangun Livewire Component untuk Display Panel dengan Alpine.js untuk animasi angka.
5.  **Phase 5:** Hubungkan sistem Event/Polling Livewire antara Admin dan Display.
