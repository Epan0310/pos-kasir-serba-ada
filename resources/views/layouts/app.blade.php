<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SerbaAda Mart POS')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts & Bootstrap Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --arasta-dark: #1C1917;
            --arasta-bg: #F8F6F0;
            --arasta-surface: #FFFFFF;
            --arasta-accent: #D97706;
            --arasta-accent-hover: #B45309;
            --arasta-border: #E7E5E4;
            --arasta-text: #292524;
            --arasta-muted: #78716C;
        }

        body {
            background-color: var(--arasta-bg);
            color: var(--arasta-text);
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
        }

        .header-bar {
            background-color: var(--arasta-dark);
            color: #FFFFFF;
            padding: 10px 24px;
        }

        .header-bar a {
            color: #E7E5E4;
            text-decoration: none;
            margin-left: 20px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: color 0.2s;
        }

        .header-bar a:hover,
        .header-bar a.active {
            color: var(--arasta-accent);
            font-weight: 700;
        }
    </style>

    @stack('styles')
</head>
<body>

    <!-- NAVBAR UNIVERSAL -->
    <div class="header-bar d-flex justify-content-between align-items-center sticky-top shadow-sm">
        <div class="fw-bold fs-5 d-flex align-items-center">
            <i class="bi bi-shop me-2 text-warning"></i>SerbaAda Mart POS
        </div>
        <div class="d-flex align-items-center">
            
            @auth
                <!-- Menu Khusus Admin -->
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">Kategori</a>
                    <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">Produk</a>
                    <a href="{{ route('transactions.index') }}" class="{{ request()->routeIs('transactions.*') ? 'active' : '' }}">Kasir</a>
                    <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">Laporan</a>
                @endif

                <!-- User Info & Logout -->
                <span class="ms-3 ps-3 border-start border-secondary text-light small">
                    <i class="bi bi-person-circle me-1"></i> {{ auth()->user()->name }} 
                    <span class="badge bg-secondary ms-1">{{ ucfirst(auth()->user()->role) }}</span>
                </span>

                <form action="{{ route('logout') }}" method="POST" class="d-inline ms-3">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-light py-1 px-2 fs-7 border-0" title="Keluar dari Aplikasi">
                        <i class="bi bi-box-arrow-right me-1"></i> Keluar
                    </button>
                </form>
            @endauth

        </div>
    </div>

    <!-- MAIN CONTENT -->
    <main>
        @yield('content')
    </main>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>