<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — SENSTOCK ITSM</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --brand: #889ABF;
            --brand-dark: #6878A0;
            --brand-pale: #EEF1F7;
            --gray-900: #1A1F2E;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Sora', sans-serif;
            min-height: 100vh;
            display: flex;
            background: var(--gray-900);
            overflow: hidden;
        }

        .login-left {
            flex: 1;
            background: linear-gradient(135deg, #1A1F2E 0%, #2D3348 50%, #3D4560 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px;
            position: relative;
            overflow: hidden;
        }

        .login-left::before {
            content: '';
            position: absolute;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(136,154,191,.25) 0%, transparent 70%);
            top: -100px; right: -100px;
        }

        .login-left::after {
            content: '';
            position: absolute;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(136,154,191,.15) 0%, transparent 70%);
            bottom: -100px; left: -100px;
        }

        .login-left-content { position: relative; z-index: 1; text-align: center; }

        .login-logo {
            width: 90px; height: 90px;
            background: white;
            border-radius: 20px;
            padding: 10px;
            margin: 0 auto 28px;
            object-fit: contain;
            box-shadow: 0 8px 32px rgba(0,0,0,.3);
        }

        .login-brand-name {
            font-size: 32px;
            font-weight: 700;
            color: white;
            letter-spacing: 2px;
            margin-bottom: 6px;
        }

        .login-brand-sub {
            font-size: 13px;
            color: rgba(255,255,255,.5);
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 48px;
        }

        .login-feature {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 20px;
            background: rgba(136,154,191,.12);
            border: 1px solid rgba(136,154,191,.2);
            border-radius: 12px;
            margin-bottom: 10px;
            text-align: left;
            width: 100%;
            max-width: 320px;
        }

        .login-feature-icon {
            width: 36px; height: 36px;
            background: var(--brand);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
            flex-shrink: 0;
        }

        .login-feature-text { color: rgba(255,255,255,.8); font-size: 13px; }
        .login-feature-title { color: white; font-weight: 600; font-size: 13px; margin-bottom: 2px; }

        /* Right panel */
        .login-right {
            width: 480px;
            flex-shrink: 0;
            background: #F8F9FC;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 50px;
        }

        .login-form-wrap { width: 100%; }

        .login-title {
            font-size: 26px;
            font-weight: 700;
            color: #1A1F2E;
            margin-bottom: 8px;
        }

        .login-subtitle {
            font-size: 14px;
            color: #9BA3B8;
            margin-bottom: 36px;
        }

        .form-group { margin-bottom: 18px; }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #5A6380;
            margin-bottom: 7px;
        }

        .input-wrap {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: #9BA3B8;
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 11px 14px 11px 40px;
            border: 1.5px solid #DDE1EC;
            border-radius: 10px;
            font-family: 'Sora', sans-serif;
            font-size: 14px;
            color: #2D3348;
            background: white;
            transition: all .2s ease;
            outline: none;
        }

        .form-control:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px rgba(136,154,191,.2);
        }

        .form-control.is-invalid { border-color: #E85555; }

        .invalid-feedback {
            color: #E85555;
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 24px;
        }

        .form-check input { accent-color: var(--brand); }
        .form-check label { font-size: 13px; color: #5A6380; cursor: pointer; }

        .btn-login {
            width: 100%;
            padding: 13px;
            background: var(--brand);
            color: white;
            border: none;
            border-radius: 10px;
            font-family: 'Sora', sans-serif;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all .2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-login:hover {
            background: var(--brand-dark);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(136,154,191,.4);
        }

        .alert-danger {
            background: #FDEEEE;
            color: #9b2c2c;
            border-left: 4px solid #E85555;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .login-footer {
            margin-top: 32px;
            text-align: center;
            color: #9BA3B8;
            font-size: 12px;
        }

        @media (max-width: 900px) {
            .login-left { display: none; }
            .login-right { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="login-left">
        <div class="login-left-content">
            <img src="{{ asset('images/logo.jpg') }}" alt="SENSTOCK" class="login-logo">
            <div class="login-brand-name">SENSTOCK</div>
            <div class="login-brand-sub">Sénégalaise de Stockage</div>

            <div class="login-feature">
                <div class="login-feature-icon"><i class="fa-solid fa-ticket"></i></div>
                <div>
                    <div class="login-feature-title">Ticketing centralisé</div>
                    <div class="login-feature-text">Toutes les demandes IT en un seul endroit</div>
                </div>
            </div>

            <div class="login-feature">
                <div class="login-feature-icon"><i class="fa-solid fa-clock"></i></div>
                <div>
                    <div class="login-feature-title">Suivi SLA automatique</div>
                    <div class="login-feature-text">Alertes et horodatage T0 à T6</div>
                </div>
            </div>

            <div class="login-feature">
                <div class="login-feature-icon"><i class="fa-solid fa-chart-line"></i></div>
                <div>
                    <div class="login-feature-title">KPI & Tableaux de bord</div>
                    <div class="login-feature-text">Pilotez la performance IT en temps réel</div>
                </div>
            </div>
        </div>
    </div>

    <div class="login-right">
        <div class="login-form-wrap">
            <h1 class="login-title">Bienvenue 👋</h1>
            <p class="login-subtitle">Connectez-vous à votre espace ITSM SENSTOCK</p>

            @if($errors->any())
                <div class="alert-danger">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="email">Adresse e-mail</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-envelope input-icon"></i>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}"
                            placeholder="votre@senstock.sn"
                            autofocus
                            required
                        >
                    </div>
                    @error('email')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Mot de passe</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-lock input-icon"></i>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="••••••••"
                            required
                        >
                    </div>
                    @error('password')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-check">
                    <input type="checkbox" id="remember" name="remember" value="1">
                    <label for="remember">Se souvenir de moi</label>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    Se connecter
                </button>
            </form>

            <div class="login-footer">
                © {{ date('Y') }} SENSTOCK — Plateforme ITSM v1.0
            </div>
        </div>
    </div>
</body>
</html>
