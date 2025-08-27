<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - InOut System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --light: #f8f9fa;
            --dark: #212529;
        }

        body {
            background-color: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            max-width: 400px;
            width: 100%;
            padding: 20px;
        }

        .login-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-5px);
        }

        .login-header {
            background: linear-gradient(135deg, #4361ee, #3a0ca3);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }

        .login-body {
            padding: 30px 20px;
        }

        .form-control {
            border-radius: 10px;
            padding: 12px 20px;
            border: 2px solid #e9ecef;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: #4361ee;
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.15);
        }

        .input-group-text {
            background: transparent;
            border-radius: 10px 0 0 10px;
            border: 2px solid #e9ecef;
            border-right: none;
        }

        .btn-login {
            background: linear-gradient(135deg, #4361ee, #3a0ca3);
            border: none;
            color: white;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #3a0ca3, #4361ee);
            transform: translateY(-2px);
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 20px 0;
        }

        .divider::before,
        .divider::after {
            content: "";
            flex: 1;
            border-bottom: 1px solid #dee2e6;
        }

        .divider-text {
            padding: 0 10px;
            color: #6c757d;
            font-size: 0.9rem;
        }

        .employee-illustration {
            width: 100%;
            max-width: 250px;
            margin: 0 auto 20px;
            display: block;
        }

        .system-features {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
            text-align: center;
        }

        .feature {
            padding: 10px;
        }

        .feature i {
            font-size: 1.5rem;
            color: #4361ee;
            margin-bottom: 8px;
        }

        .feature p {
            margin: 0;
            font-size: 0.8rem;
            color: #6c757d;
        }

        .invalid-feedback {
            display: block;
            margin-top: 5px;
        }

        .alert {
            border-radius: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <i class="fas fa-users fa-3x mb-3"></i>
                <h2>InOut System</h2>
                <p class="mb-0">Employee Attendance Management</p>
            </div>

            <div class="login-body">
                <!-- Display Success Message -->
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <!-- Display General Errors -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                   name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                                   placeholder="Enter your email">
                        </div>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                                   name="password" required autocomplete="current-password" placeholder="Enter your password">
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-login w-100 mb-3">Sign In</button>

                    @if (Route::has('password.request'))
                        <div class="text-center">
                            <a href="{{ route('password.request') }}">Forgot your password?</a>
                        </div>
                    @endif

                    <div class="divider">
                        <span class="divider-text">New to the system?</span>
                    </div>

                    <div class="text-center">
                        <p>Contact your manager to get access credentials</p>
                    </div>
                </form>
            </div>
        </div>

        <div class="system-features mt-4">
            <div class="feature">
                <i class="fas fa-clock"></i>
                <p>Time Tracking</p>
            </div>
            <div class="feature">
                <i class="fas fa-camera"></i>
                <p>Photo Verification</p>
            </div>
            <div class="feature">
                <i class="fas fa-chart-line"></i>
                <p>Attendance Reports</p>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    </script>
</body>
</html>
