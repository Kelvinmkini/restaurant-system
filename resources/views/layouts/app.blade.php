<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Restaurant Manager</title>
    
    <!-- FAVICON -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🍽️</text></svg>">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #e74c3c;
            --success: #27ae60;
            --info: #3498db;
            --warning: #f39c12;
        }
        
        html, body {
            height: 100%;
        }
        
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            transition: background-color 0.3s, color 0.3s;
        }
        
        /* DARK MODE STYLES */
        [data-bs-theme="dark"] body {
            background: #212529;
            color: #dee2e6;
        }
        
        [data-bs-theme="dark"] .navbar {
            background: #1a1d20 !important;
        }
        
        [data-bs-theme="dark"] .card {
            background: #2d333b;
            color: #dee2e6;
        }
        
        [data-bs-theme="dark"] .card-header {
            background: #2d333b !important;
            border-bottom-color: #444c56;
        }
        
        [data-bs-theme="dark"] .table {
            color: #dee2e6;
        }
        
        [data-bs-theme="dark"] .table-light {
            background: #2d333b;
            color: #dee2e6;
        }
        
        [data-bs-theme="dark"] .form-control,
        [data-bs-theme="dark"] .form-select {
            background: #22272e;
            border-color: #444c56;
            color: #dee2e6;
        }
        
        [data-bs-theme="dark"] .footer-fixed {
            background: #1a1d20 !important;
            border-top-color: #444c56;
        }
        
        [data-bs-theme="dark"] .dropdown-menu {
            background: #2d333b;
            border-color: #444c56;
        }
        
        [data-bs-theme="dark"] .dropdown-item {
            color: #dee2e6;
        }
        
        [data-bs-theme="dark"] .dropdown-item:hover {
            background: #444c56;
        }
        
        .navbar {
            background: var(--primary) !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: background-color 0.3s;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s, background-color 0.3s, color 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card {
            position: relative;
            overflow: hidden;
        }
        
        .bg-gradient-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .bg-gradient-success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
        .bg-gradient-warning { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .bg-gradient-info { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        
        .chart-container { position: relative; height: 400px; }
        
        .btn-custom {
            border-radius: 25px;
            padding: 10px 25px;
            font-weight: 600;
        }
        
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            transition: background-color 0.3s, color 0.3s, border-color 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--info);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        
        .profit-positive { color: var(--success); font-weight: bold; }
        .profit-negative { color: var(--secondary); font-weight: bold; }
        
        .animate-fade-in { animation: fadeIn 0.8s ease-in; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        
        /* THEME TOGGLE BUTTON */
        .theme-toggle {
            cursor: pointer;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            border: 2px solid rgba(255,255,255,0.3);
            background: transparent;
            color: white;
            transition: all 0.3s;
        }
        
        .theme-toggle:hover {
            background: rgba(255,255,255,0.1);
        }
        
        main {
            flex: 1 0 auto;
        }
        
        .footer-fixed {
            flex-shrink: 0;
            background: white;
            border-top: 1px solid #dee2e6;
            padding: 1rem 0;
            margin-top: auto;
            transition: background-color 0.3s, border-color 0.3s;
        }
    </style>
    @stack('styles')
</head>
<body>
    @auth
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">
                <i class="bi bi-shop-window me-2"></i>Restaurant Manager
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('sales.create') }}"><i class="bi bi-plus-circle me-1"></i> New Sale</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('sales.report') }}"><i class="bi bi-file-earmark-text me-1"></i> Reports</a>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-tags me-1"></i> Categories
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('categories.create') }}"><i class="bi bi-plus-circle me-2"></i>Add Category</a></li>
                            <li><a class="dropdown-item" href="{{ route('categories.index') }}"><i class="bi bi-list me-2"></i>View Categories</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-database me-1"></i> Items
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('food-items.create') }}"><i class="bi bi-plus-circle me-2"></i>Add Items</a></li>
                            <li><a class="dropdown-item" href="{{ route('food-items.index') }}"><i class="bi bi-list me-2"></i>View Items</a></li>
                        </ul>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto align-items-center">
                    <!-- THEME TOGGLE -->
                    <li class="nav-item me-3">
                        <button class="theme-toggle" id="themeToggle" onclick="toggleTheme()">
                            <i class="bi bi-moon-fill" id="themeIcon"></i>
                            <span id="themeText">Dark</span>
                        </button>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i> {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    @endauth

    <main class="container py-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show animate-fade-in" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show animate-fade-in" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('warning') }}
                @if(session('warning_action_url'))
                    <a href="{{ session('warning_action_url') }}" class="alert-link fw-bold ms-2">{{ session('warning_action_text') ?? 'Click here' }}</a>
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    @auth
    <footer class="footer-fixed">
        <div class="container text-center text-muted">
            <small>&copy; {{ date('Y') }} Restaurant Sales Management System. Welcome, {{ Auth::user()->name }}.</small>
        </div>
    </footer>
    @endauth

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- THEME TOGGLE SCRIPT -->
    <script>
        // Check saved theme or default to light
        const currentTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-bs-theme', currentTheme);
        updateThemeUI(currentTheme);

        function toggleTheme() {
            const html = document.documentElement;
            const current = html.getAttribute('data-bs-theme');
            const next = current === 'light' ? 'dark' : 'light';
            
            html.setAttribute('data-bs-theme', next);
            localStorage.setItem('theme', next);
            updateThemeUI(next);
        }

        function updateThemeUI(theme) {
            const icon = document.getElementById('themeIcon');
            const text = document.getElementById('themeText');
            
            if (theme === 'dark') {
                icon.className = 'bi bi-sun-fill';
                text.textContent = 'Light';
            } else {
                icon.className = 'bi bi-moon-fill';
                text.textContent = 'Dark';
            }
        }
    </script>
    
    <!-- CATEGORY BADGE COLORS SCRIPT -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.category-badge').forEach(function(badge) {
                var color = badge.dataset.color;
                if (color) {
                    badge.style.backgroundColor = color;
                }
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>