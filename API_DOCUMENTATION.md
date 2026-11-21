# Dokumentasi Spesifikasi REST API - Transport Application

Dokumentasi resmi untuk antarmuka pemrograman aplikasi (API) Transport Application berbasis CodeIgniter 4. Dokumentasi ini merinci seluruh endpoint yang tersedia, parameter yang diterima, format keluaran (response), kode status HTTP, serta contoh integrasi klien.

---

## 1. Informasi Umum

- **Base URL Pengembangan**: `http://localhost:8080`
- **Prefix Endpoint**: `/api/transport`
- **Format Pertukaran Data**: JSON (`application/json`)
- **Controller Terkait**: [`Transport.php`](file:///home/azure/client-server-project/transport-aplication/app/Controllers/Api/Transport.php)
- **Konfigurasi Rute**: [`Routes.php`](file:///home/azure/client-server-project/transport-aplication/app/Config/Routes.php)

### Standar Header HTTP
Klien disarankan untuk menyertakan header berikut pada setiap permintaan:
```http
Accept: application/json
```

---

## 2. Diagram Alur Endpoint Tarif

Endpoint `/api/transport/tarif` memproses pencarian rute, validasi, penghitungan jarak bola bumi (Haversine Formula), dan estimasi biaya berdasarkan data jadwal penyedia moda transportasi.

```mermaid
flowchart TD
    A["Request: GET /api/transport/tarif"] --> B{"Parameter Lengkap?"}
    B -- "Tidak / Kosong" --> C["404 Not Found: Parameter wajib diisi"]
    B -- "Ya: id_asal / asal & id_tujuan / tujuan" --> D["Cari Data Kota Asal & Tujuan di Database"]
    D --> E{"Kedua Kota Ditemukan?"}
    E -- "Tidak" --> F["404 Not Found: Kota tidak ditemukan"]
    E -- "Ya" --> G{"Kota Asal == Kota Tujuan?"}
    G -- "Ya" --> H["400 Bad Request: Kota asal dan tujuan tidak boleh sama"]
    G -- "Tidak" --> I["Hitung Jarak (Haversine Formula) dalam Km"]
    I --> J["Query Jadwal Tersedia Antar Kota"]
    J --> K["Hitung Durasi & Estimasi Tarif = Jarak * Tarif per Km"]
    K --> L["200 OK: Response Data Rute & Pilihan Transportasi"]
```

---

## 3. Daftar Rute API

| No | Method | Endpoint | Handler | Deskripsi |
| :--- | :--- | :--- | :--- | :--- |
| 1 | `GET` | `/api/transport/transports` | `Transport::index` | Mengambil seluruh master data moda transportasi |
| 2 | `GET` | `/api/transport/kota` | `Transport::kota` | Mengambil daftar seluruh kota terurut abjad (A-Z) |
| 3 | `GET` | `/api/transport/tarif` | `Transport::tarif` | Menghitung jarak antarkota dan rincian tarif perjalanan |

---

## 4. Spesifikasi Endpoint

### 4.1. Ambil Semua Moda Transportasi

Mengambil seluruh jenis moda transportasi publik yang terdaftar (contoh: Pesawat, Kereta Api, Bus, Travel).

- **Method**: `GET`
- **Path**: `/api/transport/transports`
- **Autentikasi**: *None*
- **Parameter**: *None*

#### Response Sukses (200 OK)
```json
{
  "status": "success",
  "data": [
    {
      "id": "1",
      "jenis": "Pesawat",
      "created_at": "2026-11-14 10:00:00",
      "updated_at": "2026-11-14 10:00:00"
    },
    {
      "id": "2",
      "jenis": "Kereta Api",
      "created_at": "2026-11-14 10:00:00",
      "updated_at": "2026-11-14 10:00:00"
    },
    {
      "id": "3",
      "jenis": "Bus",
      "created_at": "2026-11-14 10:00:00",
      "updated_at": "2026-11-14 10:00:00"
    }
  ]
}
```

---

### 4.2. Ambil Daftar Kota

Mengambil seluruh kota yang tercatat dalam sistem beserta informasi titik koordinat bumi (`latitude` & `longitude`). Digunakan untuk mengisi opsi dropdown pemilihan rute asal dan rute tujuan pada aplikasi front-end/mobile.

- **Method**: `GET`
- **Path**: `/api/transport/kota`
- **Autentikasi**: *None*
- **Parameter**: *None*

#### Response Sukses (200 OK)
```json
{
  "status": "success",
  "data": [
    {
      "id": "1",
      "nama_kota": "Bandung",
      "latitude": "-6.91746400",
      "longitude": "107.61912300",
      "created_at": "2026-11-14 10:00:00",
      "updated_at": "2026-11-14 10:00:00"
    },
    {
      "id": "2",
      "nama_kota": "Jakarta",
      "latitude": "-6.20876340",
      "longitude": "106.84559900",
      "created_at": "2026-11-14 10:00:00",
      "updated_at": "2026-11-14 10:00:00"
    },
    {
      "id": "3",
      "nama_kota": "Surabaya",
      "latitude": "-7.25747190",
      "longitude": "112.75208830",
      "created_at": "2026-11-14 10:00:00",
      "updated_at": "2026-11-14 10:00:00"
    }
  ]
}
```

---

### 4.3. Hitung Jarak dan Rincian Tarif

Menghitung jarak lurus geografis antar dua titik koordinat kota asal dan tujuan menggunakan rumus Haversine, lalu mengkalkulasikan estimasi tarif berdasarkan jadwal dan tarif per kilometer masing-masing operator transportasi.

- **Method**: `GET`
- **Path**: `/api/transport/tarif`
- **Autentikasi**: *None*

#### Parameter Permintaan (Query String)
Klien dapat memilih salah satu dari dua metode pengiriman parameter:

##### Pilihan A: Menggunakan ID Kota (Direkomendasikan)
| Parameter | Tipe Data | Wajib | Keterangan |
| :--- | :--- | :--- | :--- |
| `id_asal` | Integer | Ya* | ID unik kota asal (mengacu pada data tabel `kota`) |
| `id_tujuan` | Integer | Ya* | ID unik kota tujuan (mengacu pada data tabel `kota`) |

##### Pilihan B: Menggunakan Nama Kota
| Parameter | Tipe Data | Wajib | Keterangan |
| :--- | :--- | :--- | :--- |
| `asal` | String | Ya* | Nama kota asal (pencarian fuzzy / `LIKE %nama%`) |
| `tujuan` | String | Ya* | Nama kota tujuan (pencarian fuzzy / `LIKE %nama%`) |

> [!NOTE]
> Harus menyertakan pasangan `id_asal` & `id_tujuan` ATAU pasangan `asal` & `tujuan`. Jika parameter ID diisi, sistem akan memprioritaskan pencarian berdasarkan ID.

#### Contoh Permintaan
```http
GET /api/transport/tarif?id_asal=2&id_tujuan=1 HTTP/1.1
Host: localhost:8080
Accept: application/json
```
atau
```http
GET /api/transport/tarif?asal=Jakarta&tujuan=Bandung HTTP/1.1
Host: localhost:8080
Accept: application/json
```

#### Response Sukses (200 OK)
```json
{
  "status": "success",
  "data": {
    "rute": {
      "asal": {
        "id": 2,
        "nama_kota": "Jakarta",
        "latitude": -6.2087634,
        "longitude": 106.845599
      },
      "tujuan": {
        "id": 1,
        "nama_kota": "Bandung",
        "latitude": -6.917464,
        "longitude": 107.619123
      },
      "jarak_km": 118.87
    },
    "total_jadwal_tersedia": 2,
    "pilihan_transportasi": [
      {
        "id_jadwal": 1,
        "penyedia": "PT Kereta Api Indonesia",
        "jenis_transport": "Kereta Api",
        "durasi": "2 Jam 45 Menit",
        "tarif_per_km": 1200,
        "estimasi_tarif": 142600,
        "estimasi_tarif_format": "Rp 142.600"
      },
      {
        "id_jadwal": 2,
        "penyedia": "Cititrans",
        "jenis_transport": "Bus",
        "durasi": "3 Jam 15 Menit",
        "tarif_per_km": 1000,
        "estimasi_tarif": 118900,
        "estimasi_tarif_format": "Rp 118.900"
      }
    ]
  }
}
```

#### Response Error

##### 1. Kota Asal atau Kota Tujuan Tidak Ditemukan (404 Not Found)
Terjadi jika ID kota tidak ada atau nama kota tidak cocok dengan record manapun di tabel `kota`.
```json
{
  "status": 404,
  "error": 404,
  "messages": {
    "error": "Kota asal tidak ditemukan. Masukkan parameter \"id_asal\" atau \"asal\" yang valid."
  }
}
```

##### 2. Kota Asal dan Tujuan Bernilai Sama (400 Bad Request)
Terjadi jika nilai ID atau nama kota asal sama persis dengan kota tujuan.
```json
{
  "status": 400,
  "error": 400,
  "messages": {
    "error": "Kota asal dan kota tujuan tidak boleh sama."
  }
}
```

---

## 5. Rumus & Logika Perhitungan

### 5.1. Formula Haversine (Jarak Km)
Sistem menggunakan jari-jari rata-rata bumi $R = 6371\text{ km}$:

$$\Delta \text{lat} = \text{lat}_2 - \text{lat}_1$$
$$\Delta \text{lon} = \text{lon}_2 - \text{lon}_1$$
$$a = \sin^2\left(\frac{\Delta \text{lat}}{2}\right) + \cos(\text{lat}_1) \cdot \cos(\text{lat}_2) \cdot \sin^2\left(\frac{\Delta \text{lon}}{2}\right)$$
$$d = 2 \cdot R \cdot \arcsin(\sqrt{a})$$

Hasil $d$ dibulatkan hingga 2 angka di belakang koma.

### 5.2. Estimasi Tarif
$$\text{Estimasi Tarif} = \text{round}(\text{jarak\_km} \times \text{tarif\_per\_km}, -2)$$
Hasil dibulatkan ke ratusan rupiah terdekat.

---

## 6. Contoh Implementasi Klien

### cURL
```bash
# 1. Mendapatkan daftar kota
curl -X GET "http://localhost:8080/api/transport/kota" -H "Accept: application/json"

# 2. Menghitung tarif perjalanan
curl -X GET "http://localhost:8080/api/transport/tarif?id_asal=2&id_tujuan=1" -H "Accept: application/json"
```

### JavaScript (Fetch API)
```javascript
async function cekTarifTransportasi(idAsal, idTujuan) {
  const url = `http://localhost:8080/api/transport/tarif?id_asal=${idAsal}&id_tujuan=${idTujuan}`;
  
  try {
    const response = await fetch(url, {
      headers: { 'Accept': 'application/json' }
    });
    
    const result = await response.json();
    
    if (result.status === 'success') {
      console.log(`Jarak: ${result.data.rute.jarak_km} km`);
      console.log('Pilihan Armada:', result.data.pilihan_transportasi);
    } else {
      console.error('Error:', result.messages.error);
    }
  } catch (error) {
    console.error('Koneksi gagal:', error);
  }
}

// Pemanggilan contoh: Jakarta (ID: 2) ke Bandung (ID: 1)
cekTarifTransportasi(2, 1);
```
