<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>GTI Endereçamento - @yield('title', 'Dashboard')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        :root {
            --primary: #0e7490;
            --primary-dark: #0c6379;
            --primary-light: #67e8f9;
            --secondary: #14b8a6;
            --bg-body: #f3f4f6;
            --surface: #ffffff;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --border: #e5e7eb;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --radius: 12px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            padding: 1.5rem;
            position: fixed;
            height: 100vh;
            z-index: 50;
        }

        .brand {
            font-family: 'Outfit', sans-serif;
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 2.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #0e7490 0%, #14b8a6 100%);
            border-radius: 8px;
            color: white;
            font-size: 16px;
            font-weight: 700;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.8rem 1rem;
            color: var(--text-muted);
            text-decoration: none;
            border-radius: var(--radius);
            margin-bottom: 0.5rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .nav-item:hover,
        .nav-item.active {
            background: #ecfeff;
            color: var(--primary);
        }

        .nav-item i {
            font-size: 1.2rem;
        }

        /* User Profile */
        .user-profile {
            margin-top: auto;
            display: flex;
            align-items: center;
            gap: 12px;
            padding-top: 1rem;
            border-top: 1px solid var(--border);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-info h4 {
            font-size: 0.9rem;
            font-weight: 600;
        }

        .user-info span {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 260px;
            padding: 2rem;
            overflow-y: auto;
        }

        /* Components */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .page-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.8rem;
            font-weight: 600;
            color: #111827;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background 0.2s;
            box-shadow: 0 4px 6px -1px rgba(14, 116, 144, 0.2);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .btn-primary:disabled {
            background: #9ca3af;
            cursor: not-allowed;
            box-shadow: none;
        }

        .btn-secondary {
            background: white;
            color: var(--text-main);
            border: 1px solid var(--border);
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .btn-secondary:hover {
            background: #f8fafc;
        }

        .card {
            background: var(--surface);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
        }

        .form-input,
        .form-select {
            width: 100%;
            height: 44px;
            padding: 0 1rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 0.95rem;
            background: white;
            transition: border-color 0.2s, box-shadow 0.2s;
            display: flex;
            align-items: center;
            font-family: inherit;
        }

        .form-input:focus,
        .form-select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(14, 116, 144, 0.15);
        }

        .form-label {
            display: block;
            margin-bottom: 0.4rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
        }

        .form-label.required::after {
            content: ' *';
            color: #dc2626;
        }

        /* Toast */
        .toast {
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 8px;
            padding: 16px;
            position: fixed;
            z-index: 1000;
            left: 50%;
            bottom: 30px;
            transform: translateX(-50%);
            font-size: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
    @stack('styles')
</head>

<body>
    <aside class="sidebar">
        <div class="brand">
            <span class="brand-icon">GE</span> Endereçamento
        </div>
        <nav>
            <a href="{{ route('enderecamento.index') }}"
                class="nav-item {{ request()->routeIs('enderecamento.*') ? 'active' : '' }}"><i
                    class="ph ph-map-pin-area"></i> Endereçamentos</a>
        </nav>
        <div class="user-profile">
            @auth
                <img src="{{ auth()->user()->google_picture ?? 'https://ui-avatars.com/api/?name=User' }}" class="user-avatar"
                    alt="User">
                <div class="user-info" style="flex: 1; overflow: hidden;">
                    <h4 style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        {{ auth()->user()->google_name }}</h4>
                    <span style="font-size: 0.7rem; display: block;">{{ auth()->user()->google_email }}</span>
                </div>
            @endauth
            <form action="{{ route('auth.logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" title="Sair" id="btn-logout"
                    style="background:none; border:none; cursor:pointer; color: var(--text-muted); padding: 5px;">
                    <i class="ph ph-sign-out" style="font-size: 1.2rem;"></i>
                </button>
            </form>
        </div>
    </aside>

    <main class="main-content">
        @yield('content')
    </main>

    <script>
        function showToast(message) {
            let toast = document.createElement('div');
            toast.className = 'toast';
            toast.innerText = message;
            document.body.appendChild(toast);
            setTimeout(() => { toast.remove(); }, 3000);
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
    @stack('scripts')
</body>

</html>
