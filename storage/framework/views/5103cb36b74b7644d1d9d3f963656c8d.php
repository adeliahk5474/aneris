
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aneris — Join the Community</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/auth.css']); ?>
</head>

<body>

    <div class="auth-layout">

        
        <div class="auth-visual">
            <div class="visual-top">
                <div class="visual-logo">
                    <div class="visual-logo-mark">A</div>
                    <span class="visual-logo-text">Aneris</span>
                </div>
                <div class="visual-headline" id="visual-headline">Creative Exchange</div>
                <div class="visual-sub" id="visual-sub">
                    The exclusive digital gallery for professional services and artist discovery.
                </div>
            </div>

            <div class="visual-bottom">
                <div class="visual-card">
                    <div class="visual-card-title">Curated Discovery</div>
                    <div class="visual-card-text">
                        Connect with elite creators using our dynamic, social-inspired feed.
                        From concept art to high-end motion graphics, find your next masterpiece here.
                    </div>
                    <div class="visual-avatars">
                        <div class="visual-avatar" style="background: linear-gradient(135deg,#8b5cf6,#ec4899);"></div>
                        <div class="visual-avatar" style="background: linear-gradient(135deg,#06b6d4,#8b5cf6);"></div>
                        <div class="visual-avatar" style="background: linear-gradient(135deg,#f59e0b,#ef4444);"></div>
                        <span class="visual-avatar-text" id="visual-avatar-text">Joined by 10k+ artists</span>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="auth-form-panel">

            
            <div class="auth-tabs">
                <div class="auth-tab active" onclick="showForm('login')">Login</div>
                <div class="auth-tab" onclick="showForm('register')">Register</div>
            </div>

            
            <?php if(session('success')): ?>
            <div class="alert-success"><?php echo e(session('success')); ?></div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
            <div class="alert-error"><?php echo e($errors->first()); ?></div>
            <?php endif; ?>

            
            <div class="auth-form active" id="login">

                <div class="form-headline">Welcome back</div>
                <div class="form-sub">Enter your details to access your account.</div>

                <form method="POST" action="<?php echo e(route('auth.login')); ?>">
                    <?php echo csrf_field(); ?>

                    
                    <div class="field-group">
                        <div class="field-label">Email address</div>
                        <div class="field-wrap">
                            <i class="bi bi-envelope field-icon"></i>
                            <input type="email" name="email" class="field-input"
                                placeholder="name@example.com"
                                value="<?php echo e(old('email')); ?>" required autocomplete="email">
                        </div>
                    </div>

                    
                    <div class="field-group">
                        <div class="field-label">
                            Password
                            <a href="#">Forgot password?</a>
                        </div>
                        <div class="field-wrap">
                            <i class="bi bi-lock field-icon"></i>
                            <input type="password" name="password" class="field-input" id="loginPass"
                                placeholder="••••••••" required autocomplete="current-password">
                            <button type="button" class="field-toggle" onclick="togglePass('loginPass', this)">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    
                    <div class="check-row">
                        <input type="checkbox" name="remember" id="remember" class="check-input">
                        <label for="remember" class="check-label">Remember me for 30 days</label>
                    </div>

                    <button type="submit" class="btn-submit">Sign In</button>

                </form>

                <div class="switch-text">
                    Don't have an account? <a href="javascript:void(0)" onclick="showForm('register')">Register now</a>
                </div>

            </div>

            
            <div class="auth-form" id="register">

                <div class="form-headline">Create your account</div>
                <div class="form-sub">Join the community of premium digital artists and clients.</div>

                <form method="POST" action="<?php echo e(route('auth.register')); ?>">
                    <?php echo csrf_field(); ?>

                    
                    <div class="identity-label">Select your identity</div>
                    <div class="identity-grid">
                        <label class="identity-card selected" id="card-artist">
                            <input type="radio" name="role" value="artist" checked
                                onchange="selectIdentity('artist')">
                            <span class="identity-icon">🎨</span>
                            <div class="identity-title">I am an Artist</div>
                            <div class="identity-desc">Showcase and sell your professional work.</div>
                        </label>
                        <label class="identity-card" id="card-client">
                            <input type="radio" name="role" value="client"
                                onchange="selectIdentity('client')">
                            <span class="identity-icon">🔍</span>
                            <div class="identity-title">I am a Client</div>
                            <div class="identity-desc">Discover and commission exclusive talent.</div>
                        </label>
                    </div>

                    
                    <div class="field-group">
                        <div class="field-label">Full Name</div>
                        <div class="field-wrap">
                            <i class="bi bi-person field-icon"></i>
                            <input type="text" name="full_name" class="field-input"
                                placeholder="Enter your legal name"
                                value="<?php echo e(old('full_name')); ?>" required>
                        </div>
                    </div>

                    
                    <div class="field-group">
                        <div class="field-label">Email Address</div>
                        <div class="field-wrap">
                            <i class="bi bi-envelope field-icon"></i>
                            <input type="email" name="email" class="field-input"
                                placeholder="name@example.com"
                                value="<?php echo e(old('email')); ?>" required>
                        </div>
                    </div>

                    
                    <div class="field-row">
                        <div class="field-group">
                            <div class="field-label">Password</div>
                            <div class="field-wrap">
                                <i class="bi bi-lock field-icon"></i>
                                <input type="password" name="password" class="field-input" id="regPass"
                                    placeholder="••••••••" required>
                                <button type="button" class="field-toggle" onclick="togglePass('regPass', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="field-group">
                            <div class="field-label">Confirm Password</div>
                            <div class="field-wrap">
                                <i class="bi bi-lock field-icon"></i>
                                <input type="password" name="password_confirmation" class="field-input" id="regPassConf"
                                    placeholder="••••••••" required>
                                <button type="button" class="field-toggle" onclick="togglePass('regPassConf', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    
                    <div class="check-row">
                        <input type="checkbox" id="terms" class="check-input" required>
                        <label for="terms" class="check-label">
                            I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>.
                        </label>
                    </div>

                    <button type="submit" class="btn-submit">Create Account</button>

                </form>

                <div class="switch-text">
                    Already have an account? <a href="javascript:void(0)" onclick="showForm('login')">Log in</a>
                </div>

            </div>

            
            <div class="auth-footer">
                <a href="#">Terms of Service</a>
                <a href="#">Privacy Policy</a>
                <a href="#">Cookies</a>
            </div>

        </div>
    </div>

    <?php echo app('Illuminate\Foundation\Vite')(['resources/js/auth.js']); ?>

    
    <?php if($openRegisterForm): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            showForm('register');
        });
    </script>
    <?php endif; ?>

</body>

</html>
<?php /**PATH C:\Users\User\Documents\ade\aneris\resources\views/auth/auth.blade.php ENDPATH**/ ?>