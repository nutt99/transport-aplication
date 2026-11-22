# Dokumentasi Spesifikasi REST API - Transport Application

Dokumentasi resmi antarmuka pemrograman aplikasi (API) Transport Application berbasis CodeIgniter 4. Dokumentasi ini merinci seluruh endpoint, mekanisme autentikasi API Key, konfigurasi CORS, format payload, skema response sukses & error, serta contoh implementasi klien.

---

## 1. Informasi Umum & Arsitektur

- **Base URL Pengembangan**: `http://localhost:8080`
- **Prefix Endpoint**: `/api`
- **Format Pertukaran Data**: JSON (`application/json`)
- **Controller Terkait**:
  - Bisnis Rute & Rekomendasi: [`Transport.php`](file:///home/azure/client-server-project/transport-aplication/app/Controllers/Api/Transport.php)
  - CRUD Master Data: [`Moda.php`](file:///home/azure/client-server-project/transport-aplication/app/Controllers/Api/Moda.php), [`Lokasi.php`](file:///home/azure/client-server-project/transport-aplication/app/Controllers/Api/Lokasi.php), [`Penyedia.php`](file:///home/azure/client-server-project/transport-aplication/app/Controllers/Api/Penyedia.php), [`Jadwal.php`](file:///home/azure/client-server-project/transport-aplication/app/Controllers/Api/Jadwal.php)
- **Konfigurasi Rute & Filter**: [`Routes.php`](file:///home/azure/client-server-project/transport-aplication/app/Config/Routes.php), [`Filters.php`](file:///home/azure/client-server-project/transport-aplication/app/Config/Filters.php), [`Cors.php`](file:///home/azure/client-server-project/transport-aplication/app/Config/Cors.php)

---

## 2. Autentikasi & Keamanan (API Key & CORS)

Seluruh endpoint pada rute `/api/*` dilindungi oleh [`ApiKeyFilter`](file:///home/azure/client-server-project/transport-aplication/app/Filters/ApiKeyFilter.php) dan [`Cors`](file:///home/azure/client-server-project/transport-aplication/app/Config/Cors.php).

### 2.1. Header Permintaan
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
| **Secret Key** | `API_KEY_SECRET` | `GET`, `POST`, `PUT`, `PATCH`, `DELETE` | Dashboard Admin, Internal Server, Integrasi Backend |

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

### 3.1. Rute Khusus Transport & Rekomendasi
| Method | Endpoint | Handler | Deskripsi |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/transport/transports` | `Transport::index` | Mengambil seluruh data jenis moda transportasi |
| `GET` | `/api/transport/lokasi` | `Transport::lokasi` | Mengambil seluruh data titik lokasi (Bandara, Stasiun, Terminal) |
| `GET` | `/api/transport/tarif` | `Transport::tarif` | Menghitung jarak, durasi, dan tarif dengan filter jam berangkat |
| `GET`/`POST`| `/api/transport/rekomendasi-wisata` | `Transport::rekomendasiWisata` | Rekomendasi rute multi-moda (udara + darat) & estimasi biaya PP |

### 3.2. Rute Master Data (RESTful Resource)
| Resource | Method | Endpoint | Akses Key | Fungsi |
| :--- | :--- | :--- | :--- | :--- |
| **Moda** | `GET` | `/api/moda` | Public / Secret | Menampilkan daftar seluruh moda |
| | `GET` | `/api/moda/(:id)` | Public / Secret | Menampilkan detail 1 moda |
| | `POST` | `/api/moda` | **Secret Only** | Menambahkan moda baru |
| | `PUT`/`PATCH`| `/api/moda/(:id)` | **Secret Only** | Mengubah nama moda |
| | `DELETE`| `/api/moda/(:id)` | **Secret Only** | Menghapus moda |
| **Lokasi** | `GET` | `/api/lokasi` | Public / Secret | Menampilkan daftar titik lokasi |
| | `GET` | `/api/lokasi/(:id)` | Public / Secret | Menampilkan detail 1 lokasi |
| | `POST` | `/api/lokasi` | **Secret Only** | Menambahkan lokasi baru |
| | `PUT`/`PATCH`| `/api/lokasi/(:id)` | **Secret Only** | Mengubah data lokasi / koordinat |
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
Mengambil seluruh titik simpul transportasi (Bandara, Stasiun, Terminal) terurut abjad (A-Z) beserta titik koordinatnya.

**Response Sukses (200 OK):**
```json
{
  "status": "success",
  "data": [
    {
      "id": "1",
      "nama_lokasi": "Bandara Internasional Kualanamu (KNO)",
      "latitude": "3.64220000",
      "longitude": "98.88520000",
      "created_at": "2026-11-22 08:00:00",
      "updated_at": "2026-11-22 08:00:00"
    },
    {
      "id": "2",
      "nama_lokasi": "Stasiun Medan",
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
Menghitung jarak antar dua titik lokasi, mencocokkan jadwal yang tersedia, menyaring berdasarkan jam berangkat (jika diberikan), dan mengurutkan secara otomatis dari estimasi tarif termurah.

#### Parameter Query:
| Parameter | Tipe | Status | Keterangan |
| :--- | :--- | :--- | :--- |
| `id_asal` | Integer | Opsional* | ID lokasi asal (wajib jika tidak ada `asal`) |
| `id_tujuan` | Integer | Opsional* | ID lokasi tujuan (wajib jika tidak ada `tujuan`) |
| `asal` | String | Opsional* | Nama lokasi asal (wajib jika tidak ada `id_asal`) |
| `tujuan` | String | Opsional* | Nama lokasi tujuan (wajib jika tidak ada `id_tujuan`) |
| `jam` | String | Opsional | Format `HH:MM` (contoh: `08:30`). Memfilter jadwal yang sedang aktif beroperasi. |

#### Contoh Request:
```http
GET /api/transport/tarif?id_asal=1&id_tujuan=2&jam=09:00 HTTP/1.1
Host: localhost:8080
X-API-KEY: public-transport-read-2026
```

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
Endpoint pintar yang merekomendasikan rute multi-moda lengkap dari posisi user menuju lokasi wisata (difokuskan pada pariwisata Sumatera Utara / Danau Toba).

#### Alur Logika Sistem:
1. **Deteksi Pengguna di Luar Sumut**:
   - Jika pengguna berada di luar wilayah koordinat Sumut (misal Jakarta, Surabaya, dll.), sistem otomatis memilihkan **bandara keberangkatan terdekat dari GPS user**.
   - Sistem menentukan bandara kedatangan gerbang Sumut (Kualanamu `KNO` atau Silangit `DTB`) yang paling dekat dengan objek wisata dan memiliki penerbangan langsung.
   - **Leg 1**: Penerbangan ke Sumut.
   - **Leg 2**: Transportasi darat lanjutan (KA / Bus / Shuttle) menuju titik simpul terdekat dari objek wisata.
2. **Deteksi Pengguna di Dalam Sumut**:
   - Sistem memilih stasiun / terminal terdekat dari GPS user, lalu merutekan perjalanan darat langsung ke titik terdekat objek wisata.
3. **Kalkulasi Trip Type**:
   - Mendukung opsi `roundtrip` (Pulang-Pergi / default) dan `oneway` (Sekali Jalan).

#### Parameter:
Dapat dikirim melalui **Query String** (pada `GET`) atau **JSON Body** (pada `POST`):
| Parameter | Tipe | Wajib | Keterangan |
| :--- | :--- | :--- | :--- |
| `user_lat` | Float | Ya | Latitude GPS posisi pengguna (contoh: `-6.2088`) |
| `user_lng` | Float | Ya | Longitude GPS posisi pengguna (contoh: `106.8456`) |
| `wisata_lat`| Float | Ya | Latitude lokasi objek wisata (contoh: `2.6136`) |
| `wisata_lng`| Float | Ya | Longitude lokasi objek wisata (contoh: `98.9083`) |
| `nama_wisata`| String | Tidak | Nama destinasi (contoh: `Danau Toba / Parapat`) |
| `trip_type` | String | Tidak | Opsi: `'roundtrip'` (default) atau `'oneway'` |

#### Contoh Request:
```http
POST /api/transport/rekomendasi-wisata HTTP/1.1
Host: localhost:8080
X-API-KEY: public-transport-read-2026
Content-Type: application/json

{
  "user_lat": -6.2088,
  "user_lng": 106.8456,
  "wisata_lat": 2.6136,
  "wisata_lng": 98.9083,
  "nama_wisata": "Danau Toba Parapat",
  "trip_type": "roundtrip"
}
```

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
          "deskripsi": "Penerbangan dari bandara terdekat user (Bandara Internasional Soekarno-Hatta) menuju gerbang Sumatera Utara (Bandara Internasional Silangit (DTB))",
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
        },
        {
          "segmen": 2,
          "jenis_segmen": "Transportasi Darat",
          "deskripsi": "Perjalanan darat dari bandara kedatangan menuju titik transportasi terdekat dari objek wisata",
          "titik_berangkat": { "id": 8, "nama_lokasi": "Bandara Internasional Silangit (DTB)" },
          "titik_tiba": { "id": 14, "nama_lokasi": "Terminal Sosor Saba Parapat" },
          "jarak_km": 72.3,
          "jarak_ke_lokasi_wisata_km": 1.5,
          "tarif_termurah": 100000,
          "tarif_termurah_format": "Rp 100.000",
          "opsi_armada": [
            {
              "id_jadwal": 22,
              "penyedia": "Bus DAMRI Bandara Silangit - Parapat",
              "jenis_transport": "Bus",
              "jam_berlaku": "09:00 - 18:00",
              "estimasi_tarif": 100000,
              "estimasi_tarif_format": "Rp 100.000"
            }
          ]
        }
      ],
      "pulang": [
        {
          "segmen": 1,
          "jenis_segmen": "Transportasi Darat",
          "deskripsi": "Perjalanan pulang: Terminal Sosor Saba Parapat kembali menuju Bandara Internasional Silangit (DTB)",
          "tarif_termurah": 100000,
          "tarif_termurah_format": "Rp 100.000"
        },
        {
          "segmen": 2,
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
Mengelola data moda transportasi (Pesawat, Kereta Api, Bus, Shuttle).

| Method | Endpoint | Body (JSON) | Validasi | Response |
| :--- | :--- | :--- | :--- | :--- |
| `GET` | `/api/moda` | - | - | 200 OK (List data) |
| `GET` | `/api/moda/(:id)` | - | - | 200 OK / 404 Not Found |
| `POST` | `/api/moda` | `{"jenis": "Kapal Feri"}` | `jenis`: required, 2-50 chars | 201 Created |
| `PUT` | `/api/moda/(:id)` | `{"jenis": "Kapal Cepat"}` | `jenis`: min 2 chars | 200 OK |
| `DELETE`| `/api/moda/(:id)` | - | Tidak boleh ada penyedia yang menggunakan | 200 OK / 409 Conflict |

---

### 5.2. Resource: Lokasi (`/api/lokasi`)
Mengelola titik lokasi simpul transportasi (Bandara, Terminal, Stasiun).

- **Skema Body**:
```json
{
  "nama_lokasi": "Stasiun Tebing Tinggi",
  "latitude": 3.3276,
  "longitude": 99.1625
}
```

| Method | Endpoint | Keterangan & Validasi | Status Sukses |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/lokasi` | Menampilkan seluruh lokasi terurut nama A-Z | 200 OK |
| `GET` | `/api/lokasi/(:id)` | Detail lokasi berdasarkan ID | 200 OK / 404 |
| `POST` | `/api/lokasi` | `nama_lokasi` (2-100 char), `latitude` (decimal), `longitude` (decimal) | 201 Created |
| `PUT` | `/api/lokasi/(:id)` | Memperbarui nama atau koordinat lokasi | 200 OK |
| `DELETE`| `/api/lokasi/(:id)` | Hapus lokasi (gagal 409 jika terhubung ke jadwal) | 200 OK |

---

### 5.3. Resource: Penyedia (`/api/penyedia`)
Mengelola data operator atau perusahaan penyedia layanan transportasi (PT KAI, Garuda Indonesia, DAMRI, dll.).

- **Skema Body**:
```json
{
  "id_transport": 2,
  "nama_penyedia": "PT Kereta Api Indonesia (Divre I Sumut)",
  "latitude": 3.5908,
  "longitude": 98.6797
}
```

| Method | Endpoint | Keterangan & Validasi | Status Sukses |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/penyedia` | Otomatis me-join nama jenis moda transportasi | 200 OK |
| `GET` | `/api/penyedia/(:id)` | Detail penyedia armada | 200 OK / 404 |
| `POST` | `/api/penyedia` | `id_transport` (valid ID di tabel transport), `nama_penyedia` (2-150 char) | 201 Created |
| `PUT` | `/api/penyedia/(:id)` | Update info penyedia armada | 200 OK |
| `DELETE`| `/api/penyedia/(:id)` | Hapus data penyedia (gagal 409 jika terhubung jadwal) | 200 OK |

---

### 5.4. Resource: Jadwal (`/api/jadwal`)
Mengelola jadwal operasional rute dan tarif per km.

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

| Method | Endpoint | Keterangan & Validasi | Status Sukses |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/jadwal` | Menampilkan jadwal lengkap join penyedia & lokasi rute | 200 OK |
| `GET` | `/api/jadwal/(:id)` | Detail 1 jadwal | 200 OK / 404 |
| `POST` | `/api/jadwal` | Validasi: `id_penyedia`, `id_lokasi_asal` != `id_lokasi_tujuan`, `tarif_per_km` | 201 Created |
| `PUT` | `/api/jadwal/(:id)` | Update jadwal operasional atau tarif per km | 200 OK |
| `DELETE`| `/api/jadwal/(:id)` | Hapus jadwal | 200 OK |

---

## 6. Contoh Pemanggilan Klien

### 6.1. cURL

```bash
# 1. Mengambil Lokasi (Public Key)
curl -X GET "http://localhost:8080/api/transport/lokasi" \
  -H "X-API-KEY: public-transport-read-2026" \
  -H "Accept: application/json"

# 2. Cek Tarif dengan Jam Berangkat (Public Key)
curl -X GET "http://localhost:8080/api/transport/tarif?id_asal=1&id_tujuan=2&jam=08:30" \
  -H "X-API-KEY: public-transport-read-2026" \
  -H "Accept: application/json"

# 3. Menambahkan Lokasi Baru (Wajib Secret Key)
curl -X POST "http://localhost:8080/api/lokasi" \
  -H "X-API-KEY: secret-transport-admin-9f8a7b6c" \
  -H "Content-Type: application/json" \
  -d '{"nama_lokasi": "Terminal Amplas Medan", "latitude": 3.5353, "longitude": 98.7058}'
```

### 6.2. JavaScript (Fetch API)

```javascript
const BASE_URL = 'http://localhost:8080/api';
const API_KEY_PUBLIC = 'public-transport-read-2026';

// Permintaan Rekomendasi Wisata
async function dapatkanRekomendasiWisata(userCoords, wisataCoords, namaDestinasi) {
  try {
    const response = await fetch(`${BASE_URL}/transport/rekomendasi-wisata`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-API-KEY': API_KEY_PUBLIC,
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        user_lat: userCoords.latitude,
        user_lng: userCoords.longitude,
        wisata_lat: wisataCoords.latitude,
        wisata_lng: wisataCoords.longitude,
        nama_wisata: namaDestinasi,
        trip_type: 'roundtrip'
      })
    });

    const data = await response.json();
    if (response.ok) {
      console.log('Estimasi Biaya PP:', data.data.ringkasan_biaya.total_biaya_format);
      console.log('Detail Rute:', data.data.rute_perjalanan);
    } else {
      console.error('API Error:', data.message || data.messages);
    }
  } catch (error) {
    console.error('Koneksi Gagal:', error);
  }
}
```
