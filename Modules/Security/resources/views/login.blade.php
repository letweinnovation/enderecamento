<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login &bull; GTI Endereçamento</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --surface: rgba(255, 255, 255, 0.95);
            --bg-gradient: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0f766e 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-gradient);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        .blob {
            position: absolute;
            width: 500px;
            height: 500px;
            background: linear-gradient(180deg, rgba(14, 116, 144, 0.4) 0%, rgba(20, 184, 166, 0.2) 100%);
            border-radius: 50%;
            filter: blur(80px);
            z-index: 0;
            animation: float 20s infinite ease-in-out;
        }

        .blob-1 {
            top: -200px;
            left: -200px;
        }

        .blob-2 {
            bottom: -200px;
            right: -200px;
            animation-delay: -10s;
            background: linear-gradient(180deg, rgba(59, 130, 246, 0.3) 0%, rgba(37, 99, 235, 0.1) 100%);
        }

        @keyframes float {
            0% {
                transform: translate(0, 0) scale(1);
            }

            33% {
                transform: translate(30px, -50px) scale(1.1);
            }

            66% {
                transform: translate(-20px, 20px) scale(0.9);
            }

            100% {
                transform: translate(0, 0) scale(1);
            }
        }

        .card {
            background: var(--surface);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 3.5rem;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            text-align: center;
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 10;
        }

        .logo-area {
            margin-bottom: 2rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #0e7490 0%, #14b8a6 100%);
            border-radius: 16px;
            color: white;
            font-size: 28px;
            font-weight: 700;
            box-shadow: 0 10px 25px -5px rgba(14, 116, 144, 0.4);
        }

        h1 {
            color: #111827;
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .subtitle-brand {
            color: #0e7490;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.75rem;
        }

        p.subtitle {
            color: #6B7280;
            font-size: 0.95rem;
            line-height: 1.5;
            margin-bottom: 2.5rem;
        }

        .btn-google {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 0.875rem 1.5rem;
            background: white;
            border: 1px solid #E5E7EB;
            border-radius: 12px;
            color: #374151;
            font-size: 1rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-google:hover {
            border-color: #D1D5DB;
            background: #F9FAFB;
            transform: translateY(-1px);
        }

        .btn-google img {
            width: 20px;
            height: 20px;
            margin-right: 12px;
        }

        .alert {
            background: #fee2e2;
            color: #991b1b;
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .footer {
            margin-top: 2rem;
            font-size: 0.8rem;
            color: #9CA3AF;
        }
    </style>
</head>

<body>
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <div class="card">
        <div class="logo-area">GE</div>
        <div class="subtitle-brand">GTI Plug</div>
        <h1>Endereçamento</h1>
        <p class="subtitle">Faça login com sua conta institucional<br><strong>@letwe.com.br</strong> para continuar.</p>

        @if(session('error'))
            <div class="alert">{{ session('error') }}</div>
        @endif

        <a href="{{ $authUrl }}" class="btn-google" id="btn-login-google">
            <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google Logo">
            Entrar com Google
        </a>

        <div class="footer">&copy; {{ date('Y') }} Letwe Innovation</div>
    </div>
</body>

</html>
