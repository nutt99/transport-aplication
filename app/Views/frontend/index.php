<?php
/**
 * @var string $transportApi
 * @var string $wisataProxyApi
 */
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mau Kemana? — Wisata & Transportasi Sumut</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Leaflet Maps -->
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin=""
    >
    <link rel="stylesheet" href="<?= base_url('css/app.css') ?>">
</head>
<body>
    <div id="app" class="app-shell">
        <!-- Peta Interaktif Bersama (Latar Halaman 1 & Halaman 2) -->
        <div id="map-container" class="map-canvas" aria-label="Peta Wisata Sumatera Utara"></div>

        <!-- ==================== HALAMAN 1: LANDING ==================== -->
        <section id="screen-landing" class="screen screen-landing is-active">
            <div class="landing-overlay"></div>

            <div class="landing-content">
                <div style="display: flex; justify-content: space-between; align-items: center; width: 100%; max-width: 480px; margin: 0 auto 1rem;">
                    <button type="button" id="btn-open-origin-modal" class="location-pill" style="margin-bottom: 0;">
                        <span class="location-dot"></span>
                        <span id="landing-origin-label">Medan, Sumatera Utara</span>
                        <span class="pill-caret">▾</span>
                    </button>
                    <a href="<?= base_url('admin') ?>" style="background: rgba(15, 31, 51, 0.8); color: #fff; backdrop-filter: blur(8px); padding: 0.45rem 0.85rem; border-radius: 9999px; font-size: 0.78rem; font-weight: 600; text-decoration: none; border: 1px solid rgba(255,255,255,0.25); display: inline-flex; align-items: center; gap: 0.35rem; transition: background 0.2s;" title="Kelola Titik Transportasi">
                        ⚙️ Admin
                    </a>
                </div>

                <div class="landing-bottom">
                    <h1 class="landing-title">Mau Kemana?</h1>
                    <p class="landing-subtitle">
                        Jelajahi tempat wisata Sumatera Utara, lihat peta interaktif, dan cek estimasi biaya pesawat, kereta, hingga bus dalam satu aplikasi.
                    </p>
                    <button type="button" id="btn-start" class="btn-primary btn-lg">
                        Mulai &nbsp;→
                    </button>
                </div>
            </div>
        </section>

        <!-- ==================== HALAMAN 2: PETA WISATA ==================== -->
        <section id="screen-map" class="screen screen-map" aria-hidden="true">
            <div class="map-top-bar">
                <div class="map-search-row">
                    <button type="button" id="btn-back-landing" class="btn-circle" aria-label="Kembali">
                        ‹
                    </button>

                    <div class="search-box">
                        <input
                            type="search"
                            id="search-input"
                            placeholder="Cari tempat wisata di Sumut…"
                            autocomplete="off"
                        >
                    </div>

                    <select id="kota-select" class="kota-select" aria-label="Pilih Kabupaten atau Kota">
                        <option value="Medan">Kota Medan</option>
                    </select>
                </div>

                <div id="category-chips" class="chips-row" role="tablist" aria-label="Filter Kategori Wisata">
                    <button type="button" class="chip is-active" data-kategori="">Semua</button>
                </div>

                <div id="map-status-toast" class="map-toast hidden">Memuat destinasi wisata…</div>
            </div>

            <!-- Kartu Pratinjau Wisata Melayang di Bawah -->
            <div id="wisata-preview-card" class="preview-card hidden">
                <div class="preview-body">
                    <img id="preview-img" class="preview-thumb" src="" alt="Foto destinasi">
                    <div class="preview-info">
                        <h2 id="preview-title" class="preview-title">-</h2>
                        <p id="preview-address" class="preview-address">-</p>
                        <p id="preview-meta" class="preview-meta">-</p>
                    </div>
                </div>
                <button type="button" id="btn-to-detail" class="btn-primary btn-md">
                    Lihat Wisata &amp; Harga Tiket
                </button>
            </div>
        </section>

        <!-- ==================== HALAMAN 3: WISATA & TRANSPORTASI ==================== -->
        <section id="screen-detail" class="screen screen-detail" aria-hidden="true">
            <div class="detail-scroll">
                <header class="detail-header">
                    <button type="button" id="btn-back-map" class="btn-circle" aria-label="Kembali ke Peta">
                        ‹
                    </button>
                    <h1 class="detail-page-title">Wisata &amp; Transportasi</h1>
                </header>

                <!-- Destinasi Terpilih -->
                <div id="selected-wisata-banner" class="selected-banner hidden">
                    <img id="banner-img" class="banner-thumb" src="" alt="">
                    <div class="banner-info">
                        <span class="banner-badge" id="banner-category">Wisata</span>
                        <h2 id="banner-title" class="banner-title">-</h2>
                        <p id="banner-sub" class="banner-sub">-</p>
                        <div class="banner-actions">
                            <span id="banner-ticket" class="banner-ticket">-</span>
                            <a id="banner-link" href="#" target="_blank" rel="noopener" class="banner-link hidden">
                                Detail di JejakSumut ↗
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Grid Destinasi Populer (Gaya Figma) -->
                <div class="section-block">
                    <div class="section-head">
                        <h2 class="section-title">Destinasi Populer</h2>
                        <span id="popular-kota-label" class="section-hint">Medan</span>
                    </div>
                    <div id="popular-grid" class="popular-grid"></div>
                </div>

                <!-- Kontrol Asal & Tipe Perjalanan -->
                <div class="section-block">
                    <div class="section-head">
                        <h2 class="section-title">Estimasi Rute &amp; Harga</h2>
                    </div>

                    <div class="control-card">
                        <div class="control-row">
                            <label for="origin-select-detail" class="control-label">Lokasi Keberangkatan</label>
                            <div class="origin-input-group">
                                <select id="origin-select-detail" class="form-select">
                                    <option value="medan">Medan (Dalam Sumut)</option>
                                    <option value="jakarta">Jakarta (Luar Sumut)</option>
                                    <option value="palembang">Palembang (Luar Sumut)</option>
                                    <option value="surabaya">Surabaya (Luar Sumut)</option>
                                    <option value="gps">Lokasi GPS Saya</option>
                                </select>
                                <button type="button" id="btn-use-gps" class="btn-outline-sm" title="Deteksi GPS">
                                    GPS
                                </button>
                            </div>
                        </div>

                        <div class="trip-toggle" role="group" aria-label="Tipe Perjalanan">
                            <button type="button" id="btn-trip-roundtrip" class="toggle-btn is-active" data-trip="roundtrip">
                                Pulang-Pergi (PP)
                            </button>
                            <button type="button" id="btn-trip-oneway" class="toggle-btn" data-trip="oneway">
                                Sekali Jalan
                            </button>
                        </div>
                    </div>

                    <!-- Ringkasan Total Biaya -->
                    <div id="transport-summary" class="summary-card hidden"></div>

                    <!-- Daftar Kartu Tiket / Segmen -->
                    <div id="transport-segments" class="segments-stack">
                        <div class="empty-box">Pilih destinasi untuk menghitung estimasi transportasi.</div>
                    </div>
                </div>

                <!-- Kalkulator Tarif Antar Simpul (Endpoint /api/transport/tarif) -->
                <div class="section-block">
                    <div class="section-head">
                        <h2 class="section-title">Cek Tarif Antar Simpul</h2>
                    </div>

                    <form id="tarif-form" class="control-card">
                        <div class="tarif-grid">
                            <div class="control-row">
                                <label for="tarif-asal" class="control-label">Simpul Asal</label>
                                <select id="tarif-asal" class="form-select" required></select>
                            </div>
                            <div class="control-row">
                                <label for="tarif-tujuan" class="control-label">Simpul Tujuan</label>
                                <select id="tarif-tujuan" class="form-select" required></select>
                            </div>
                            <div class="control-row">
                                <label for="tarif-jam" class="control-label">Jam Berangkat (Opsional)</label>
                                <input type="time" id="tarif-jam" class="form-input" value="09:00">
                            </div>
                        </div>
                        <button type="submit" id="btn-cek-tarif" class="btn-primary btn-md">
                            Cek Tarif &amp; Jadwal
                        </button>
                    </form>

                    <div id="tarif-results" class="segments-stack"></div>
                </div>

                <p class="disclaimer-text">
                    * Estimasi tarif dihitung otomatis dari jarak &amp; jadwal armada pada API Transportasi, sedangkan data destinasi bersumber dari API JejakSumut.
                </p>
            </div>
        </section>

        <!-- Modal Pilih Lokasi Asal (dari Pill Halaman 1) -->
        <div id="origin-modal" class="modal-backdrop hidden" role="dialog" aria-modal="true" aria-labelledby="origin-modal-title">
            <div class="modal-card">
                <div class="modal-head">
                    <h3 id="origin-modal-title">Pilih Lokasi Asal Kamu</h3>
                    <button type="button" id="btn-close-origin" class="btn-text-close">✕</button>
                </div>
                <p class="modal-desc">
                    Koordinat asal dipakai untuk menentukan rute pesawat + darat (dari luar Sumut) atau langsung darat (dari dalam Sumut).
                </p>
                <div class="preset-list">
                    <button type="button" class="preset-btn" data-origin="gps">Gunakan GPS Perangkat</button>
                    <button type="button" class="preset-btn" data-origin="medan">Medan, Sumatera Utara</button>
                    <button type="button" class="preset-btn" data-origin="jakarta">Jakarta (DKI Jakarta)</button>
                    <button type="button" class="preset-btn" data-origin="palembang">Palembang, Sumatera Selatan</button>
                    <button type="button" class="preset-btn" data-origin="surabaya">Surabaya, Jawa Timur</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.APP_CONFIG = {
            transportApi: <?= json_encode($transportApi) ?>,
            wisataProxyApi: <?= json_encode($wisataProxyApi) ?>
        };
    </script>
    <script
        src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""
    ></script>
    <script src="<?= base_url('js/app.js') ?>"></script>
</body>
</html>