<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — Aneris</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite('resources/css/admin/login.css')
</head>

<body>
    <div class="login-card">
        <div class="login-logo">
            <div class="login-logo-mark">A</div>
            <span class="login-logo-text">Aneris</span>
        </div>

        <div class="login-title">Admin Panel</div>
        <div class="login-sub">Masuk untuk mengelola verifikasi artist</div>

        <form method="POST" action="{{ route('admin.login') }}" id="admin-login-form">
            @csrf

            <div class="form-group">
                <label for="email">Email</label>
                <div class="input-wrap">
                    <i class="bi bi-envelope input-icon"></i>
                    <input type="email" name="email" id="email"
                        value="{{ old('email') }}"
                        placeholder="admin@aneris.com"
                        autocomplete="email"
                        autofocus>
                </div>
                @error('email')
                <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrap">
                    <i class="bi bi-lock input-icon"></i>
                    <input type="password" name="password" id="password"
                        placeholder="••••••••"
                        autocomplete="current-password">
                    <button type="button" class="toggle-pass" id="togglePassword" tabindex="-1">
                        <i class="bi bi-eye" id="toggleIcon"></i>
                    </button>
                </div>
                @error('password')
                <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-login" id="btnLogin">
                <span id="btnText">Masuk</span>
                <span id="btnLoading" style="display:none;">
                    <i class="bi bi-arrow-clockwise spin"></i> Memproses...
                </span>
            </button>
        </form>
    </div>

    @vite('resources/js/admin/login.js')
</body>

</html>