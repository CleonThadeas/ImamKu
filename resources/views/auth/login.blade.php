<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ImamKu') }} — Login</title>

        <link rel="icon" type="image/svg+xml" href="{{ asset('storage/logo/Logo.svg') }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            * { box-sizing: border-box; margin: 0; padding: 0; }

            body {
                font-family: 'Inter', sans-serif;
                background: #0A0F1A;
                color: #F9FAFB;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                overflow: hidden;
            }

            .login-bg {
                position: fixed;
                inset: 0;
                background: linear-gradient(135deg, #0F2B1F 0%, #0A0F1A 50%, #1B4332 100%);
                z-index: 0;
            }

            .login-bg::before {
                content: '';
                position: absolute;
                top: -50%;
                left: -50%;
                width: 200%;
                height: 200%;
                background: radial-gradient(ellipse at center, rgba(212,168,67,0.06) 0%, transparent 60%);
                animation: pulse 8s ease-in-out infinite alternate;
            }

            .login-bg::after {
                content: '';
                position: absolute;
                bottom: -30%;
                right: -30%;
                width: 60%;
                height: 60%;
                background: radial-gradient(circle, rgba(16,185,129,0.04) 0%, transparent 60%);
                animation: pulse 10s ease-in-out infinite alternate-reverse;
            }

            @keyframes pulse {
                from { transform: scale(1); }
                to { transform: scale(1.1); }
            }

            .login-container {
                position: relative;
                z-index: 1;
                width: 100%;
                max-width: 420px;
                padding: 20px;
            }

            .login-card {
                background: rgba(17, 24, 39, 0.8);
                backdrop-filter: blur(20px);
                border: 1px solid rgba(55, 65, 81, 0.5);
                border-radius: 24px;
                padding: 44px 36px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.4);
                animation: cardEnter 0.6s ease-out;
            }

            @keyframes cardEnter {
                from { opacity: 0; transform: translateY(20px) scale(0.97); }
                to { opacity: 1; transform: translateY(0) scale(1); }
            }

            .login-brand {
                text-align: center;
                margin-bottom: 32px;
            }

            .login-brand .brand-icon {
                font-size: 3rem;
                margin-bottom: 8px;
                display: block;
            }

            .login-brand h1 {
                font-size: 1.8rem;
                font-weight: 800;
                color: #D4A843;
                letter-spacing: 1px;
            }

            .login-brand .brand-sub {
                font-size: 0.75rem;
                color: #9CA3AF;
                text-transform: uppercase;
                letter-spacing: 3px;
                margin-top: 4px;
            }

            .form-group {
                margin-bottom: 20px;
            }

            .form-label {
                display: block;
                font-size: 0.8rem;
                font-weight: 600;
                color: #9CA3AF;
                margin-bottom: 8px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .form-input {
                width: 100%;
                padding: 12px 16px;
                background: rgba(31, 41, 55, 0.6);
                border: 1px solid #374151;
                border-radius: 10px;
                color: #F9FAFB;
                font-size: 0.9rem;
                font-family: inherit;
                transition: border-color 0.2s, box-shadow 0.2s;
            }

            .form-input:focus {
                outline: none;
                border-color: #D4A843;
                box-shadow: 0 0 0 3px rgba(212, 168, 67, 0.15);
            }

            .form-input::placeholder {
                color: #6B7280;
            }

            .remember-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 24px;
            }

            .remember-row label {
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 0.8rem;
                color: #9CA3AF;
                cursor: pointer;
            }

            .remember-row input[type="checkbox"] {
                width: 16px;
                height: 16px;
                accent-color: #D4A843;
                cursor: pointer;
            }

            .btn-login {
                width: 100%;
                padding: 14px;
                background: linear-gradient(135deg, #B8922D, #D4A843);
                color: #0F2B1F;
                border: none;
                border-radius: 10px;
                font-size: 1rem;
                font-weight: 700;
                cursor: pointer;
                transition: all 0.3s ease;
                letter-spacing: 0.5px;
                font-family: inherit;
            }

            .btn-login:hover {
                background: linear-gradient(135deg, #D4A843, #E8C96A);
                transform: translateY(-2px);
                box-shadow: 0 8px 24px rgba(212, 168, 67, 0.3);
            }

            .error-msg {
                background: rgba(239, 68, 68, 0.1);
                border: 1px solid rgba(239, 68, 68, 0.3);
                color: #F87171;
                padding: 10px 14px;
                border-radius: 8px;
                font-size: 0.8rem;
                margin-bottom: 16px;
            }

            .status-msg {
                background: rgba(16, 185, 129, 0.1);
                border: 1px solid rgba(16, 185, 129, 0.3);
                color: #34D399;
                padding: 10px 14px;
                border-radius: 8px;
                font-size: 0.8rem;
                margin-bottom: 16px;
            }
        </style>
    </head>
    <body>
        <div class="login-bg"></div>

        <div class="login-container">
            <div class="login-card">
                <div class="login-brand">
                    <img src="{{ asset('storage/logo/Logo.svg') }}" alt="ImamKu" style="height:56px; width:auto; margin-bottom:8px;">
                    <h1>ImamKu</h1>
                    <div class="brand-sub">Ramadan Schedule</div>
                </div>

                @if(session('status'))
                    <div class="status-msg">{{ session('status') }}</div>
                @endif

                @if($errors->any())
                    <div class="error-msg">
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-group">
                        <label class="form-label" for="email">Email</label>
                        <input id="email" type="email" name="email" class="form-input" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="nama@email.com">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <input id="password" type="password" name="password" class="form-input" required autocomplete="current-password" placeholder="••••••••">
                    </div>

                    <div class="remember-row">
                        <label>
                            <input type="checkbox" name="remember">
                            Ingat saya
                        </label>
                    </div>

                    <button type="submit" class="btn-login">Masuk</button>
                </form>
            </div>
        </div>
    </body>
</html>
