<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — Aneris</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <?php echo app('Illuminate\Foundation\Vite')('resources/css/admin/login.css'); ?>
</head>

<body>
    <div class="login-card">
        <div class="login-logo">
            <div class="login-logo-mark">A</div>
            <span class="login-logo-text">Aneris</span>
        </div>

        <div class="login-title">Admin Panel</div>
        <div class="login-sub">Masuk untuk mengelola verifikasi artist</div>

        <form method="POST" action="<?php echo e(route('admin.login')); ?>" id="admin-login-form">
            <?php echo csrf_field(); ?>

            <div class="form-group">
                <label for="email">Email</label>
                <div class="input-wrap">
                    <i class="bi bi-envelope input-icon"></i>
                    <input type="email" name="email" id="email"
                        value="<?php echo e(old('email')); ?>"
                        placeholder="admin@aneris.com"
                        autocomplete="email"
                        autofocus>
                </div>
                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="field-error"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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
                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="field-error"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <button type="submit" class="btn-login" id="btnLogin">
                <span id="btnText">Masuk</span>
                <span id="btnLoading" style="display:none;">
                    <i class="bi bi-arrow-clockwise spin"></i> Memproses...
                </span>
            </button>
        </form>
    </div>

    <?php echo app('Illuminate\Foundation\Vite')('resources/js/admin/login.js'); ?>
</body>

</html><?php /**PATH C:\Users\User\Documents\ade\aneris\resources\views/admin/login.blade.php ENDPATH**/ ?>