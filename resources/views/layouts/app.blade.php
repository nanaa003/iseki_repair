<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Iseki Repair - Sistem Perbaikan Traktor</title>
    <meta name="description" content="Sistem manajemen perbaikan traktor Iseki dengan QR scanning dan OCR">
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/favicon.svg') }}">
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/cropper.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/inter.css') }}" rel="stylesheet">
    <style>
        :root {
            --pink-50: #fdf2f8;
            --pink-100: #fce7f3;
            --pink-200: #fbcfe8;
            --pink-300: #f9a8d4;
            --pink-400: #f472b6;
            --pink-500: #ec4899;
            --pink-600: #db2777;
            --pink-700: #be185d;
            --pink-800: #9d174d;
            --pink-900: #831843;
            --glass-bg: rgba(255, 255, 255, 0.75);
            --glass-border: rgba(255, 255, 255, 0.3);
        }

        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background: linear-gradient(135deg, var(--pink-50) 0%, #fff1f5 50%, #fce7f3 100%);
            min-height: 100vh;
        }

        /* Hero Banner */
        .hero-banner {
            background: linear-gradient(135deg, var(--pink-500) 0%, var(--pink-700) 50%, var(--pink-800) 100%);
            color: white;
            padding: 2.5rem 1rem;
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .hero-banner::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.08) 0%, transparent 60%);
            animation: shimmer 8s ease-in-out infinite;
        }

        .hero-banner h1 {
            font-weight: 800;
            font-size: 1.8rem;
            letter-spacing: -0.025em;
            position: relative;
            z-index: 1;
        }

        .hero-banner p {
            font-weight: 400;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        @keyframes shimmer {

            0%,
            100% {
                transform: translate(0, 0) rotate(0deg);
            }

            50% {
                transform: translate(10%, 10%) rotate(5deg);
            }
        }

        /* Glass Cards */
        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(219, 39, 119, 0.08);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .glass-card:hover {
            box-shadow: 0 12px 40px rgba(219, 39, 119, 0.15);
            transform: translateY(-2px);
        }

        /* Buttons */
        .btn-pink {
            background: linear-gradient(135deg, var(--pink-500), var(--pink-600));
            border: none;
            color: white;
            font-weight: 600;
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 14px rgba(236, 72, 153, 0.3);
        }

        .btn-pink:hover {
            background: linear-gradient(135deg, var(--pink-600), var(--pink-700));
            box-shadow: 0 6px 20px rgba(236, 72, 153, 0.4);
            transform: translateY(-1px);
            color: white;
        }

        .btn-pink-outline {
            background: transparent;
            border: 2px solid var(--pink-400);
            color: var(--pink-600);
            font-weight: 600;
            border-radius: 12px;
            padding: 0.65rem 1.5rem;
            transition: all 0.3s ease;
        }

        .btn-pink-outline:hover {
            background: var(--pink-500);
            border-color: var(--pink-500);
            color: white;
            transform: translateY(-1px);
        }

        /* Card Header Pink */
        .card-header-pink {
            background: linear-gradient(135deg, var(--pink-500), var(--pink-600));
            color: white;
            font-weight: 700;
            border-radius: 16px 16px 0 0 !important;
            padding: 1rem 1.25rem;
            border: none;
        }

        /* Tables */
        .table-premium thead {
            background: linear-gradient(135deg, var(--pink-600), var(--pink-700));
            color: white;
        }

        .table-premium thead th {
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 1rem 0.75rem;
            border: none;
        }

        .table-premium tbody tr {
            transition: all 0.2s ease;
        }

        .table-premium tbody tr:hover {
            background: var(--pink-50);
        }

        .table-premium tbody td {
            padding: 0.85rem 0.75rem;
            vertical-align: middle;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .hero-banner {
                padding: 1.5rem 1rem;
            }

            .hero-banner h1 {
                font-size: 1.4rem;
            }

            .table-premium thead th {
                padding: 0.75rem 0.5rem;
                font-size: 0.75rem;
            }

            .table-premium tbody td {
                padding: 0.6rem 0.5rem;
                font-size: 0.85rem;
            }

            .stat-card {
                padding: 1rem;
            }

            .stat-card .stat-icon {
                width: 40px;
                height: 40px;
                font-size: 1.1rem;
            }

            .stat-card .stat-value {
                font-size: 1.4rem;
            }
        }

        /* Stats Cards */
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 1.25rem;
            border: 1px solid var(--pink-100);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(219, 39, 119, 0.12);
        }

        .stat-card .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .stat-card .stat-value {
            font-size: 1.75rem;
            font-weight: 800;
            line-height: 1;
        }

        .stat-card .stat-label {
            font-size: 0.8rem;
            color: #6b7280;
            font-weight: 500;
        }

        /* Badge */
        .badge-pink {
            background: linear-gradient(135deg, var(--pink-500), var(--pink-600));
            color: white;
            font-weight: 600;
            padding: 0.35rem 0.75rem;
            border-radius: 8px;
        }

        .badge-success-soft {
            background: #d1fae5;
            color: #065f46;
            font-weight: 600;
            padding: 0.35rem 0.75rem;
            border-radius: 8px;
        }

        .badge-warning-soft {
            background: #fef3c7;
            color: #92400e;
            font-weight: 600;
            padding: 0.35rem 0.75rem;
            border-radius: 8px;
        }

        /* Navbar Admin */
        .navbar-admin {
            background: linear-gradient(135deg, var(--pink-700), var(--pink-800)) !important;
            border-bottom: 3px solid var(--pink-400);
            box-shadow: 0 4px 15px rgba(157, 23, 77, 0.2);
        }

        .navbar-admin .nav-link {
            font-weight: 500;
            transition: all 0.2s ease;
            border-radius: 8px;
            margin: 0 0.15rem;
            padding: 0.5rem 1rem !important;
        }

        .navbar-admin .nav-link:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        /* Form Controls */
        .form-control:focus,
        .form-select:focus {
            border-color: var(--pink-400);
            box-shadow: 0 0 0 3px rgba(244, 114, 182, 0.15);
        }

        /* Nav Pills */
        .nav-pills-pink .nav-link {
            color: var(--pink-600);
            font-weight: 600;
            border-radius: 10px;
            padding: 0.6rem 1rem;
            transition: all 0.2s ease;
        }

        .nav-pills-pink .nav-link.active {
            background: linear-gradient(135deg, var(--pink-500), var(--pink-600));
            color: white;
            box-shadow: 0 4px 12px rgba(236, 72, 153, 0.3);
        }

        /* Animations */
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .slide-up {
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Progress Bar OCR */
        .ocr-progress {
            height: 6px;
            border-radius: 3px;
            background: var(--pink-100);
            overflow: hidden;
        }

        .ocr-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--pink-400), var(--pink-600));
            border-radius: 3px;
            transition: width 0.3s ease;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: var(--pink-50);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--pink-300);
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--pink-400);
        }

        @yield('styles')
    </style>
    @yield('head')
</head>

<body>
    <!-- Top Navbar for all pages -->
    @hasSection('hide_navbar')
    @else
    <nav class="navbar navbar-expand-lg" style="background: rgba(255,255,255,0.85); backdrop-filter: blur(10px); border-bottom: 1px solid var(--pink-100); padding: 0.5rem 0;">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ route('repair.index') }}" style="color: var(--pink-700);">
                <i class="bi bi-wrench-adjustable-circle" style="font-size: 1.3rem;"></i>
                <span>Iseki Repair</span>
            </a>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('repair.index') }}" class="btn btn-sm btn-pink-outline px-3" style="font-size: 0.8rem;">
                    <i class="bi bi-house me-1"></i>Dashboard
                </a>
                @auth
                <a href="{{ route('admin.dashboard') }}" class="btn btn-sm px-3" style="font-size: 0.8rem; background: var(--pink-700); color: white; border-radius: 10px;">
                    <i class="bi bi-speedometer2 me-1"></i>Admin
                </a>
                @else
                <a href="{{ route('login') }}" class="btn btn-sm px-3" style="font-size: 0.8rem; background: var(--pink-700); color: white; border-radius: 10px;">
                    <i class="bi bi-person-lock me-1"></i>Admin Login
                </a>
                @endauth
            </div>
        </div>
    </nav>
    @endif

    @yield('content')

    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/html5-qrcode.min.js') }}"></script>
    <script src="{{ asset('assets/js/cropper.min.js') }}"></script>
    <script src="{{ asset('assets/js/tesseract.min.js') }}"></script>
    @stack('scripts')
</body>

</html>