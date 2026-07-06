<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'MiniStock' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-bg: #1E293B;
            --sidebar-text: #94A3B8;
            --sidebar-active: #F1F5F9;
            --accent: #6366F1;
            --accent-hover: #4F46E5;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #F8FAFC;
            color: #1E293B;
        }

        .navbar-public {
            background-color: #fff;
            border-bottom: 1px solid #E2E8F0;
        }

        .navbar-brand-name {
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--accent);
        }

        /* Sidebar desktop */
        .sidebar {
            width: 240px;
            min-height: 100vh;
            background-color: var(--sidebar-bg);
            position: fixed;
            top: 0;
            left: 0;
            padding: 1.5rem 1rem;
            display: flex;
            flex-direction: column;
            z-index: 1030;
            transition: transform 0.25s ease;
        }

        .sidebar-brand {
            color: #fff;
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 2rem;
            padding: 0 0.5rem;
        }

        .sidebar-brand span { color: var(--accent); }

        .sidebar-nav .nav-link {
            color: var(--sidebar-text);
            padding: 0.6rem 0.75rem;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.15s;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .sidebar-nav .nav-link:hover,
        .sidebar-nav .nav-link.active {
            background-color: rgba(255,255,255,0.08);
            color: var(--sidebar-active);
        }

        .sidebar-bottom { margin-top: auto; }

        .painel-content {
            margin-left: 240px;
            padding: 2rem;
            min-height: 100vh;
        }

        /* Topbar mobile */
        .mobile-topbar {
            display: none;
            background-color: var(--sidebar-bg);
            padding: 0.75rem 1rem;
            position: sticky;
            top: 0;
            z-index: 1031;
        }

        .mobile-topbar .brand {
            color: #fff;
            font-weight: 700;
            font-size: 1rem;
        }

        .mobile-topbar .brand span { color: var(--accent); }

        .btn-hamburger {
            background: transparent;
            border: none;
            color: #fff;
            font-size: 1.4rem;
            padding: 0;
            line-height: 1;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1029;
        }

        .sidebar-overlay.active { display: block; }

        /* Vitrine */
        .product-card {
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            transition: box-shadow 0.2s;
            background: #fff;
        }

        .product-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.08); }

        .product-price {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--accent);
        }

        .badge-stock {
            font-size: 0.72rem;
            font-weight: 600;
            padding: 0.3em 0.7em;
            border-radius: 20px;
        }

        /* Tabela painel */
        .table-painel {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #E2E8F0;
        }

        .table-painel thead th {
            background-color: #F1F5F9;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
            color: #64748B;
            border-bottom: 1px solid #E2E8F0;
        }

        .btn-accent {
            background-color: var(--accent);
            border-color: var(--accent);
            color: #fff;
            font-weight: 500;
        }

        .btn-accent:hover {
            background-color: var(--accent-hover);
            border-color: var(--accent-hover);
            color: #fff;
        }

        /* Modal */
        .livewire-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.45);
            z-index: 1040;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .livewire-modal {
            background: #fff;
            border-radius: 16px;
            width: 100%;
            max-width: 480px;
            padding: 2rem;
            position: relative;
            z-index: 1050;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            max-height: 90vh;
            overflow-y: auto;
        }

        /* ── Mobile ── */
        @media (max-width: 767px) {

            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .mobile-topbar {
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .painel-content {
                margin-left: 0;
                padding: 1rem;
            }

            .col-hide-mobile { display: none !important; }

            .livewire-modal {
                padding: 1.25rem;
                border-radius: 12px;
                max-height: 95vh;
            }

            .painel-actions {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 0.75rem !important;
            }

            .painel-actions .input-group {
                max-width: 100% !important;
            }

            .painel-actions .btn-accent {
                width: 100%;
                margin-left: 0 !important;
            }

            .vitrine-hero h1 {
                font-size: 1.6rem !important;
            }

            .product-price {
                font-size: 1.1rem;
            }
        }
    </style>

    @livewireStyles
</head>
<body>

    {{ $slot }}

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const isOpen  = sidebar.classList.contains('open');
            sidebar.classList.toggle('open', !isOpen);
            overlay.classList.toggle('active', !isOpen);
            document.body.style.overflow = isOpen ? '' : 'hidden';
        }

        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    </script>

    @livewireScripts
</body>
</html>
