@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <style>
        .login-shell {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 28px;
            background: linear-gradient(135deg, #f1efe9 0%, #dfeaf5 100%);
        }

        .login-panel {
            width: min(1180px, 100%);
            min-height: 680px;
            display: grid;
            grid-template-columns: 1.1fr 1.15fr;
            background: rgba(255, 255, 255, 0.48);
            border: 1px solid rgba(148, 163, 184, 0.2);
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 0 30px 70px rgba(70, 76, 86, 0.14);
            backdrop-filter: blur(14px);
        }

        .login-brand-panel {
            background: linear-gradient(135deg, rgba(241, 239, 233, 0.98), rgba(223, 234, 245, 0.8));
            padding: 36px 36px 28px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-right: 1px solid rgba(148, 163, 184, 0.18);
        }

        .brand-top {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 2.1rem;
            font-weight: 800;
            color: #2f2d2a;
        }

        .brand-top .brand-mark {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #d9c4a1, #b7a58a);
            color: #fff;
            font-size: 1.2rem;
            box-shadow: 0 12px 24px rgba(183, 165, 138, 0.35);
        }

        .brand-copy {
            padding: 8px 0 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: 300px;
        }

        .brand-copy .eyebrow {
            margin: 0 0 12px;
            text-transform: uppercase;
            letter-spacing: 0.14em;
            font-size: 0.74rem;
            font-weight: 700;
            color: rgba(62, 60, 57, 0.7);
        }

        .brand-copy h1 {
            margin: 0;
            font-size: clamp(2.2rem, 4.2vw, 4.3rem);
            line-height: 0.98;
            color: #1f1d1b;
            font-weight: 800;
            letter-spacing: -0.05em;
        }

        .login-card {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 34px 32px;
            background: linear-gradient(135deg, rgba(6, 18, 36, 0.92), rgba(10, 23, 43, 0.9));
        }

        .login-card-inner {
            width: min(100%, 420px);
            padding: 8px 0;
        }

        .login-head {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            margin-bottom: 8px;
        }

        .login-head .brand-mark {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #d9c4a1, #b7a58a);
            color: #fdfaf4;
            box-shadow: 0 10px 18px rgba(183, 165, 138, 0.3);
            font-weight: 700;
        }

        .login-card h3 {
            margin: 0;
            font-weight: 800;
            color: #f4efe8;
            font-size: clamp(2rem, 3vw, 2.7rem);
            letter-spacing: -0.04em;
            text-align: center;
        }

        .subtitle {
            margin: 18px 0 20px;
            text-align: center;
            color: rgba(216, 223, 232, 0.82);
            font-size: 0.96rem;
            line-height: 1.5;
        }

        .role-switch {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 18px;
        }

        .role-btn {
            border: 1px solid rgba(255, 255, 255, 0.16);
            background: rgba(148, 163, 184, 0.08);
            color: rgba(237, 242, 247, 0.9);
            border-radius: 12px;
            padding: 0.75rem 0.9rem;
            font-weight: 700;
            transition: all 0.2s ease;
        }

        .role-btn.is-active {
            background: linear-gradient(135deg, rgba(217, 196, 161, 0.9), rgba(183, 165, 138, 0.9));
            color: #201d1a;
            border-color: rgba(217, 196, 161, 0.95);
            box-shadow: 0 12px 20px rgba(183, 165, 138, 0.25);
        }

        form {
            margin-top: 6px;
        }

        .field {
            margin-bottom: 0.95rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.55rem;
            font-weight: 600;
            color: rgba(237, 242, 247, 0.9);
            font-size: 0.96rem;
        }

        .password-wrap {
            position: relative;
        }

        .form-control {
            width: 100%;
            min-height: 52px;
            border-radius: 14px;
            border: 1px solid rgba(148, 163, 184, 0.25);
            background: rgba(255, 255, 255, 0.12);
            color: #f5f7fb;
            padding: 0.85rem 3rem 0.85rem 1rem;
            outline: none;
        }

        .form-control::placeholder {
            color: rgba(226, 233, 241, 0.5);
        }

        .form-control:focus {
            border-color: rgba(217, 196, 161, 0.8);
            box-shadow: 0 0 0 0.22rem rgba(217, 196, 161, 0.15);
            background: rgba(255, 255, 255, 0.14);
            color: #fff;
        }


        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: rgba(255, 255, 255, 0.75);
            font-size: 1.05rem;
            line-height: 1;
            width: 30px;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            cursor: pointer;
        }

        .btn-submit {
            width: 100%;
            min-height: 54px;
            border: none;
            border-radius: 14px;
            background: linear-gradient(135deg, #d9c4a1, #c8b08f);
            color: #1d1a17;
            font-weight: 800;
            letter-spacing: 0.02em;
            box-shadow: 0 16px 28px rgba(183, 165, 138, 0.28);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 18px 28px rgba(183, 165, 138, 0.32);
        }

        @media (max-width: 920px) {
            .login-panel {
                grid-template-columns: 1fr;
                min-height: auto;
            }

            .login-brand-panel {
                border-right: none;
                border-bottom: 1px solid rgba(148, 163, 184, 0.18);
                min-height: 220px;
            }

        }

        .login-shell {
            background: #f5f6f8;
        }

        .login-panel {
            background: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(33, 37, 41, 0.08);
            backdrop-filter: none;
        }

        .login-brand-panel {
            background: #f8f9fa;
            border-color: #dee2e6;
        }

        .brand-top {
            color: #212529;
            font-size: 1.75rem;
        }

        .brand-top .brand-mark,
        .login-head .brand-mark {
            background: #0d6efd;
            border-radius: 6px;
            box-shadow: none;
        }

        .brand-copy h1 {
            color: #212529;
            font-size: 3rem;
            font-weight: 700;
            letter-spacing: 0;
            line-height: 1.1;
        }

        .login-card {
            background: #ffffff;
        }

        .login-card h3 {
            color: #212529;
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: 0;
        }

        .subtitle {
            color: #6c757d;
        }

        .role-btn {
            background: #ffffff;
            border: 1px solid #ced4da;
            border-radius: 6px;
            color: #212529;
            transition: none;
        }

        .role-btn.is-active {
            background: #0d6efd;
            border-color: #0d6efd;
            box-shadow: none;
            color: #ffffff;
        }

        .form-label {
            color: #212529;
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            min-height: auto;
            padding-left: 0;
            margin: 0.75rem 0 1.25rem;
        }

        .form-check-input {
            width: 18px !important;
            height: 18px !important;
            min-width: 18px !important;
            max-width: 18px !important;
            min-height: 18px !important;
            max-height: 18px !important;
            box-sizing: border-box !important;
            border-radius: 3px !important;
            border: 1.5px solid #ced4da !important;
            background-color: #ffffff;
            cursor: pointer;
            margin: 0 !important;
            padding: 0 !important;
            float: none !important;
            flex-shrink: 0;
            appearance: none;
            -webkit-appearance: none;
            display: inline-block;
            vertical-align: middle;
            transition: all 0.15s ease;
        }

        .form-check-input:hover {
            border-color: #0d6efd !important;
        }

        .form-check-input:focus {
            outline: 0;
            border-color: #86b7fe !important;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.2) !important;
        }

        .form-check-input:checked {
            background-color: #0d6efd !important;
            border-color: #0d6efd !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23ffffff' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round' d='M3.5 8.5l3 3 6-6'/%3e%3c/svg%3e") !important;
            background-repeat: no-repeat !important;
            background-position: center !important;
            background-size: 13px 13px !important;
        }

        .form-check-label {
            margin: 0;
            cursor: pointer;
            font-size: 0.94rem;
            font-weight: 500;
            color: #212529;
            user-select: none;
            line-height: 1.2;
            display: inline-flex;
            align-items: center;
        }

        .form-control {
            min-height: 44px;
            padding: 0.625rem 2.8rem 0.625rem 0.75rem;
            background: #ffffff;
            border: 1px solid #ced4da;
            border-radius: 6px;
            color: #212529;
        }

        .form-control:focus {
            background: #ffffff;
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
            color: #212529;
        }

        .form-control:-webkit-autofill,
        .form-control:-webkit-autofill:hover,
        .form-control:-webkit-autofill:focus {
            -webkit-text-fill-color: #212529;
            box-shadow: 0 0 0 1000px #ffffff inset;
        }

        .password-toggle {
            color: #6c757d;
            border-radius: 4px;
        }

        .btn-submit,
        .btn-submit:hover {
            min-height: 44px;
            background: #0d6efd;
            border: 1px solid #0d6efd;
            border-radius: 6px;
            box-shadow: none;
            color: #ffffff;
            font-weight: 600;
            letter-spacing: 0;
            transition: none;
            transform: none;
        }

        body.dark-mode .login-card {
            background: #2b3035;
            border-left: 1px solid #495057;
        }

        body.dark-mode .login-card h3,
        body.dark-mode .login-card .subtitle,
        body.dark-mode .login-card .form-label {
            color: #f8f9fa !important;
        }

        body.dark-mode .login-card .form-check-label {
            color: #f8f9fa !important;
        }

        body.dark-mode .login-card .form-check-input {
            background-color: #343a40;
            border-color: #6c757d;
        }

        body.dark-mode .login-card .form-check-input:hover {
            border-color: #86b7fe;
        }

        body.dark-mode .login-card .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        body.dark-mode .login-card .form-control {
            background: #343a40;
            border-color: #495057;
            color: #ffffff;
            caret-color: #ffffff;
            -webkit-text-fill-color: #ffffff;
        }

        body.dark-mode .login-card .form-control::placeholder {
            color: #adb5bd;
        }

        @media (max-width: 920px) {
            .login-card {
                border-top: 1px solid #dee2e6;
            }
        }
    </style>

    <div class="login-shell">
        <div class="login-panel">
            <aside class="login-brand-panel">
                <div class="brand-top">
                    <span class="brand-mark">I</span>
                    <span>Inventaris</span>
                </div>

                <div class="brand-copy">
                    <p class="eyebrow">Sistem inventaris</p>
                    <h1>Kelola barang<br>lebih rapi.</h1>
                </div>
            </aside>

            <section class="login-card">
                <div class="login-card-inner">
                    <div class="login-head">
                        <div class="brand-mark">I</div>
                    </div>

                    <h3>Login Inventaris</h3>
                    <p class="subtitle">Masuk untuk mengelola data barang dan transaksi.</p>

                    <div class="role-switch" aria-label="Pilih role login">
                        <button type="button" class="role-btn is-active" data-username="admin" data-password="password">Admin</button>
                        <button type="button" class="role-btn" data-username="pegawai" data-password="password">User</button>
                    </div>

                    <form method="POST" action="{{ url('/login') }}">
                        @csrf

                        <div class="field">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" name="username" id="username" class="form-control" value="{{ old('username', 'admin') }}" autocomplete="username" required>
                        </div>

                        <div class="field">
                            <label for="password" class="form-label">Password</label>
                            <div class="password-wrap">
                                <input type="password" name="password" id="password" class="form-control" value="password" required>
                                <button type="button" class="password-toggle" id="passwordToggle" aria-label="Lihat password" aria-pressed="false">
                                    <i class="bi bi-eye" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label" for="remember">Ingat saya</label>
                        </div>

                        <button type="submit" class="btn-submit">Masuk</button>
                    </form>
                </div>
            </section>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const roleButtons = document.querySelectorAll('.role-btn');
            const usernameInput = document.getElementById('username');
            const passwordInput = document.getElementById('password');
            const passwordToggle = document.getElementById('passwordToggle');

            roleButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    roleButtons.forEach(function (item) {
                        item.classList.toggle('is-active', item === button);
                    });

                    usernameInput.value = button.dataset.username || '';
                    passwordInput.value = button.dataset.password || '';
                    passwordInput.focus();
                });
            });

            if (passwordToggle && passwordInput) {
                passwordToggle.addEventListener('click', function () {
                    const isPassword = passwordInput.type === 'password';
                    passwordInput.type = isPassword ? 'text' : 'password';
                    passwordToggle.innerHTML = `<i class="bi ${isPassword ? 'bi-eye-slash' : 'bi-eye'}" aria-hidden="true"></i>`;
                    passwordToggle.setAttribute('aria-label', isPassword ? 'Sembunyikan password' : 'Lihat password');
                    passwordToggle.setAttribute('aria-pressed', isPassword ? 'true' : 'false');
                });
            }
        });
    </script>
@endsection
