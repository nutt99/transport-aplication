# Dokumentasi Spesifikasi REST API - Transport Application

Dokumentasi resmi antarmuka pemrograman aplikasi (API) Transport Application berbasis CodeIgniter 4. Dokumentasi ini merinci seluruh endpoint REST API (`/api/*`), endpoint BFF/Proxy Frontend (`/app-api/*`), mekanisme autentikasi API Key, konfigurasi CORS, format payload, serta skema response sukses & error.

---

## 1. Informasi Umum & Arsitektur

- **Base URL Server**: `http://localhost:8080` (atau sesuai `TRANSPORT_API_URL` di `.env`, misal `http://127.0.0.1:8082`)
- **Prefix REST API**: `/api` (dilindungi `X-API-KEY`)
- **Prefix Frontend Proxy (BFF)**: `/app-api` (tanpa `X-API-KEY` di browser; diteruskan oleh server via [`ApiClient.php`](file:///home/azure/client-server-project/transport-aplication/app/Libraries/ApiClient.php))
- **Format Pertukaran Data**: JSON (`application/json`)
- **Controller Terkait**:
  - Bisnis Rute & Rekomendasi: [`Transport.php`](file:///home/azure/client-server-project/transport-aplication/app/Controllers/Api/Transport.php)
  - CRUD Master Data: [`Moda.php`](file:///home/azure/client-server-project/transport-aplication/app/Controllers/Api/Moda.php), [`Lokasi.php`](file:///home/azure/client-server-project/transport-aplication/app/Controllers/Api/Lokasi.php), [`Penyedia.php`](file:///home/azure/client-server-project/transport-aplication/app/Controllers/Api/Penyedia.php), [`Jadwal.php`](file:///home/azure/client-server-project/transport-aplication/app/Controllers/Api/Jadwal.php)
  - Frontend API Proxy: [`FrontendApi.php`](file:///home/azure/client-server-project/transport-aplication/app/Controllers/FrontendApi.php)
- **Konfigurasi Rute & Filter**: [`Routes.php`](file:///home/azure/client-server-project/transport-aplication/app/Config/Routes.php), [`Filters.php`](file:///home/azure/client-server-project/transport-aplication/app/Config/Filters.php), [`Cors.php`](file:///home/azure/client-server-project/transport-aplication/app/Config/Cors.php)

---

## 2. Autentikasi & Keamanan (API Key & CORS)

Seluruh endpoint pada rute `/api/*` dilindungi oleh [`ApiKeyFilter`](file:///home/azure/client-server-project/transport-aplication/app/Filters/ApiKeyFilter.php) dan [`Cors`](file:///home/azure/client-server-project/transport-aplication/app/Config/Cors.php).

### 2.1. Header Permintaan (`/api/*`)
Setiap klien wajib menyertakan header `X-API-KEY`:
```http
X-API-KEY: <KUNCI_API_ANDA>
Accept: application/json
Content-Type: application/json
```

### 2.2. Level Hak Akses API Key

| Tipe Key | Variabel `.env` | Hak Akses Method | Penggunaan |
| :--- | :--- | :--- | :--- |
| **Public Key** | `API_KEY_PUBLIC` | `GET` saja | Aplikasi Publik, Frontend Web, Aplikasi Mobile pengguna |
| **Secret Key** | `API_KEY_SECRET` | `GET`, `POST`, `PUT`, `PATCH`, `DELETE` | Dashboard Admin (`LokasiController`, `TransportController`), Internal Server |

> [!WARNING]
> Permintaan non-`GET` (`POST`, `PUT`, `PATCH`, `DELETE`) menggunakan **Public Key** akan langsung ditolak dengan status **403 Forbidden**. Gunakan **Secret Key** untuk mutasi data.

### 2.3. Respons Terkait Autentikasi
- **401 Unauthorized (Header Tidak Disertakan)**:
  ```json
  {
    "status": 401,
    "error": "Unauthorized",
    "message": "Header X-API-KEY wajib disertakan."
  }
  ```
- **401 Unauthorized (Key Tidak Cocok)**:
  ```json
  {
    "status": 401,
    "error": "Unauthorized",
    "message": "API Key tidak valid."
  }
  ```
- **403 Forbidden (Public Key Melakukan Write/Delete)**:
  ```json
  {
    "status": 403,
    "error": "Forbidden",
    "message": "API Key publik hanya memiliki akses baca (GET)."
  }
  ```

---

## 3. Ringkasan Seluruh Rute API

### 3.1. Rute Khusus Transport & Rekomendasi (`/api/transport/*`)
| Method | Endpoint | Handler | Deskripsi |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/transport/transports` | `Transport::index` | Mengambil seluruh data jenis moda transportasi |
| `GET` | `/api/transport/lokasi` | `Transport::lokasi` | Mengambil seluruh data titik lokasi (Bandara, Stasiun, Terminal, dll.) |
| `GET` | `/api/transport/tarif` | `Transport::tarif` | Menghitung jarak dan tarif dengan filter jam berangkat |
| `GET`/`POST`| `/api/transport/rekomendasi-wisata` | `Transport::rekomendasiWisata` | Rekomendasi rute multi-moda (udara + darat) & estimasi biaya PP |

### 3.2. Rute Master Data (`/api/*` RESTful Resource)
| Resource | Method | Endpoint | Akses Key | Fungsi |
| :--- | :--- | :--- | :--- | :--- |
| **Moda** | `GET` | `/api/moda` | Public / Secret | Menampilkan daftar seluruh moda |
| | `GET` | `/api/moda/(:id)` | Public / Secret | Menampilkan detail 1 moda |
| | `POST` | `/api/moda` | **Secret Only** | Menambahkan moda baru |
| | `PUT`/`PATCH`| `/api/moda/(:id)` | **Secret Only** | Mengubah nama moda |
| | `DELETE`| `/api/moda/(:id)` | **Secret Only** | Menghapus moda |
| **Lokasi** | `GET` | `/api/lokasi` | Public / Secret | Menampilkan daftar titik lokasi (mendukung `?q=` & `?tipe=`) |
| | `GET` | `/api/lokasi/(:id)` | Public / Secret | Menampilkan detail 1 lokasi |
| | `POST` | `/api/lokasi` | **Secret Only** | Menambahkan lokasi baru (termasuk `tipe_lokasi`) |
| | `PUT`/`PATCH`| `/api/lokasi/(:id)` | **Secret Only** | Mengubah data lokasi / tipe / koordinat |
| | `DELETE`| `/api/lokasi/(:id)` | **Secret Only** | Menghapus lokasi |
| **Penyedia** | `GET` | `/api/penyedia` | Public / Secret | Menampilkan penyedia armada + join moda |
| | `GET` | `/api/penyedia/(:id)` | Public / Secret | Menampilkan detail penyedia |
| | `POST` | `/api/penyedia` | **Secret Only** | Menambahkan penyedia baru |
| | `PUT`/`PATCH`| `/api/penyedia/(:id)` | **Secret Only** | Mengubah data penyedia |
| | `DELETE`| `/api/penyedia/(:id)` | **Secret Only** | Menghapus data penyedia |
| **Jadwal** | `GET` | `/api/jadwal` | Public / Secret | Menampilkan seluruh jadwal rute |
| | `GET` | `/api/jadwal/(:id)` | Public / Secret | Menampilkan detail jadwal rute |
| | `POST` | `/api/jadwal` | **Secret Only** | Menambahkan jadwal rute baru |
| | `PUT`/`PATCH`| `/api/jadwal/(:id)` | **Secret Only** | Mengubah jam / tarif jadwal |
| | `DELETE`| `/api/jadwal/(:id)` | **Secret Only** | Menghapus jadwal rute |

### 3.3. Rute Frontend Proxy / BFF (`/app-api/*`)
Digunakan oleh halaman web publik ([`index.php`](file:///home/azure/client-server-project/transport-aplication/app/Views/frontend/index.php)) agar klien browser tidak perlu menyimpan `X-API-KEY` maupun terkena masalah CORS ke API eksternal JejakSumut.

| Method | Endpoint | Handler | Sumber Data |
| :--- | :--- | :--- | :--- |
| `GET` | `/app-api/wisata/destinasi` | `FrontendApi::destinasi` | API JejakSumut (`/destinasi`) |
| `GET` | `/app-api/wisata/destinasi/(:segment)` | `FrontendApi::destinasiDetail/$1` | API JejakSumut (`/destinasi/{slug}`) |
| `GET` | `/app-api/wisata/kategori` | `FrontendApi::kategori` | API JejakSumut (`/kategori`) |
| `GET` | `/app-api/wisata/kabupaten-kota` | `FrontendApi::kabupatenKota` | API JejakSumut (`/kabupaten-kota`) |
| `GET` | `/app-api/transport/transports` | `FrontendApi::transports` | Internal `/api/transport/transports` |
| `GET` | `/app-api/transport/lokasi` | `FrontendApi::lokasi` | Internal `/api/transport/lokasi` |
| `GET` | `/app-api/transport/tarif` | `FrontendApi::tarif` | Internal `/api/transport/tarif` |
| `GET` | `/app-api/transport/rekomendasi-wisata` | `FrontendApi::rekomendasiWisata` | Internal `/api/transport/rekomendasi-wisata` |

---

## 4. Spesifikasi Endpoint Rute & Layanan Transport

### 4.1. GET `/api/transport/transports`
Mengambil semua jenis moda transportasi.

**Response Sukses (200 OK):**
```json
{
  "status": "success",
  "data": [
    { "id": "1", "jenis": "Pesawat", "created_at": "2026-11-14 10:00:00", "updated_at": "2026-11-14 10:00:00" },
    { "id": "2", "jenis": "Kereta Api", "created_at": "2026-11-14 10:00:00", "updated_at": "2026-11-14 10:00:00" },
    { "id": "3", "jenis": "Bus", "created_at": "2026-11-14 10:00:00", "updated_at": "2026-11-14 10:00:00" }
  ]
}
```

---

### 4.2. GET `/api/transport/lokasi`
Mengambil seluruh titik simpul transportasi (Bandara, Stasiun, Terminal, Halte, Pelabuhan) terurut abjad (A-Z) beserta koordinat dan tipe lokasinya.

**Response Sukses (200 OK):**
```json
{
  "status": "success",
  "data": [
    {
      "id": "1",
      "nama_lokasi": "Bandara Internasional Kualanamu (KNO)",
      "tipe_lokasi": "bandara",
      "latitude": "3.64220000",
      "longitude": "98.88520000",
      "created_at": "2026-11-22 08:00:00",
      "updated_at": "2026-11-22 08:00:00"
    },
    {
      "id": "2",
      "nama_lokasi": "Stasiun Medan",
      "tipe_lokasi": "stasiun",
      "latitude": "3.59080000",
      "longitude": "98.67970000",
      "created_at": "2026-11-22 08:00:00",
      "updated_at": "2026-11-22 08:00:00"
    }
  ]
}
```

---

### 4.3. GET `/api/transport/tarif`
Menghitung jarak antar dua titik lokasi (Haversine Formula), mencocokkan jadwal yang tersedia, menyaring berdasarkan jam berangkat (jika diberikan), dan mengurutkan secara otomatis dari estimasi tarif termurah.

#### Parameter Query:
| Parameter | Tipe | Status | Keterangan |
| :--- | :--- | :--- | :--- |
| `id_asal` | Integer | Opsional* | ID lokasi asal (wajib jika tidak ada `asal`) |
| `id_tujuan` | Integer | Opsional* | ID lokasi tujuan (wajib jika tidak ada `tujuan`) |
| `asal` | String | Opsional* | Nama lokasi asal (wajib jika tidak ada `id_asal`) |
| `tujuan` | String | Opsional* | Nama lokasi tujuan (wajib jika tidak ada `id_tujuan`) |
| `jam` | String | Opsional | Format `HH:MM` (contoh: `08:30`). Memfilter jadwal yang sedang aktif beroperasi. |

#### Response Sukses (200 OK):
```json
{
  "status": "success",
  "data": {
    "rute": {
      "asal": {
        "id": 1,
        "nama_lokasi": "Bandara Internasional Kualanamu (KNO)",
        "latitude": 3.6422,
        "longitude": 98.8852
      },
      "tujuan": {
        "id": 2,
        "nama_lokasi": "Stasiun Medan",
        "latitude": 3.5908,
        "longitude": 98.6797
      },
      "jarak_km": 23.47,
      "jam_berangkat": "09:00"
    },
    "total_jadwal_tersedia": 1,
    "pilihan_transportasi": [
      {
        "id_jadwal": 1,
        "penyedia": "KA Bandara Railink",
        "jenis_transport": "Kereta Api",
        "jam_berlaku": "05:00 - 20:00",
        "tarif_per_km": 2100,
        "estimasi_tarif": 49300,
        "estimasi_tarif_format": "Rp 49.300"
      }
    ]
  }
}
```

---

### 4.4. GET/POST `/api/transport/rekomendasi-wisata`
Endpoint cerdas yang merekomendasikan rute multi-moda lengkap dari posisi GPS pengguna menuju objek wisata (difokuskan pada pariwisata Sumatera Utara / Danau Toba).

#### Alur Logika Sistem:
1. **Deteksi Pengguna di Luar Sumut** (`lat < 0.5 || lat > 4.3 || lng < 97.0 || lng > 100.5`):
   - Memilih bandara keberangkatan terdekat dari GPS user.
   - Menentukan bandara gerbang Sumut (`KNO` atau `DTB` Silangit) yang terdekat dengan objek wisata dan memiliki jadwal penerbangan.
   - **Segmen 1**: Penerbangan ke Sumut.
   - **Segmen 2**: Transportasi darat lanjutan menuju titik simpul terdekat objek wisata.
2. **Deteksi Pengguna di Dalam Sumut**:
   - Memilih stasiun/terminal terdekat dari GPS user, lalu merutekan perjalanan darat langsung ke titik terdekat objek wisata.
3. **Kalkulasi Trip Type**:
   - Mendukung `roundtrip` (Pulang-Pergi / default) dan `oneway` (Sekali Jalan).

#### Parameter (`GET` Query String atau `POST` JSON Body):
| Parameter | Tipe | Wajib | Keterangan |
| :--- | :--- | :--- | :--- |
| `user_lat` | Float | Ya | Latitude GPS posisi pengguna (contoh: `-6.2088`) |
| `user_lng` | Float | Ya | Longitude GPS posisi pengguna (contoh: `106.8456`) |
| `wisata_lat`| Float | Ya | Latitude lokasi objek wisata (contoh: `2.6136`) |
| `wisata_lng`| Float | Ya | Longitude lokasi objek wisata (contoh: `98.9083`) |
| `nama_wisata`| String | Tidak | Nama destinasi (default: `'Destinasi Wisata'`) |
| `trip_type` | String | Tidak | `'roundtrip'` (default) atau `'oneway'` |

#### Response Sukses (200 OK):
```json
{
  "status": "success",
  "data": {
    "metadata": {
      "nama_wisata": "Danau Toba Parapat",
      "koordinat_wisata": { "latitude": 2.6136, "longitude": 98.9083 },
      "koordinat_user": { "latitude": -6.2088, "longitude": 106.8456 },
      "user_di_luar_sumut": true,
      "trip_type": "roundtrip",
      "tipe_perjalanan": "Pulang-Pergi (PP)",
      "stasiun_terdekat_wisata": {
        "id": 8,
        "nama_lokasi": "Bandara Internasional Silangit (DTB)",
        "latitude": 2.2606,
        "longitude": 98.9939,
        "jarak_ke_objek_wisata_km": 40.42
      }
    },
    "ringkasan_biaya": {
      "biaya_pergi": 1050000,
      "biaya_pergi_format": "Rp 1.050.000",
      "biaya_pulang": 1050000,
      "biaya_pulang_format": "Rp 1.050.000",
      "total_biaya_estimasi": 2100000,
      "total_biaya_format": "Rp 2.100.000",
      "keterangan": "Estimasi menggunakan opsi armada termurah untuk setiap segmen rute."
    },
    "rute_perjalanan": {
      "pergi": [
        {
          "segmen": 1,
          "jenis_segmen": "Penerbangan",
          "deskripsi": "Penerbangan dari bandara terdekat user menuju gerbang Sumatera Utara",
          "titik_berangkat": {
            "id": 9,
            "nama_lokasi": "Bandara Internasional Soekarno-Hatta (CGK)",
            "latitude": -6.1275,
            "longitude": 106.6537,
            "jarak_dari_user_km": 23.1
          },
          "titik_tiba": {
            "id": 8,
            "nama_lokasi": "Bandara Internasional Silangit (DTB)",
            "latitude": 2.2606,
            "longitude": 98.9939
          },
          "jarak_km": 1285.5,
          "tarif_termurah": 950000,
          "tarif_termurah_format": "Rp 950.000",
          "opsi_armada": [
            {
              "id_jadwal": 12,
              "penyedia": "Batik Air",
              "jenis_transport": "Pesawat",
              "jam_berlaku": "07:00 - 09:30",
              "estimasi_tarif": 950000,
              "estimasi_tarif_format": "Rp 950.000"
            }
          ]
        }
      ],
      "pulang": [
        {
          "segmen": 1,
          "jenis_segmen": "Penerbangan",
          "deskripsi": "Perjalanan pulang: Bandara Internasional Silangit (DTB) kembali menuju Bandara Internasional Soekarno-Hatta (CGK)",
          "tarif_termurah": 950000,
          "tarif_termurah_format": "Rp 950.000"
        }
      ]
    }
  }
}
```

---

## 5. Spesifikasi Master Data (Resource CRUD)

> [!IMPORTANT]
> Operasi `POST`, `PUT`, `PATCH`, dan `DELETE` memerlukan `X-API-KEY` dengan nilai **Secret Key**.

### 5.1. Resource: Moda (`/api/moda`)
Mengelola data moda transportasi (`transport`).

| Method | Endpoint | Body (JSON) | Validasi | Response |
| :--- | :--- | :--- | :--- | :--- |
| `GET` | `/api/moda` | - | - | 200 OK (List data) |
| `GET` | `/api/moda/(:id)` | - | - | 200 OK / 404 Not Found |
| `POST` | `/api/moda` | `{"jenis": "Kapal Feri"}` | `jenis`: required, 2-50 chars | 201 Created |
| `PUT` | `/api/moda/(:id)` | `{"jenis": "Kapal Cepat"}` | `jenis`: min 2 chars | 200 OK |
| `DELETE`| `/api/moda/(:id)` | - | Gagal jika dipakai penyedia | 200 OK / 409 Conflict |

---

### 5.2. Resource: Lokasi (`/api/lokasi`)
Mengelola titik lokasi simpul transportasi (`lokasi`).

- **Query Filter pada `GET /api/lokasi`**:
  - `q` (opsional, string): Pencarian teks pada `nama_lokasi` (case-insensitive).
  - `tipe` (opsional, string): Filter berdasarkan tipe (`bandara`, `stasiun`, `terminal`, `halte`, `pelabuhan`).
- **Skema Body (`POST` / `PUT` / `PATCH`)**:
```json
{
  "nama_lokasi": "Stasiun Tebing Tinggi",
  "tipe_lokasi": "stasiun",
  "latitude": 3.3276,
  "longitude": 99.1625
}
```

| Method | Endpoint | Keterangan & Validasi | Status Sukses / Error |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/lokasi` | Menampilkan lokasi terurut `nama_lokasi` A-Z (dukung `?q=` & `?tipe=`) | 200 OK |
| `GET` | `/api/lokasi/(:id)` | Detail lokasi berdasarkan ID | 200 OK / 404 |
| `POST` | `/api/lokasi` | `nama_lokasi` (2-100 char), `latitude` (decimal), `longitude` (decimal), `tipe_lokasi` (opsional, default `'terminal'`) | 201 Created |
| `PUT` | `/api/lokasi/(:id)` | Memperbarui nama, tipe, atau koordinat lokasi | 200 OK / 404 |
| `DELETE`| `/api/lokasi/(:id)` | Cek relasi ke `jadwal` (`id_lokasi_asal` / `id_lokasi_tujuan`) | 200 OK / 409 Conflict |

---

### 5.3. Resource: Penyedia (`/api/penyedia`)
Mengelola data operator atau perusahaan penyedia layanan transportasi (`penyedia`).

- **Skema Body**:
```json
{
  "id_transport": 2,
  "nama_penyedia": "PT Kereta Api Indonesia (Divre I Sumut)",
  "latitude": 3.5908,
  "longitude": 98.6797
}
```

| Method | Endpoint | Keterangan & Validasi | Status Sukses / Error |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/penyedia` | Otomatis join `jenis_transport` dari tabel `transport` | 200 OK |
| `GET` | `/api/penyedia/(:id)` | Detail penyedia armada | 200 OK / 404 |
| `POST` | `/api/penyedia` | `id_transport` (valid ID di tabel `transport`), `nama_penyedia` (2-150 char) | 201 Created |
| `PUT` | `/api/penyedia/(:id)` | Update info penyedia armada | 200 OK / 404 |
| `DELETE`| `/api/penyedia/(:id)` | Gagal 409 jika masih terhubung ke jadwal | 200 OK / 409 Conflict |

---

### 5.4. Resource: Jadwal (`/api/jadwal`)
Mengelola jadwal operasional rute dan tarif per km (`jadwal`).

- **Skema Body**:
```json
{
  "id_penyedia": 1,
  "id_lokasi_asal": 1,
  "id_lokasi_tujuan": 2,
  "start_hours": "08:00:00",
  "ended_hours": "09:30:00",
  "tarif_per_km": 1500
}
```

| Method | Endpoint | Keterangan & Validasi | Status Sukses / Error |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/jadwal` | Menampilkan jadwal lengkap join penyedia & lokasi rute | 200 OK |
| `GET` | `/api/jadwal/(:id)` | Detail 1 jadwal | 200 OK / 404 |
| `POST` | `/api/jadwal` | Validasi: `id_penyedia`, `id_lokasi_asal` != `id_lokasi_tujuan`, `tarif_per_km` | 201 Created |
| `PUT` | `/api/jadwal/(:id)` | Update jadwal operasional atau tarif per km | 200 OK / 404 |
| `DELETE`| `/api/jadwal/(:id)` | Hapus jadwal | 200 OK / 404 |

---

## 6. Spesifikasi Frontend Proxy (`/app-api/*`)

Endpoint ini berjalan di domain utama aplikasi web untuk dikonsumsi langsung oleh JavaScript di halaman frontend tanpa mengekspos API Key rahasia ke browser.

### 6.1. Wisata JejakSumut (`/app-api/wisata/*`)
- **`GET /app-api/wisata/destinasi`**
  - Query params opsional yang diteruskan: `kota`, `kategori`, `kode_kabupaten`, `harga_maksimal`, `kata_kunci`, `urutan`, `halaman`, `batas`.
- **`GET /app-api/wisata/destinasi/(:segment)`**
  - Mengambil detail destinasi berdasarkan slug.
- **`GET /app-api/wisata/kategori`**
  - Mengambil daftar kategori wisata.
- **`GET /app-api/wisata/kabupaten-kota`**
  - Mengambil daftar kabupaten/kota dari API JejakSumut.

### 6.2. Transportasi Internal (`/app-api/transport/*`)
- **`GET /app-api/transport/transports`** → Proxy ke `GET /api/transport/transports`
- **`GET /app-api/transport/lokasi`** → Proxy ke `GET /api/transport/lokasi`
- **`GET /app-api/transport/tarif`** → Proxy ke `GET /api/transport/tarif` (query: `id_asal`, `id_tujuan`, `asal`, `tujuan`, `jam`)
- **`GET /app-api/transport/rekomendasi-wisata`** → Proxy ke `GET /api/transport/rekomendasi-wisata` (query: `user_lat`, `user_lng`, `wisata_lat`, `wisata_lng`, `nama_wisata`, `trip_type`)

---

## 7. Contoh Pemanggilan Klien

### 7.1. cURL

```bash
# 1. Mengambil Lokasi dengan filter tipe (Public Key)
curl -X GET "http://127.0.0.1:8082/api/lokasi?tipe=bandara" \
  -H "X-API-KEY: public-transport-read-2026" \
  -H "Accept: application/json"

# 2. Cek Tarif dengan Jam Berangkat (Public Key)
curl -X GET "http://127.0.0.1:8082/api/transport/tarif?id_asal=1&id_tujuan=2&jam=08:30" \
  -H "X-API-KEY: public-transport-read-2026" \
  -H "Accept: application/json"

# 3. Menambahkan Lokasi Baru lengkap dengan tipe_lokasi (Wajib Secret Key)
curl -X POST "http://127.0.0.1:8082/api/lokasi" \
  -H "X-API-KEY: secret-transport-admin-9f8a7b6c" \
  -H "Content-Type: application/json" \
  -d '{"nama_lokasi": "Terminal Amplas Medan", "tipe_lokasi": "terminal", "latitude": 3.5353, "longitude": 98.7058}'
```

### 7.2. JavaScript (Fetch API via Frontend Proxy)

```javascript
// Memanggil melalui /app-api agar tidak perlu menyertakan X-API-KEY di browser
async function cekRekomendasiWisata(userCoords, wisataCoords, namaDestinasi) {
  const params = new URLSearchParams({
    user_lat: userCoords.latitude,
    user_lng: userCoords.longitude,
    wisata_lat: wisataCoords.latitude,
    wisata_lng: wisataCoords.longitude,
    nama_wisata: namaDestinasi,
    trip_type: 'roundtrip'
  });

  const response = await fetch(`/app-api/transport/rekomendasi-wisata?${params}`);
  const result = await response.json();

  if (response.ok) {
    console.log('Estimasi Biaya PP:', result.data.ringkasan_biaya.total_biaya_format);
  } else {
    console.error('Error:', result.message || result.messages);
  }
}
```
