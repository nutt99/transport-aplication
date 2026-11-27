# Transport Application — Backend & REST API Platform

Platform backend berbasis **CodeIgniter 4 (PHP)** yang menyediakan layanan web portal serta antarmuka **REST API** untuk sistem transportasi terintegrasi dan konektivitas pariwisata di Sumatera Utara.

Aplikasi ini mengintegrasikan data simpul transit (Bandara, Stasiun Kereta Api, Terminal Bus), jadwal operasional armada, perhitungan jarak & tarif otomatis, serta rekomendasi rute multi-moda cerdas yang terhubung dengan API pariwisata [JejakSumut](https://api-jejaksumut.vulnera.web.id).

---

## 📌 Fitur Utama

1. **Smart Multi-Moda Trip Recommendation (`/api/transport/rekomendasi-wisata`)**:
   - Mendeteksi titik koordinat pengguna (apakah di dalam atau di luar wilayah Sumatera Utara).
   - **Luar Sumut** (misal: Jakarta, Surabaya): Sistem secara otomatis mencarikan bandara terdekat pengguna $\rightarrow$ penerbangan ke gerbang Sumut (Bandara Kualanamu `KNO` atau Silangit `DTB`) $\rightarrow$ dilanjutkan transportasi darat (Kereta Api / Bus DAMRI / Shuttle) langsung ke lokasi wisata.
   - **Dalam Sumut**: Merutekan langsung simpul stasiun/terminal terdekat menuju objek wisata.
   - Menghitung estimasi rute **Pulang-Pergi (Roundtrip / PP)** atau **Sekali Jalan (Oneway)** lengkap dengan total biaya perjalanan termurah.

2. **Kalkulator Jarak & Tarif Antar Titik (`/api/transport/tarif`)**:
   - Menghitung jarak geodesic antar simpul simetris (menggunakan formula *Haversine* berbasis koordinat latitude & longitude).
   - Filter jam keberangkatan aktif (mencocokkan jadwal operasional armada).
   - Mengurutkan pilihan armada secara otomatis dari estimasi tarif termurah.

3. **Master Data Transportasi (CRUD Resource)**:
   - **Moda Transportasi**: Pesawat Terbang, Kereta Api, Bus, Shuttle.
   - **Titik Lokasi Simpul**: Bandara Internasional Kualanamu, Bandara Internasional Silangit, Stasiun Medan, Terminal Sosor Saba Parapat, dll.
   - **Penyedia Armada**: Garuda Indonesia, Batik Air, PT KAI, KA Bandara Railink, DAMRI, dll.
   - **Jadwal & Tarif**: Tarif per kilometer dan rentang jam aktif operasional.

4. **Integrasi Data Pariwisata (Proxy JejakSumut)**:
   - Katalog objek wisata alam, sejarah, budaya, dan religi di Sumatera Utara.
   - Proxy aman untuk filter kategori, kabupaten/kota, dan harga tiket masuk.

---

## 🛠️ Arsitektur & Teknologi

- **Framework**: CodeIgniter 4 (PHP 8.2+)
- **Database**: PostgreSQL / MySQL
- **Frontend Web**: HTML5, Modern CSS, Vanilla JS, & Leaflet.js Interactive Map
- **Keamanan API**: API Key Authentication (`X-API-KEY`) & Cross-Origin Resource Sharing (CORS)

---

## 🔐 Autentikasi & Level Hak Akses API

Seluruh endpoint pada rute `/api/*` dilindungi oleh `ApiKeyFilter`. Setiap permintaan klien wajib menyertakan header `X-API-KEY`:

| Tipe API Key | Default Header Value | Method Diizinkan | Penggunaan |
| :--- | :--- | :--- | :--- |
| **Public Key** | `public-transport-read-2026` | `GET` saja | Aplikasi Publik, Klien Mobile Android, Web Frontend |
| **Secret Key** | `secret-transport-admin-9f8a7b6c` | `GET`, `POST`, `PUT`, `PATCH`, `DELETE` | Dashboard Admin & Manajemen Data |

> [!WARNING]
> Permintaan non-`GET` (`POST`, `PUT`, `DELETE`) menggunakan **Public Key** akan langsung ditolak dengan respons status **403 Forbidden**.

---

## 🌐 Dokumentasi Endpoint REST API

Dokumentasi spesifikasi lengkap payload dan format JSON dapat dibaca di [API_DOCUMENTATION.md](API_DOCUMENTATION.md).

### Ringkasan Rute Utama:
* `GET  /api/transport/transports` — Daftar jenis moda transportasi.
* `GET  /api/transport/lokasi` — Titik simpul transit terurut A-Z beserta koordinat GPS.
* `GET  /api/transport/tarif?id_asal=...&id_tujuan=...&jam=08:30` — Hitung jarak & tarif armada aktif.
* `POST /api/transport/rekomendasi-wisata` — Rekomendasi rute multi-moda dari GPS user ke objek wisata.
* `GET  /api/moda`, `POST /api/moda`, `PUT /api/moda/(:id)`, `DELETE /api/moda/(:id)` — CRUD Moda.
* `GET  /api/lokasi`, `POST /api/lokasi`, `PUT /api/lokasi/(:id)`, `DELETE /api/lokasi/(:id)` — CRUD Lokasi.
* `GET  /api/penyedia`, `POST /api/penyedia`, `PUT /api/penyedia/(:id)`, `DELETE /api/penyedia/(:id)` — CRUD Penyedia.
* `GET  /api/jadwal`, `POST /api/jadwal`, `PUT /api/jadwal/(:id)`, `DELETE /api/jadwal/(:id)` — CRUD Jadwal.

---

## 🚀 Panduan Menjalankan Server Lokal

### 1. Kebutuhan Sistem
* PHP 8.2 atau lebih baru (dengan ekstensi `intl`, `mbstring`, `curl`, `pdo_pgsql` atau `mysqli`).
* PostgreSQL (default) atau MySQL.
* Composer.

### 2. Langkah Setup
1. Masuk ke direktori:
   ```bash
   cd transport-aplication
   ```
2. Salin environment file dan sesuaikan kredensial database:
   ```bash
   cp env .env
   ```
3. Konfigurasi database di `.env`:
   ```env
   CI_ENVIRONMENT = development
   database.default.hostname = 127.0.0.1
   database.default.database = transport_aplication
   database.default.username = postgres
   database.default.password = postgres
   database.default.DBDriver = Postgre
   database.default.port = 5432
   ```
4. Jalankan migrasi dan seeder database:
   ```bash
   php spark migrate
   php spark db:seed DatabaseSeeder
   ```
5. Nyalakan development server:
   ```bash
   php spark serve --port 8080
   ```
   Web portal dapat diakses di `http://localhost:8080` dan REST API di `http://localhost:8080/api`.

---

## 📱 Klien Terkait

Aplikasi mobile Android yang terhubung ke backend ini tersedia di folder:
* **[transport_aplication_android](../transport_aplication_android)** — Klien mobile berbasis Flutter.
