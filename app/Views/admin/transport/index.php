<?php
/**
 * @var array $transportList
 * @var array $stats
 * @var string $q
 */
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Moda Transportasi — Admin Panel</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

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
            gap: 1rem;
            flex-wrap: wrap;
        }

        .navbar-left {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            flex-wrap: wrap;
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

        .nav-links {
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .nav-link {
            text-decoration: none;
            color: var(--text-muted);
            font-size: 0.875rem;
            font-weight: 500;
            padding: 0.4rem 0.75rem;
            border-radius: 0.45rem;
            transition: all 0.15s ease;
        }

        .nav-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.06);
        }

        .nav-link.active {
            color: #fff;
            background: rgba(37, 99, 235, 0.2);
            border: 1px solid rgba(37, 99, 235, 0.35);
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
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
            margin-bottom: 1.75rem;
        }

        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            padding: 1.1rem 1.25rem;
        }

        .stat-title {
            font-size: 0.8rem;
            color: var(--text-muted);
            font-weight: 500;
            margin-bottom: 0.35rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #fff;
        }

        /* Content Card */
        .content-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: 0.85rem;
            overflow: hidden;
        }

        /* Toolbar */
        .toolbar {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .toolbar-filter {
            display: flex;
            gap: 0.75rem;
            align-items: center;
            flex-wrap: wrap;
            flex: 1;
        }

        .input-search {
            background: var(--bg-base);
            border: 1px solid var(--border-color);
            color: #fff;
            padding: 0.55rem 0.9rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            min-width: 260px;
        }

        .input-search:focus {
            outline: none;
            border-color: var(--primary);
        }

        /* Table */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        thead th {
            background: rgba(31, 41, 55, 0.6);
            padding: 0.85rem 1.25rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border-color);
        }

        tbody td {
            padding: 0.95rem 1.25rem;
            font-size: 0.9rem;
            border-bottom: 1px solid rgba(55, 65, 81, 0.5);
            vertical-align: middle;
        }

        tbody tr:hover {
            background: rgba(255, 255, 255, 0.02);
        }

        .badge {
            display: inline-block;
            padding: 0.25rem 0.65rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-info {
            background: rgba(59, 130, 246, 0.15);
            color: #93c5fd;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        .badge-muted {
            background: rgba(156, 163, 175, 0.12);
            color: #d1d5db;
            border: 1px solid rgba(156, 163, 175, 0.25);
        }

        .action-group {
            display: flex;
            gap: 0.5rem;
        }

        /* Modal */
        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(4px);
            z-index: 999;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .modal-backdrop.active {
            display: flex;
        }

        .modal-box {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            width: 100%;
            max-width: 480px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
        }

        .modal-header {
            padding: 1.15rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #fff;
        }

        .modal-close {
            background: transparent;
            border: none;
            color: var(--text-muted);
            font-size: 1.4rem;
            cursor: pointer;
            line-height: 1;
        }

        .modal-close:hover {
            color: #fff;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            background: rgba(17, 24, 39, 0.5);
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 500;
            color: #cbd5e1;
            margin-bottom: 0.45rem;
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
        <div class="navbar-left">
            <a href="<?= base_url('admin/lokasi') ?>" class="navbar-brand">
                <span>Mau Kemana?</span>
                <span class="brand-badge">Admin Panel</span>
            </a>

            <nav class="nav-links" aria-label="Menu Admin">
                <a href="<?= base_url('admin/lokasi') ?>" class="nav-link">Lokasi</a>
                <a href="<?= base_url('admin/transport') ?>" class="nav-link active">Moda Transportasi</a>
            </nav>
        </div>

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
                <div class="stat-title">Total Moda Transportasi</div>
                <div class="stat-value"><?= (int) $stats['total_transport'] ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Total Penyedia Terhubung</div>
                <div class="stat-value" style="color: #60a5fa;"><?= (int) $stats['total_penyedia'] ?></div>
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
                        placeholder="Cari jenis transportasi..."
                        value="<?= esc($q ?? '') ?>"
                    >
                    <button type="submit" class="btn btn-outline btn-sm">Cari</button>
                    <?php if (!empty($q)): ?>
                        <a href="<?= base_url('admin/transport') ?>" class="btn btn-outline btn-sm" style="color: #f87171;">Reset</a>
                    <?php endif; ?>
                </form>

                <button type="button" class="btn btn-primary" onclick="bukaModalTambah()">
                    <span>+</span> Tambah Moda Baru
                </button>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 70px;">ID</th>
                            <th>Jenis Moda Transportasi</th>
                            <th>Penyedia Terhubung</th>
                            <th>Diperbarui</th>
                            <th style="width: 170px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($transportList)): ?>
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <h3>Belum ada data moda transportasi</h3>
                                        <p>Klik tombol "Tambah Moda Baru" di atas untuk menambahkan jenis transportasi.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($transportList as $item): ?>
                                <tr>
                                    <td style="color: var(--text-muted);">#<?= (int) $item['id'] ?></td>
                                    <td style="font-weight: 600; color: #fff;"><?= esc($item['jenis']) ?></td>
                                    <td>
                                        <?php if ((int) $item['jumlah_penyedia'] > 0): ?>
                                            <span class="badge badge-info"><?= (int) $item['jumlah_penyedia'] ?> Penyedia</span>
                                        <?php else: ?>
                                            <span class="badge badge-muted">0 Penyedia</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="color: var(--text-muted); font-size: 0.85rem;">
                                        <?= esc($item['updated_at'] ?? $item['created_at'] ?? '-') ?>
                                    </td>
                                    <td>
                                        <div class="action-group">
                                            <button
                                                type="button"
                                                class="btn btn-outline btn-sm"
                                                onclick='bukaModalEdit(<?= json_encode([
                                                    'id'    => (int) $item['id'],
                                                    'jenis' => $item['jenis'],
                                                ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                            >
                                                Edit
                                            </button>
                                            <button
                                                type="button"
                                                class="btn btn-danger btn-sm"
                                                onclick='konfirmasiHapus(<?= (int) $item["id"] ?>, <?= json_encode($item["jenis"]) ?>)'
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

    <!-- Modal Tambah / Edit Moda Transportasi -->
    <div id="modal-transport" class="modal-backdrop">
        <div class="modal-box">
            <div class="modal-header">
                <h2 class="modal-title" id="modal-form-title">Tambah Moda Transportasi</h2>
                <button type="button" class="modal-close" onclick="tutupModal()">&times;</button>
            </div>

            <form id="form-transport" method="POST" action="<?= base_url('admin/transport/simpan') ?>">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="input-jenis">Jenis Transportasi <span style="color: #ef4444;">*</span></label>
                        <input
                            type="text"
                            id="input-jenis"
                            name="jenis"
                            class="form-control"
                            placeholder="Contoh: Pesawat, Kereta Api, Bus, Kapal Feri"
                            minlength="2"
                            maxlength="50"
                            required
                        >
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="tutupModal()">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btn-submit-modal">Simpan Moda</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Form Tersembunyi untuk Hapus -->
    <form id="form-hapus" method="POST" style="display: none;">
        <?= csrf_field() ?>
    </form>

    <script>
        function bukaModalTambah() {
            document.getElementById('modal-form-title').textContent = 'Tambah Moda Transportasi Baru';
            document.getElementById('form-transport').action = '<?= base_url('admin/transport/simpan') ?>';
            document.getElementById('btn-submit-modal').textContent = 'Simpan Moda';
            document.getElementById('input-jenis').value = '';

            document.getElementById('modal-transport').classList.add('active');
            setTimeout(() => document.getElementById('input-jenis').focus(), 100);
        }

        function bukaModalEdit(data) {
            document.getElementById('modal-form-title').textContent = 'Edit Moda Transportasi #' + data.id;
            document.getElementById('form-transport').action = '<?= base_url('admin/transport/update') ?>/' + data.id;
            document.getElementById('btn-submit-modal').textContent = 'Perbarui Data';
            document.getElementById('input-jenis').value = data.jenis || '';

            document.getElementById('modal-transport').classList.add('active');
            setTimeout(() => document.getElementById('input-jenis').focus(), 100);
        }

        function tutupModal() {
            document.getElementById('modal-transport').classList.remove('active');
        }

        function konfirmasiHapus(id, jenis) {
            if (confirm(`Yakin ingin menghapus moda transportasi "${jenis}"? Aksi ini tidak dapat dibatalkan.`)) {
                const form = document.getElementById('form-hapus');
                form.action = '<?= base_url('admin/transport/hapus') ?>/' + id;
                form.submit();
            }
        }

        document.getElementById('modal-transport').addEventListener('click', function(e) {
            if (e.target === this) {
                tutupModal();
            }
        });
    </script>
</body>
</html>
