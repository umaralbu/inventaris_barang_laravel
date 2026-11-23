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

        .login-shell {
            background: linear-gradient(145deg, #edf4f3, #f8faf9 56%, #f5edef);
        }

        .login-panel {
            width: min(1120px, 100%);
            min-height: 620px;
            border-radius: 10px;
            border-color: rgba(20, 66, 75, 0.16);
            box-shadow: 0 24px 64px rgba(24, 57, 65, 0.13);
        }

        .login-brand-panel {
            position: relative;
            overflow: hidden;
            background: linear-gradient(125deg, #123f54 0%, #116a70 72%, #a83e4d 100%);
            border: 0;
            padding: 42px;
        }

        .login-brand-panel::after {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background: repeating-linear-gradient(135deg, rgba(255,255,255,0.07) 0 1px, transparent 1px 18px);
        }

        .login-brand-panel > * {
            position: relative;
            z-index: 1;
        }

        .brand-top {
            gap: 12px;
            color: #ffffff;
            font-size: 1rem;
        }

        .brand-top-copy {
            display: grid;
            gap: 3px;
        }

        .brand-top-copy strong {
            font-size: 1.05rem;
            line-height: 1.1;
        }

        .brand-top-copy small {
            max-width: 250px;
            color: rgba(255, 255, 255, 0.74);
            font-size: 0.62rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            line-height: 1.35;
            text-transform: uppercase;
        }

        .brand-top .brand-mark,
        .login-head .brand-mark {
            flex: 0 0 42px;
            font-size: 0.64rem;
            font-weight: 800;
            letter-spacing: 0.03em;
            background: rgba(255, 255, 255, 0.13);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 8px;
            box-shadow: none;
            color: #ffffff;
        }

        .brand-copy .eyebrow {
            color: rgba(255, 255, 255, 0.76);
            letter-spacing: 0.08em;
        }

        .brand-copy h1 {
            max-width: 490px;
            color: #ffffff;
            font-size: clamp(2.4rem, 4vw, 3.8rem);
            line-height: 1.02;
            letter-spacing: 0;
        }

        .brand-description {
            max-width: 400px;
            margin: 1.1rem 0 0;
            color: rgba(255, 255, 255, 0.82);
            line-height: 1.6;
        }

        .login-card {
            background: #ffffff;
        }

        .login-card h3 {
            color: #17343c;
            font-size: 1.8rem;
            font-family: "Montserrat", sans-serif;
            font-weight: 900;
        }

        .login-card .subtitle {
            color: #647a80;
        }

        .form-check-input:checked,
        .form-check-input:hover,
        .form-check-input:focus {
            border-color: #303684 !important;
        }

        .form-check-input:checked {
            background-color: #303684 !important;
        }

        .form-control:focus {
            border-color: #0e6670;
            box-shadow: 0 0 0 0.2rem rgba(14, 102, 112, 0.14);
        }

        .btn-submit,
        .btn-submit:hover {
            background: #0e6670;
            border-color: #0e6670;
        }

        body.dark-mode .login-card {
            background: #19333c;
            border-color: rgba(184, 213, 214, 0.16);
        }

        body.dark-mode .login-card h3,
        body.dark-mode .login-card .subtitle,
        body.dark-mode .login-card .form-label,
        body.dark-mode .login-card .form-check-label {
            color: #edf5f4 !important;
        }

        @media (max-width: 920px) {
            .login-brand-panel {
                min-height: 260px;
                padding: 30px;
            }

            .brand-copy {
                min-height: 150px;
            }

            .brand-copy h1 {
                font-size: 2.5rem;
            }
        }

        @media (max-width: 575.98px) {
            .login-shell {
                padding: 12px;
            }

            .login-brand-panel {
                min-height: 230px;
                padding: 22px;
            }

            .brand-copy h1 {
                font-size: 2rem;
                font-family: "Montserrat", sans-serif;
                font-weight: 900;
            }

            .login-card {
                padding: 28px 20px;
            }
        }

        .login-shell {
            min-height: 100vh;
            padding: 0;
            align-items: stretch;
            justify-content: stretch;
            background: #ffffff;
        }

        .login-panel {
            width: 100%;
            min-height: 100vh;
            grid-template-columns: minmax(0, 1.03fr) minmax(420px, 0.97fr);
            border: 0;
            border-radius: 0;
            box-shadow: none;
        }

        .login-brand-panel {
            position: relative;
            isolation: isolate;
            overflow: hidden;
            min-height: 100vh;
            padding: clamp(32px, 5vw, 72px);
            border: 0;
            background-color: #252a68;
            background-image:
                url("{{ asset('login-gradient-waves.svg') }}?v=2"),
                radial-gradient(ellipse at 18% 18%, rgba(105, 117, 230, 0.42), transparent 38%),
                radial-gradient(ellipse at 78% 78%, rgba(38, 161, 181, 0.18), transparent 34%),
                linear-gradient(138deg, #252a68 0%, #303684 48%, #3b4399 100%);
            background-size: 100% 100%, cover, cover, cover;
            color: #ffffff;
        }

        .login-brand-panel::before {
            content: "";
            position: absolute;
            z-index: -1;
            inset: 0;
            background-color: rgba(177, 182, 232, 0.1);
            -webkit-mask: url("{{ asset('geometric-pattern.svg') }}") 0 0 / 64px 56px repeat;
            mask: url("{{ asset('geometric-pattern.svg') }}") 0 0 / 64px 56px repeat;
        }

        .login-brand-panel::after {
            content: none;
        }

        .brand-top {
            justify-content: flex-start;
            gap: 0;
            color: #ffffff;
            font-size: 1rem;
        }

        .ums-wordmark {
            display: block;
            width: min(380px, 100%);
            padding: 10px 14px;
            border: 1px solid rgba(48, 54, 132, 0.08);
            border-radius: 14px;
            background: #ffffff;
            box-shadow: 0 12px 30px rgba(17, 22, 75, 0.16);
        }

        .ums-wordmark img {
            display: block;
            width: 100%;
            height: auto;
        }

        .brand-copy {
            min-height: 0;
            margin: auto 0;
            padding: 48px 0;
            justify-content: center;
        }

        .brand-copy .eyebrow {
            margin-bottom: 14px;
            color: #f5c400;
            font-family: "Poppins", sans-serif;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.1em;
        }

        .brand-copy h1 {
            max-width: 540px;
            color: #ffffff;
            font-family: "Montserrat", sans-serif;
            font-size: clamp(2.5rem, 4vw, 3.9rem);
            font-weight: 900;
            letter-spacing: 0;
            line-height: 1.08;
        }

        .title-dot {
            color: #f5c400;
        }

        .brand-description {
            max-width: 500px;
            margin: 20px 0 0;
            color: rgba(255, 255, 255, 0.8);
            font-family: "Poppins", sans-serif;
            font-size: 0.94rem;
            font-weight: 500;
            line-height: 1.65;
        }

        .login-brand-footer {
            margin: 0;
            color: rgba(255, 255, 255, 0.72);
            font-size: 0.75rem;
            font-weight: 500;
        }

        .login-library-logo {
            display: block;
            width: min(132px, 36vw);
            height: auto;
            margin: 8px auto 0;
        }

        .login-card {
            position: relative;
            overflow: hidden;
            align-items: center;
            width: auto;
            min-width: 0;
            padding: 56px clamp(28px, 7vw, 96px);
            background:
                radial-gradient(ellipse at 14% 12%, rgba(213, 218, 255, 0.72), transparent 38%),
                linear-gradient(112deg, #bac2eb 0%, #e0e4fa 22%, #f0f1ff 48%, #ffffff 100%);
            border: 0;
            border-radius: 0;
            box-shadow: none;
        }

        .login-card-inner {
            position: relative;
            z-index: 1;
            width: min(100%, 400px);
            padding: 0;
        }

        .login-card::before {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background-color: rgba(110, 142, 163, 0.14);
            -webkit-mask: url("{{ asset('geometric-pattern.svg') }}") 0 0 / 64px 56px repeat;
            mask: url("{{ asset('geometric-pattern.svg') }}") 0 0 / 64px 56px repeat;
        }

        .login-head {
            display: none;
        }

        .login-card h3 {
            position: relative;
            margin: 0 0 24px;
            padding-bottom: 15px;
            color: #303684;
            font-family: "Montserrat", sans-serif;
            font-size: 1.65rem;
            font-weight: 900;
            letter-spacing: 0;
            text-align: center;
        }

        .login-card h3::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 48px;
            height: 4px;
            border-radius: 99px;
            background: #f5c400;
            transform: translateX(-50%);
        }

        .login-card .subtitle {
            margin: -10px 0 24px;
            color: #59617b;
            font-size: 0.83rem;
            line-height: 1.65;
            text-align: center;
        }

        .field {
            margin-bottom: 16px;
        }

        .form-label {
            margin-bottom: 7px;
            color: #303684;
            font-size: 0.78rem;
            font-weight: 700;
        }

        .form-control {
            min-height: 44px;
            padding: 0.65rem 2.8rem 0.65rem 0.85rem;
            border: 1px solid #d5d9e7;
            border-radius: 10px;
            background: #ffffff;
            color: #283044;
            font-size: 0.86rem;
        }

        .form-control:focus {
            border-color: #5057a6;
            box-shadow: 0 0 0 0.2rem rgba(48, 54, 132, 0.12);
            color: #283044;
        }

        .form-check {
            margin: 4px 0 20px;
        }

        .login-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
            margin: 4px 0 20px;
        }

        .login-options .form-check {
            margin: 0;
        }

        .forgot-password {
            padding: 0;
            border: 0;
            background: transparent;
            color: #303684;
            font-size: 0.76rem;
            font-weight: 700;
            cursor: pointer;
        }

        .forgot-password:hover,
        .forgot-password:focus-visible {
            color: #242a70;
            text-decoration: underline;
        }

        .login-help {
            margin: -8px 0 16px;
            color: #59617b;
            font-size: 0.76rem;
            line-height: 1.5;
        }

        .form-check-label {
            color: #59617b;
            font-size: 0.78rem;
        }

        .form-check-input:checked {
            border-color: #303684 !important;
            background-color: #303684 !important;
        }

        .password-toggle {
            color: #69718a;
        }

        .btn-submit,
        .btn-submit:hover {
            min-height: 46px;
            border: 0;
            border-bottom: 3px solid #f5c400;
            border-radius: 10px;
            background: #303684;
            box-shadow: none;
            color: #ffffff;
            font-size: 0.84rem;
            font-weight: 700;
            letter-spacing: 0;
            transition: background-color 0.15s ease;
        }

        .btn-submit:hover {
            background: #272d78;
        }

        .login-copyright {
            margin: 24px 0 0;
            color: #69718a;
            font-size: 0.68rem;
            line-height: 1.5;
            text-align: center;
        }

        body.dark-mode .login-card {
            background: #ffffff;
        }

        body.dark-mode .login-card h3,
        body.dark-mode .login-card .subtitle,
        body.dark-mode .login-card .form-label,
        body.dark-mode .login-card .form-check-label,
        body.dark-mode .login-copyright {
            color: #303684 !important;
        }

        body.dark-mode .login-card .form-control {
            background: #ffffff;
            border-color: #d5d9e7;
            color: #283044;
            -webkit-text-fill-color: #283044;
        }

        .login-card::after {
            content: "";
            position: absolute;
            z-index: 0;
            inset: 0 auto 0 0;
            width: clamp(260px, 38vw, 540px);
            pointer-events: none;
            background: linear-gradient(
                90deg,
                rgba(48, 54, 132, 0.38) 0%,
                rgba(58, 65, 155, 0.3) 24%,
                rgba(93, 101, 178, 0.18) 52%,
                rgba(145, 150, 203, 0.06) 80%,
                rgba(48, 54, 132, 0) 100%
            );
        }

        @media (max-width: 920px) {
            .login-shell {
                align-items: flex-start;
            }

            .login-panel {
                grid-template-columns: minmax(0, 1fr);
                min-height: 100vh;
            }

            .login-brand-panel {
                min-height: 300px;
                padding: 28px clamp(22px, 6vw, 42px);
            }

            .brand-copy {
                min-height: 150px;
                margin: 28px 0 12px;
                padding: 0;
                flex-shrink: 0;
            }

            .brand-copy > * {
                flex-shrink: 0;
            }

            .brand-copy h1 {
                font-size: clamp(2rem, 7vw, 3rem);
            }

            .brand-description {
                margin-top: 12px;
                font-size: 0.84rem;
            }

            .login-card {
                padding: 42px 24px 48px;
            }

            .login-card::after {
                inset: 0 0 auto;
                width: auto;
                height: 210px;
                background: linear-gradient(
                    180deg,
                    rgba(48, 54, 132, 0.38) 0%,
                    rgba(58, 65, 155, 0.3) 24%,
                    rgba(93, 101, 178, 0.18) 52%,
                    rgba(145, 150, 203, 0.06) 80%,
                    rgba(48, 54, 132, 0) 100%
                );
            }
        }

        @media (max-width: 575.98px) {
            .login-brand-panel {
                min-height: 270px;
                padding: 22px;
            }

            .ums-wordmark {
                width: min(320px, 100%);
            }

            .ums-wordmark img {
                width: 100%;
            }

            .brand-copy {
                margin-top: 22px;
            }

            .brand-copy .eyebrow {
                margin-bottom: 9px;
                font-size: 0.67rem;
            }

            .brand-copy h1 {
                font-size: 2rem;
            }

            .brand-description {
                max-width: 360px;
                font-size: 0.76rem;
            }

            .login-card {
                padding: 34px 22px 42px;
            }

            .login-card h3 {
                font-size: 1.45rem;
            }
        }
    </style>

    <div class="login-shell">
        <div class="login-panel">
            <aside class="login-brand-panel">
                <div class="brand-top">
                    <span class="ums-wordmark">
                        <img src="{{ asset('ums-logo-color.webp') }}" alt="Universitas Muhammadiyah Surakarta">
                    </span>
                </div>

                <div class="brand-copy">
                    <p class="eyebrow">Sistem inventaris perpustakaan</p>
                    <h1>Inventaris<br>Perpustakaan<span class="title-dot">.</span></h1>
                    <p class="brand-description">Kelola aset ruang baca, referensi, sirkulasi, pengolahan, dan ruang pendukung dalam satu sistem.</p>
                </div>

                <div class="login-brand-footer">
                    <span>Universitas Muhammadiyah Surakarta</span>
                </div>
            </aside>

            <section class="login-card">
                <div class="login-card-inner">
                    <div class="login-head">
                        <div class="brand-mark">UMS</div>
                    </div>

                    <h3>Masuk ke sistem</h3>
                    <p class="subtitle">Portal inventaris Perpustakaan UMS. Masuk dengan akun yang terdaftar.</p>

                    <form method="POST" action="{{ url('/login') }}">
                        @csrf

                        <div class="field">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" name="username" id="username" class="form-control" value="{{ old('username') }}" autocomplete="username" required>
                        </div>

                        <div class="field">
                            <label for="password" class="form-label">Password</label>
                            <div class="password-wrap">
                                <input type="password" name="password" id="password" class="form-control" autocomplete="current-password" required>
                                <button type="button" class="password-toggle" id="passwordToggle" aria-label="Lihat password" aria-pressed="false">
                                    <i class="bi bi-eye" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>

                        <div class="login-options">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                <label class="form-check-label" for="remember">Ingat saya</label>
                            </div>
                            <button type="button" class="forgot-password" id="forgotPasswordButton" aria-controls="loginHelp" aria-expanded="false">Lupa password?</button>
                        </div>
                        <p class="login-help" id="loginHelp" hidden>Fitur reset mandiri belum tersedia. Hubungi administrator Perpustakaan UMS untuk bantuan akun.</p>

                        <button type="submit" class="btn-submit">Masuk</button>
                    </form>

                    <img class="login-library-logo" src="{{ asset('ums-library-logo.png') }}" alt="Logo UMS Library">
                    <p class="login-copyright">&copy; Perpustakaan Universitas Muhammadiyah Surakarta</p>
                </div>
            </section>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordInput = document.getElementById('password');
            const passwordToggle = document.getElementById('passwordToggle');
            const forgotPasswordButton = document.getElementById('forgotPasswordButton');
            const loginHelp = document.getElementById('loginHelp');

            if (passwordToggle && passwordInput) {
                passwordToggle.addEventListener('click', function () {
                    const isPassword = passwordInput.type === 'password';
                    passwordInput.type = isPassword ? 'text' : 'password';
                    passwordToggle.innerHTML = `<i class="bi ${isPassword ? 'bi-eye-slash' : 'bi-eye'}" aria-hidden="true"></i>`;
                    passwordToggle.setAttribute('aria-label', isPassword ? 'Sembunyikan password' : 'Lihat password');
                    passwordToggle.setAttribute('aria-pressed', isPassword ? 'true' : 'false');
                });
            }

            if (forgotPasswordButton && loginHelp) {
                forgotPasswordButton.addEventListener('click', function () {
                    const isExpanded = forgotPasswordButton.getAttribute('aria-expanded') === 'true';
                    forgotPasswordButton.setAttribute('aria-expanded', String(!isExpanded));
                    loginHelp.hidden = isExpanded;
                });
            }
        });
    </script>
@endsection
