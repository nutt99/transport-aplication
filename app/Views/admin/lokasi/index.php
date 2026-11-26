<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Lokasi Transportasi — Admin Panel</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Leaflet CSS -->
    <link 
        rel="stylesheet" 
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
        crossorigin=""
    />

    <style>
        :root {
            --bg-base: #090d16;
            --bg-surface: #111827;
            --card-bg: #1f2937;
            --border-color: #374151;
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --text-main: #f9fafb;
            --text-muted: #9ca3af;
            --danger: #ef4444;
            --danger-hover: #dc2626;
            --success: #10b981;
            --warning: #f59e0b;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background-color: var(--bg-base);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top Navbar */
        .navbar {
            background: var(--bg-surface);
            border-bottom: 1px solid var(--border-color);
            padding: 0.85rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: #fff;
            font-weight: 700;
            font-size: 1.15rem;
        }

        .brand-badge {
            background: rgba(37, 99, 235, 0.2);
            color: #60a5fa;
            border: 1px solid rgba(37, 99, 235, 0.4);
            font-size: 0.75rem;
            padding: 0.2rem 0.5rem;
            border-radius: 9999px;
            font-weight: 600;
        }

        .navbar-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-greeting {
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        .user-greeting strong {
            color: #e5e7eb;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            font-size: 0.875rem;
            font-weight: 600;
            padding: 0.55rem 1rem;
            border-radius: 0.5rem;
            border: none;
            cursor: pointer;
            transition: all 0.15s ease;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--primary);
            color: #fff;
        }

        .btn-primary:hover {
            background: var(--primary-hover);
        }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--border-color);
            color: #e5e7eb;
        }

        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: #6b7280;
        }

        .btn-danger {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.4);
            color: #fca5a5;
        }

        .btn-danger:hover {
            background: var(--danger);
            color: #fff;
        }

        .btn-sm {
            padding: 0.35rem 0.65rem;
            font-size: 0.8rem;
        }

        /* Container */
        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 1.75rem 1.5rem 3rem;
            width: 100%;
            flex: 1;
        }

        /* Alerts */
        .alert {
            padding: 0.85rem 1.25rem;
            border-radius: 0.65rem;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.12);
            color: #6ee7b7;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.12);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            margin-bottom: 1.75rem;
        }

        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            padding: 1.15rem;
            position: relative;
            overflow: hidden;
        }

        .stat-title {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            margin-bottom: 0.35rem;
            font-weight: 600;
        }

        .stat-value {
            font-size: 1.65rem;
            font-weight: 700;
            color: #fff;
        }

        /* Content Card */
        .content-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 0.85rem;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
        }

        .toolbar {
            padding: 1.25rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            justify-content: space-between;
            align-items: center;
        }

        .toolbar-filter {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            align-items: center;
            flex: 1;
        }

        .input-search {
            background: #111827;
            border: 1px solid var(--border-color);
            color: #fff;
            padding: 0.55rem 0.85rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            min-width: 240px;
        }

        .input-search:focus, .select-filter:focus {
            outline: none;
            border-color: var(--primary);
        }

        .select-filter {
            background: #111827;
            border: 1px solid var(--border-color);
            color: #fff;
            padding: 0.55rem 0.85rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
        }

        /* Table */
        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.9rem;
        }

        th {
            background: #111827;
            padding: 0.85rem 1.25rem;
            color: #9ca3af;
            font-weight: 600;
            border-bottom: 1px solid var(--border-color);
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }

        td {
            padding: 0.95rem 1.25rem;
            border-bottom: 1px solid rgba(55, 65, 81, 0.6);
            color: #e5e7eb;
            vertical-align: middle;
        }

        tr:hover td {
            background: rgba(255, 255, 255, 0.02);
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.25rem 0.65rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        .badge-bandara {
            background: rgba(99, 102, 241, 0.15);
            color: #a5b4fc;
            border: 1px solid rgba(99, 102, 241, 0.3);
        }

        .badge-stasiun {
            background: rgba(245, 158, 11, 0.15);
            color: #fcd34d;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .badge-terminal {
            background: rgba(16, 185, 129, 0.15);
            color: #6ee7b7;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .badge-halte {
            background: rgba(14, 165, 233, 0.15);
            color: #7dd3fc;
            border: 1px solid rgba(14, 165, 233, 0.3);
        }

        .badge-pelabuhan {
            background: rgba(6, 182, 212, 0.15);
            color: #67e8f9;
            border: 1px solid rgba(6, 182, 212, 0.3);
        }

        .action-btns {
            display: flex;
            gap: 0.5rem;
        }

        /* Modal */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            padding: 1.5rem;
            opacity: 0;
            visibility: hidden;
            transition: all 0.25s ease;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-dialog {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            width: 100%;
            max-width: 680px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            transform: translateY(15px);
            transition: transform 0.25s ease;
        }

        .modal-overlay.active .modal-dialog {
            transform: translateY(0);
        }

        .modal-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: #fff;
        }

        .btn-close {
            background: transparent;
            border: none;
            color: var(--text-muted);
            font-size: 1.35rem;
            cursor: pointer;
            line-height: 1;
        }

        .btn-close:hover {
            color: #fff;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            padding: 1.25rem 1.5rem;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1.15rem;
        }

        .form-group {
            margin-bottom: 1.15rem;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 500;
            color: #cbd5e1;
            margin-bottom: 0.4rem;
        }

        .form-control {
            width: 100%;
            background: #111827;
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            padding: 0.65rem 0.85rem;
            color: #fff;
            font-size: 0.9rem;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
        }

        /* Map Picker */
        .map-picker-wrapper {
            margin-top: 0.5rem;
            border: 1px solid var(--border-color);
            border-radius: 0.65rem;
            overflow: hidden;
            position: relative;
        }

        #map-picker {
            width: 100%;
            height: 260px;
            background: #1e293b;
        }

        .map-picker-hint {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #111827;
            padding: 0.55rem 0.85rem;
            font-size: 0.78rem;
            color: #94a3b8;
            border-top: 1px solid var(--border-color);
        }

        .empty-state {
            padding: 3rem 1.5rem;
            text-align: center;
            color: var(--text-muted);
        }

        .empty-state p {
            margin-top: 0.5rem;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <!-- Top Navbar -->
    <header class="navbar">
        <a href="<?= base_url('admin/lokasi') ?>" class="navbar-brand">
            <span>Mau Kemana?</span>
            <span class="brand-badge">Admin Lokasi</span>
        </a>

        <div class="navbar-actions">
            <span class="user-greeting">Halo, <strong><?= esc(session()->get('admin_username') ?? 'Admin') ?></strong></span>
            <a href="<?= base_url('/') ?>" target="_blank" class="btn btn-outline btn-sm">
                Lihat Web Publik ↗
            </a>
            <a href="<?= base_url('admin/logout') ?>" class="btn btn-danger btn-sm">
                Logout
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container">
        <!-- Notifikasi Flashdata -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <span>✓</span>
                <div><?= esc(session()->getFlashdata('success')) ?></div>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-error">
                <span>⚠️</span>
                <div><?= esc(session()->getFlashdata('error')) ?></div>
            </div>
        <?php endif; ?>

        <!-- Stats Grid -->
        <section class="stats-grid">
            <div class="stat-card">
                <div class="stat-title">Total Titik Lokasi</div>
                <div class="stat-value"><?= (int)$stats['total'] ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Bandara</div>
                <div class="stat-value" style="color: #a5b4fc;"><?= (int)$stats['bandara'] ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Stasiun Kereta</div>
                <div class="stat-value" style="color: #fcd34d;"><?= (int)$stats['stasiun'] ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Terminal Bus</div>
                <div class="stat-value" style="color: #6ee7b7;"><?= (int)$stats['terminal'] ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Halte</div>
                <div class="stat-value" style="color: #7dd3fc;"><?= (int)$stats['halte'] ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Pelabuhan / Dermaga</div>
                <div class="stat-value" style="color: #67e8f9;"><?= (int)$stats['pelabuhan'] ?></div>
            </div>
        </section>

        <!-- Main Card Table -->
        <section class="content-card">
            <!-- Toolbar -->
            <div class="toolbar">
                <form action="" method="GET" class="toolbar-filter">
                    <input 
                        type="search" 
                        name="q" 
                        class="input-search" 
                        placeholder="Cari nama lokasi / halte / stasiun..." 
                        value="<?= esc($q ?? '') ?>"
                    >
                    <select name="tipe" class="select-filter" onchange="this.form.submit()">
                        <option value="">Semua Tipe Lokasi</option>
                        <option value="bandara" <?= ($tipe ?? '') === 'bandara' ? 'selected' : '' ?>>Bandara (Udara)</option>
                        <option value="stasiun" <?= ($tipe ?? '') === 'stasiun' ? 'selected' : '' ?>>Stasiun (Kereta Api)</option>
                        <option value="terminal" <?= ($tipe ?? '') === 'terminal' ? 'selected' : '' ?>>Terminal (Bus)</option>
                        <option value="halte" <?= ($tipe ?? '') === 'halte' ? 'selected' : '' ?>>Halte</option>
                        <option value="pelabuhan" <?= ($tipe ?? '') === 'pelabuhan' ? 'selected' : '' ?>>Pelabuhan / Dermaga</option>
                    </select>
                    <button type="submit" class="btn btn-outline btn-sm">Filter</button>
                    <?php if (!empty($q) || !empty($tipe)): ?>
                        <a href="?" class="btn btn-outline btn-sm" style="color: #f87171;">Reset</a>
                    <?php endif; ?>
                </form>

                <button type="button" class="btn btn-primary" onclick="bukaModalTambah()">
                    <span>+</span> Tambah Lokasi Baru
                </button>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>Nama Titik Lokasi</th>
                            <th>Tipe Simpul</th>
                            <th>Koordinat (Lat, Lng)</th>
                            <th style="text-align: right; width: 180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($lokasiList)): ?>
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <h3>Tidak ada titik lokasi ditemukan</h3>
                                        <p>Silakan sesuaikan kata kunci pencarian atau tambahkan titik lokasi baru.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($lokasiList as $item): ?>
                                <tr>
                                    <td style="color: var(--text-muted); font-weight: 500;">#<?= (int)$item['id'] ?></td>
                                    <td>
                                        <strong style="color: #fff; font-size: 0.95rem;"><?= esc($item['nama_lokasi']) ?></strong>
                                    </td>
                                    <td>
                                        <?php 
                                            $t = strtolower($item['tipe_lokasi'] ?? 'terminal');
                                            $badgeClass = 'badge-terminal';
                                            $label = 'Terminal';
                                            if ($t === 'bandara') { $badgeClass = 'badge-bandara'; $label = '✈️ Bandara'; }
                                            elseif ($t === 'stasiun') { $badgeClass = 'badge-stasiun'; $label = '🚆 Stasiun'; }
                                            elseif ($t === 'halte') { $badgeClass = 'badge-halte'; $label = '🚏 Halte'; }
                                            elseif ($t === 'pelabuhan') { $badgeClass = 'badge-pelabuhan'; $label = '🚢 Pelabuhan'; }
                                            else { $badgeClass = 'badge-terminal'; $label = '🚌 Terminal'; }
                                        ?>
                                        <span class="badge <?= $badgeClass ?>"><?= $label ?></span>
                                    </td>
                                    <td style="font-family: monospace; font-size: 0.85rem; color: #94a3b8;">
                                        <?= esc($item['latitude']) ?>, <?= esc($item['longitude']) ?>
                                    </td>
                                    <td>
                                        <div class="action-btns" style="justify-content: flex-end;">
                                            <button 
                                                type="button" 
                                                class="btn btn-outline btn-sm"
                                                onclick='bukaModalEdit(<?= json_encode($item) ?>)'
                                            >
                                                Edit
                                            </button>
                                            <button 
                                                type="button" 
                                                class="btn btn-danger btn-sm"
                                                onclick="konfirmasiHapus(<?= (int)$item['id'] ?>, '<?= esc(addslashes($item['nama_lokasi'])) ?>')"
                                            >
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <!-- Modal Form Tambah / Edit Lokasi -->
    <div id="modal-lokasi" class="modal-overlay">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 id="modal-form-title" class="modal-title">Tambah Titik Lokasi Baru</h3>
                <button type="button" class="btn-close" onclick="tutupModal()">&times;</button>
            </div>

            <form id="form-lokasi" method="POST" action="<?= base_url('admin/lokasi/simpan') ?>">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group" style="grid-column: span 2;">
                            <label for="input-nama">Nama Titik Lokasi / Halte / Bandara *</label>
                            <input 
                                type="text" 
                                id="input-nama" 
                                name="nama_lokasi" 
                                class="form-control" 
                                placeholder="Contoh: Halte Lapangan Merdeka atau Bandara Kualanamu (KNO)" 
                                required
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="input-tipe">Tipe Simpul Transportasi *</label>
                        <select id="input-tipe" name="tipe_lokasi" class="form-control" required>
                            <option value="halte">🚏 Halte / Tempat Pemberhentian Bus</option>
                            <option value="terminal">🚌 Terminal Bus (Tipe A / B / C)</option>
                            <option value="stasiun">🚆 Stasiun Kereta Api</option>
                            <option value="bandara">✈️ Bandara / Lapangan Terbang</option>
                            <option value="pelabuhan">🚢 Pelabuhan / Dermaga Penyeberangan</option>
                        </select>
                    </div>

                    <!-- Map Picker Interaktif -->
                    <div class="form-group">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                            <label style="margin-bottom: 0;">Pilih Titik di Peta (Klik / Geser Pin)</label>
                            <button type="button" class="btn btn-outline btn-sm" id="btn-gps-picker" onclick="ambilGpsAdmin()" style="font-size: 0.75rem;">
                                📍 Pakai GPS Saya
                            </button>
                        </div>

                        <div class="map-picker-wrapper">
                            <div id="map-picker"></div>
                            <div class="map-picker-hint">
                                <span>💡 Klik pada peta atau geser pin merah untuk mengambil koordinat otomatis</span>
                                <span id="map-coord-preview" style="font-family: monospace; color: #60a5fa;">Lat: -, Lng: -</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-row" style="margin-bottom: 0;">
                        <div class="form-group">
                            <label for="input-lat">Latitude *</label>
                            <input 
                                type="text" 
                                id="input-lat" 
                                name="latitude" 
                                class="form-control" 
                                placeholder="Misal: 3.5952" 
                                required
                            >
                        </div>
                        <div class="form-group">
                            <label for="input-lng">Longitude *</label>
                            <input 
                                type="text" 
                                id="input-lng" 
                                name="longitude" 
                                class="form-control" 
                                placeholder="Misal: 98.6722" 
                                required
                            >
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="tutupModal()">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btn-submit-modal">Simpan Lokasi</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Form Tersembunyi untuk Hapus -->
    <form id="form-hapus" method="POST" style="display: none;">
        <?= csrf_field() ?>
    </form>

    <!-- Leaflet JS -->
    <script 
        src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" 
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" 
        crossorigin=""
    ></script>

    <script>
        let pickerMap = null;
        let pickerMarker = null;
        const DEFAULT_CENTER = [3.5952, 98.6722]; // Medan

        function initMapPicker() {
            if (pickerMap) return;

            pickerMap = L.map('map-picker').setView(DEFAULT_CENTER, 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap'
            }).addTo(pickerMap);

            pickerMarker = L.marker(DEFAULT_CENTER, { draggable: true }).addTo(pickerMap);

            // Event klik di peta
            pickerMap.on('click', function(e) {
                setMarkerPosition(e.latlng.lat, e.latlng.lng);
            });

            // Event geser marker
            pickerMarker.on('dragend', function(e) {
                const pos = pickerMarker.getLatLng();
                setMarkerPosition(pos.lat, pos.lng);
            });
        }

        function setMarkerPosition(lat, lng) {
            const fixedLat = parseFloat(lat).toFixed(7);
            const fixedLng = parseFloat(lng).toFixed(7);

            pickerMarker.setLatLng([fixedLat, fixedLng]);
            pickerMap.panTo([fixedLat, fixedLng]);

            document.getElementById('input-lat').value = fixedLat;
            document.getElementById('input-lng').value = fixedLng;
            document.getElementById('map-coord-preview').textContent = `Lat: ${fixedLat}, Lng: ${fixedLng}`;
        }

        // Sinkronisasi manual dari input field ke marker peta
        document.getElementById('input-lat').addEventListener('change', syncFromInputs);
        document.getElementById('input-lng').addEventListener('change', syncFromInputs);

        function syncFromInputs() {
            const lat = parseFloat(document.getElementById('input-lat').value);
            const lng = parseFloat(document.getElementById('input-lng').value);
            if (!isNaN(lat) && !isNaN(lng)) {
                setMarkerPosition(lat, lng);
            }
        }

        function ambilGpsAdmin() {
            if (!navigator.geolocation) {
                alert('Browser Anda tidak mendukung geolokasi GPS.');
                return;
            }
            const btn = document.getElementById('btn-gps-picker');
            btn.textContent = '⏳ Mencari GPS...';

            navigator.geolocation.getCurrentPosition(
                function(pos) {
                    btn.textContent = '📍 Pakai GPS Saya';
                    setMarkerPosition(pos.coords.latitude, pos.coords.longitude);
                    pickerMap.setView([pos.coords.latitude, pos.coords.longitude], 15);
                },
                function(err) {
                    btn.textContent = '📍 Pakai GPS Saya';
                    alert('Gagal mendeteksi lokasi GPS: ' + err.message);
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        }

        function bukaModalTambah() {
            document.getElementById('modal-form-title').textContent = 'Tambah Titik Lokasi Baru';
            document.getElementById('form-lokasi').action = '<?= base_url('admin/lokasi/simpan') ?>';
            document.getElementById('btn-submit-modal').textContent = 'Simpan Lokasi';

            document.getElementById('input-nama').value = '';
            document.getElementById('input-tipe').value = 'halte';
            document.getElementById('input-lat').value = '';
            document.getElementById('input-lng').value = '';
            document.getElementById('map-coord-preview').textContent = 'Lat: -, Lng: -';

            document.getElementById('modal-lokasi').classList.add('active');

            setTimeout(() => {
                initMapPicker();
                pickerMap.invalidateSize();
                setMarkerPosition(DEFAULT_CENTER[0], DEFAULT_CENTER[1]);
            }, 200);
        }

        function bukaModalEdit(data) {
            document.getElementById('modal-form-title').textContent = 'Edit Titik Lokasi #' + data.id;
            document.getElementById('form-lokasi').action = '<?= base_url('admin/lokasi/update') ?>/' + data.id;
            document.getElementById('btn-submit-modal').textContent = 'Perbarui Data';

            document.getElementById('input-nama').value = data.nama_lokasi || '';
            document.getElementById('input-tipe').value = data.tipe_lokasi || 'terminal';
            document.getElementById('input-lat').value = data.latitude;
            document.getElementById('input-lng').value = data.longitude;

            document.getElementById('modal-lokasi').classList.add('active');

            setTimeout(() => {
                initMapPicker();
                pickerMap.invalidateSize();
                setMarkerPosition(data.latitude, data.longitude);
                pickerMap.setView([data.latitude, data.longitude], 14);
            }, 200);
        }

        function tutupModal() {
            document.getElementById('modal-lokasi').classList.remove('active');
        }

        function konfirmasiHapus(id, nama) {
            if (confirm(`Yakin ingin menghapus titik lokasi "${nama}"? Aksi ini tidak dapat dibatalkan.`)) {
                const form = document.getElementById('form-hapus');
                form.action = '<?= base_url('admin/lokasi/hapus') ?>/' + id;
                form.submit();
            }
        }

        // Tutup modal jika klik di luar box
        document.getElementById('modal-lokasi').addEventListener('click', function(e) {
            if (e.target === this) {
                tutupModal();
            }
        });
    </script>
</body>
</html>
