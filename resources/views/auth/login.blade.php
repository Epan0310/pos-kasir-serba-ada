<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SerbaAda Mart POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --arasta-dark: #1C1917;
            --arasta-bg: #F8F6F0;
            --arasta-surface: #FFFFFF;
            --arasta-accent: #D97706;
            --arasta-border: #E7E5E4;
            --arasta-text: #292524;
        }

        body {
            background-color: var(--arasta-bg);
            color: var(--arasta-text);
            font-family: 'Plus Jakarta Sans', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background: var(--arasta-surface);
            border: 1px solid var(--arasta-border);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            width: 100%;
            max-width: 400px;
            padding: 32px;
        }

        .btn-arasta {
            background-color: var(--arasta-accent);
            color: #FFFFFF;
            font-weight: 700;
            border: none;
            border-radius: 10px;
            padding: 12px;
        }

        .btn-arasta:hover {
            background-color: #B45309;
            color: #FFFFFF;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="text-center mb-4">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 56px; height: 56px; background-color: var(--arasta-dark); color: var(--arasta-accent);">
                <i class="bi bi-shop fs-3"></i>
            </div>
            <h4 class="fw-bold mb-0">SerbaAda Mart</h4>
            <small class="text-muted">Masuk ke sistem POS Kasir</small>
        </div>

        @if(session('success'))
            <div class="alert alert-success p-2 fs-7 mb-3" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger p-2 fs-7 mb-3" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.process') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label small fw-bold">Username</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                    <input type="text" name="username" class="form-control border-start-0" placeholder="Masukkan username" value="{{ old('username') }}" required autofocus>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-bold">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                    <input type="password" name="password" class="form-control border-start-0" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" class="btn btn-arasta w-100">
                Masuk Sistem <i class="bi bi-arrow-right ms-1"></i>
            </button>
        </form>

        <div class="mt-4 pt-3 border-top text-center text-muted" style="font-size: 11px;">
            Default Admin: <strong>admin / admin123</strong><br>
            Default Kasir: <strong>kasir / kasir123</strong>
        </div>
    </div>

</body>
</html>