<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bengkel Sampah - Login</title>
    <style>
        :root {
            --primary-bg: #E3F4F1;
            --primary-accent: #00B6A0;
            --primary-dark: #008378;
            --input-bg: #E3F4F1;
            --border-color: #C7E3DF;
            --text-main: #008378;
            --error-bg: #FFEBEB;
            --error-text: #C33;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--primary-bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            background: #fff;
            padding: 0;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            display: flex;
            width: 900px;
            min-height: 500px;
            overflow: hidden;
        }

        .left-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: var(--text-main);
            padding: 60px 40px;
            position: relative;
            background: var(--primary-bg);
        }

        .logo {
            width: 300px;
            max-width: 60vw;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo img {
            width: 100%;
            height: auto;
            object-fit: contain;
            display: block;
        }

        .brand {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 100%;
        }

        .brand h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            letter-spacing: -1px;
            color: var(--text-main);
        }

        .brand p {
            text-align: center;
            font-size: 1rem;
            opacity: 0.9;
            font-weight: 400;
            color: var(--primary-dark);
            margin-top: -60px;
        }

        .right-panel {
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(10px);
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-width: 400px;
            border-radius: 0 20px 20px 0;
            border-left: 1.5px solid var(--border-color);
        }

        .login-form h2 {
            color: var(--primary-dark);
            font-size: 2rem;
            margin-bottom: 40px;
            font-weight: 600;
            text-align: center;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group input {
            width: 100%;
            padding: 18px 20px;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            font-size: 1rem;
            background: var(--input-bg);
            transition: all 0.3s ease;
            outline: none;
            color: var(--text-main);
        }

        .form-group input:focus {
            border-color: var(--primary-accent);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(0, 182, 160, 0.08);
        }

        .form-group input::placeholder {
            color: #999;
            font-weight: 400;
        }

        .button-group {
            display: flex;
            gap: 15px;
        }

        .btn {
            flex: 1;
            padding: 18px;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 56px;
        }

        .btn-login {
            background: var(--primary-accent);
            color: white;
        }

        .btn-login:hover:not(:disabled) {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 182, 160, 0.13);
        }

        .btn-login:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .error {
            background: var(--error-bg);
            color: var(--error-text);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #fcc;
            font-size: 0.95rem;
        }

        /* Loading Spinner Styles */
        .spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
            margin-right: 8px;
        }

        .btn.loading .spinner {
            display: inline-block;
        }

        .btn.loading .btn-text {
            opacity: 0.7;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 900px) {
            .container {
                width: 98vw;
                min-width: 0;
            }
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                width: 95vw;
                max-width: 400px;
                min-height: unset;
            }

            .left-panel {
                padding: 40px 20px;
            }

            .right-panel {
                min-width: auto;
                border-radius: 0 0 20px 20px;
                border-left: none;
                border-top: 1.5px solid var(--border-color);
            }

            .brand h1 {
                font-size: 2rem;
            }

            .button-group {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="left-panel">
            <div class="logo">
                <img src="{{ asset('company/bengkelsampah.png') }}" alt="Bengkel Sampah Logo"
                    onerror="this.onerror=null;this.src='https://i.ibb.co/6b8Qw7d/bengkel-sampah-logo.png';">
            </div>
            <div class="brand" style="margin-top:2rem">
                <p>Incolabboration by:</p>
                <div class="collaboration"
                    style="margin-top:-2rem;display:flex;align-items:center;justify-content:space-between">
                    <img src="{{ asset('company/unops-logo.png') }}" alt="UNOPS Logo"
                        style="display:block;max-height:130px;margin-right:1.3rem">
                    <img src="{{ asset('company/sucofindo-logo.png') }}" alt="UNOPS Logo"
                        style="display:block;max-height:55px;margin-right:0.8rem">
                    <img src="{{ asset('company/menlhk-logo.png') }}" alt="MENLHK Logo"
                        style="display:block;max-height:45px">
                </div>
            </div>
        </div>
        <div class="right-panel">
            <div class="login-form">
                <h2>Selamat Datang</h2>
                @if ($errors->any())
                    <div class="error">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif
                <form id="loginForm" method="POST" action="{{ route('admin.login') }}">
                    @csrf
                    <div class="form-group">
                        <input type="text" name="email" placeholder="User Name" required autofocus
                            value="{{ old('email') }}">
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" placeholder="Password" required>
                    </div>
                    <div class="button-group">
                        <button type="submit" class="btn btn-login" id="loginBtn">
                            <div class="spinner"></div>
                            <span class="btn-text">Login</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const loginBtn = document.getElementById('loginBtn');
            const btnText = loginBtn.querySelector('.btn-text');

            // Show loading state
            loginBtn.classList.add('loading');
            loginBtn.disabled = true;
            btnText.textContent = 'Memproses...';

            // Form will submit normally, but button shows loading state
        });
    </script>
</body>

</html>
