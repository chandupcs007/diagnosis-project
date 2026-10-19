<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - {{ config('diagnosis.name') }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --login-overlay: linear-gradient(135deg, rgba(26, 35, 126, 0.88) 0%, rgba(0, 131, 143, 0.82) 100%);
        }

        body {
            font-family: 'Nunito', sans-serif;
            min-height: 100vh;
            margin: 0;
            position: relative;
            background: #1a237e;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: var(--login-overlay),
                url('{{ $hospitalSetting->logo_url ?? '' }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            z-index: 0;
        }

        @if(!$hospitalSetting->logo_url)
        body::before {
            background-image: var(--login-overlay),
                radial-gradient(circle at 20% 20%, rgba(255,255,255,0.08) 0%, transparent 45%),
                radial-gradient(circle at 80% 80%, rgba(0,188,212,0.15) 0%, transparent 40%),
                linear-gradient(160deg, #283593 0%, #006064 100%);
        }
        @endif

        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background: radial-gradient(circle at center, transparent 35%, rgba(0, 0, 0, 0.25) 100%);
            pointer-events: none;
            z-index: 0;
        }

        .login-page-wrap {
            position: relative;
            z-index: 1;
            width: 100%;
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 2rem 1rem;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.97);
            border-radius: 18px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            backdrop-filter: blur(6px);
        }

        .login-container > .row {
            align-items: stretch;
            min-height: 34rem;
        }

        .login-left,
        .login-right {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-left {
            background: linear-gradient(160deg, rgba(40, 53, 147, 0.95) 0%, rgba(0, 131, 143, 0.92) 100%);
            color: white;
            padding: 2.75rem 2.5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .login-left::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url('{{ $hospitalSetting->logo_url ?? '' }}');
            background-size: cover;
            background-position: center;
            opacity: 0.12;
        }

        .login-left-content {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 18rem;
            margin: 0 auto;
        }

        .hospital-logo-wrap {
            width: 88px;
            height: 88px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.95);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            padding: 0.5rem;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        }

        .hospital-logo-wrap img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .medical-icon {
            font-size: 2.5rem;
            margin-bottom: 0;
            opacity: 0.95;
        }

        .login-right {
            padding: 2.75rem 2.5rem;
            background: #fff;
        }

        .login-form-wrap {
            width: 100%;
            max-width: 22rem;
            margin: 0 auto;
        }

        .login-brand-logo {
            width: 4.5rem;
            height: 4.5rem;
            margin: 0 auto 1rem;
            border-radius: 50%;
            overflow: hidden;
            box-shadow: 0 4px 14px rgba(78, 115, 223, 0.25);
        }

        .login-brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .login-form-header h3 {
            font-weight: 700;
            margin-bottom: 0.35rem;
        }

        .login-features {
            margin-top: 1.75rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            text-align: left;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            font-size: 0.95rem;
        }

        .feature-item i {
            width: 1.1rem;
            text-align: center;
            opacity: 0.9;
        }

        .input-group-text {
            background: #f8f9fc;
            border: 2px solid #e3e6f0;
            border-right: 0;
            color: #6c757d;
        }

        .input-group .form-control {
            border-left: 0;
        }

        .input-group:focus-within .input-group-text,
        .input-group:focus-within .form-control {
            border-color: #4e73df;
        }

        .form-control {
            border: 2px solid #e3e6f0;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(78, 115, 223, 0.4);
        }
        .demo-credentials {
            background: #f8f9fc;
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1.5rem;
            font-size: 0.9rem;
        }

        @media (max-width: 767.98px) {
            .login-page-wrap {
                padding: 1rem;
            }

            .login-container > .row {
                min-height: auto;
            }

            .login-left,
            .login-right {
                padding: 2rem 1.5rem;
            }
        }
    </style>
</head>
<body data-auth-form="login">
    <div class="login-page-wrap">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-11 col-lg-10 col-xl-9">
                <div class="login-container">
                    <div class="row g-0">
                        <div class="col-md-6 login-left">
                            <div class="login-left-content">
                                <div class="hospital-logo-wrap">
                                    @if($hospitalSetting->logo_url)
                                        <img src="{{ $hospitalSetting->logo_url }}" alt="{{ $hospitalSetting->hospital_name }}">
                                    @else
                                        <i class="fas fa-hospital medical-icon"></i>
                                    @endif
                                </div>
                                <h2 class="mb-2">{{ $hospitalSetting->hospital_name }}</h2>
                                <p class="mb-1 opacity-75">{{ $hospitalSetting->department_name }}</p>
                                <p class="mb-4 small opacity-75">{{ config('diagnosis.name') }} · {{ config('diagnosis.description') }}</p>
                                <div class="login-features">
                                    <div class="feature-item mb-3">
                                        <i class="fas fa-shield-alt"></i>
                                        <span>Secure & HIPAA Compliant</span>
                                    </div>
                                    <div class="feature-item mb-3">
                                        <i class="fas fa-bolt"></i>
                                        <span>Real-time Diagnosis</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-chart-line"></i>
                                        <span>Advanced Analytics</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Right Side - Login Form -->
                        <div class="col-md-6 login-right">
                            <div class="login-form-wrap">
                            <div class="login-brand-logo">
                                <img src="{{ asset('images/carevision-brand.jpg') }}" alt="{{ config('diagnosis.name') }}">
                            </div>
                            <div class="login-form-header text-center mb-4">
                                <h3 class="text-gray-900">Welcome Back</h3>
                                <p class="text-muted mb-0">Sign in to your account</p>
                            </div>

                            @if($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    @foreach($errors->all() as $error)
                                        <p class="mb-0">{{ $error }}</p>
                                    @endforeach
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            @if(session('status'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('status') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('login') }}" data-auth-form>
                                @csrf
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-envelope"></i>
                                        </span>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="{{ old('email') }}" placeholder="Enter your email" required autofocus>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                        <input type="password" class="form-control" id="password" name="password" 
                                               placeholder="Enter your password" required>
                                    </div>
                                </div>

                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                    <label class="form-check-label" for="remember">Remember me</label>
                                </div>

                                <button type="submit" class="btn btn-login btn-primary w-100">
                                    <i class="fas fa-sign-in-alt me-2"></i>Sign In
                                </button>
                            </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/fullscreen.js') }}"></script>
</body>
</html>