<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — Panel Transportasi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-base: #0f172a;
            --card-bg: #1e293b;
            --border-color: #334155;
            --primary: #3b82f6;
            --primary-hover: #2563eb;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --danger: #ef4444;
            --success: #10b981;
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
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .login-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            width: 100%;
            max-width: 420px;
            padding: 2.5rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.4), 0 8px 10px -6px rgba(0, 0, 0, 0.4);
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .app-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(59, 130, 246, 0.15);
            color: #60a5fa;
            padding: 0.35rem 0.85rem;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 1rem;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        .login-title {
            font-size: 1.6rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.5rem;
        }

        .login-subtitle {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .alert {
            padding: 0.85rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.15);
            color: #6ee7b7;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #cbd5e1;
            margin-bottom: 0.5rem;
        }

        .input-control {
            width: 100%;
            background: #0f172a;
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            color: #fff;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .input-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);
        }

        .btn-submit {
            width: 100%;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 0.5rem;
            padding: 0.85rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s ease;
            margin-top: 0.5rem;
        }

        .btn-submit:hover {
            background: var(--primary-hover);
        }

        .login-footer {
            margin-top: 1.75rem;
            text-align: center;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .login-footer a {
            color: #60a5fa;
            text-decoration: none;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <span class="app-badge">Admin Panel</span>
            <h1 class="login-title">Transportasi Sumut</h1>
            <p class="login-subtitle">Masuk untuk mengelola halte, terminal, stasiun, dan bandara</p>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-error">
                <span>⚠️</span>
                <div><?= esc(session()->getFlashdata('error')) ?></div>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <span>✓</span>
                <div><?= esc(session()->getFlashdata('success')) ?></div>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('admin/login') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="username">Username</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    class="input-control" 
                    placeholder="Masukkan username admin" 
                    value="<?= old('username', 'admin') ?>" 
                    required 
                    autofocus
                >
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="input-control" 
                    placeholder="Masukkan password admin" 
                    required
                >
            </div>

            <button type="submit" class="btn-submit">Masuk ke Panel Admin</button>
        </form>

        <div class="login-footer">
            <p>Kembali ke <a href="<?= base_url('/') ?>">Halaman Wisata &amp; Peta</a></p>
        </div>
    </div>
</body>
</html>
