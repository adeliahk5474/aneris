
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Aneris'); ?></title>

    
    <link rel="icon" type="image/png" href="<?php echo e(asset('favicon.png')); ?>">
    <link rel="apple-touch-icon" href="<?php echo e(asset('favicon.png')); ?>">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">
    <?php echo app('Illuminate\Foundation\Vite')('resources/css/layouts/app.css'); ?>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body>

    <nav id="app-topnav" role="banner">

        
        <a href="<?php echo e(route('home')); ?>" class="app-logo">
            <div class="app-logo-mark">A</div>
            <span class="app-logo-text">Aneris</span>
        </a>

        
        <div class="app-nav-center">
            <a href="<?php echo e(route('home')); ?>"
                class="app-nav-link <?php echo e(request()->routeIs('home') ? 'active' : ''); ?>">
                <span>Home</span>
            </a>
            <a href="<?php echo e(route('explore')); ?>"
                class="app-nav-link <?php echo e(request()->routeIs('explore') ? 'active' : ''); ?>">
                <span>Search</span>
            </a>
            <a href="<?php echo e(route('chat.list')); ?>"
                class="app-nav-link <?php echo e(request()->routeIs('chat.*') ? 'active' : ''); ?>">
                <span style="position:relative; display:inline-flex; align-items:center; gap:6px;">
                    Chat
                    <span id="chat-nav-badge" class="chat-nav-badge <?php echo e($unreadChatCount > 0 ? 'visible' : ''); ?>">
                        <?php echo e($unreadChatCount > 99 ? '99+' : ($unreadChatCount ?: '')); ?>

                    </span>
                </span>
            </a>
            <a href="<?php echo e(auth()->check() ? route('profile.show', $authUser->user_id) : route('auth.form')); ?>"
                class="app-nav-link <?php echo e(request()->routeIs('profile.*') ? 'active' : ''); ?>">
                <span>Profile</span>
            </a>
        </div>

        
        <div class="app-nav-right">

            <?php if(!$isArtist): ?>
            <a href="<?php echo e(route('cart.index')); ?>" class="app-icon-btn" aria-label="Cart">
                <i class="bi bi-bag"></i>
            </a>
            <?php endif; ?>

            
            <a href="<?php echo e(route('notifications.index')); ?>"
                class="app-icon-btn <?php echo e($unreadNotifCount > 0 ? 'has-unread' : ''); ?>"
                aria-label="Notifications">
                <i class="bi bi-bell"></i>
                <span class="notif-badge" aria-hidden="true"></span>
            </a>

            <?php if(auth()->guard()->guest()): ?>
            <a href="<?php echo e(route('auth.form')); ?>" class="btn-artist">I'm an artist+</a>
            <a href="<?php echo e(route('auth.form')); ?>" class="btn-signup">Sign up</a>
            <?php endif; ?>

            <?php if(auth()->guard()->check()): ?>
            <div class="profile-menu-wrap" id="profileMenuWrap">
                <button class="profile-trigger" id="profileTrigger"
                    aria-haspopup="true" aria-expanded="false"
                    onclick="toggleProfileMenu(event)" type="button">
                    <img src="<?php echo e($authUser->avatar ?? asset('images/default-avatar.png')); ?>"
                        alt="<?php echo e($authUser->name); ?>" class="app-avatar">
                    <span class="profile-trigger-name"><?php echo e($authUser->name); ?></span>
                    <i class="bi bi-chevron-down profile-trigger-chevron"></i>
                </button>

                <div class="profile-dropdown" id="profileDropdown" role="menu">
                    <div class="pd-user-info">
                        <div class="pd-user-name"><?php echo e($authUser->name); ?></div>
                        <div class="pd-user-email"><?php echo e($authUser->email); ?></div>
                    </div>

                    <a href="<?php echo e(route('profile.show', $authUser->user_id)); ?>" class="pd-item" role="menuitem">
                        <i class="bi bi-person"></i> My Profile
                    </a>

                    <?php if(!$isArtist): ?>
                    <a href="<?php echo e(route('cart.index')); ?>" class="pd-item" role="menuitem">
                        <i class="bi bi-bag"></i> My Orders
                    </a>
                    <?php endif; ?>

                    <a href="<?php echo e(route('notifications.index')); ?>" class="pd-item" role="menuitem">
                        <i class="bi bi-bell"></i> Notifications
                        <?php if($unreadNotifCount > 0): ?>
                        <span style="margin-left:auto;background:var(--accent);color:#fff;font-size:10px;font-weight:700;padding:1px 6px;border-radius:999px;">
                            <?php echo e($unreadNotifCount > 99 ? '99+' : $unreadNotifCount); ?>

                        </span>
                        <?php endif; ?>
                    </a>

                    <?php if($isArtist): ?>
                    <a href="<?php echo e(route('artist.dashboard')); ?>" class="pd-item" role="menuitem">
                        <i class="bi bi-brush"></i> Artist Dashboard
                    </a>
                    <?php endif; ?>

                    <div class="pd-divider"></div>
                    <form action="<?php echo e(route('auth.logout')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="pd-item danger" role="menuitem">
                            <i class="bi bi-box-arrow-right"></i> Log Out
                        </button>
                    </form>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </nav>

    <div class="app-topnav-spacer" aria-hidden="true"></div>

    <main class="app-content">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    
    <?php if(auth()->guard()->check()): ?>
    <div id="chat-notif-toast">
        <div id="chat-notif-top">
            <div id="chat-notif-left">
                <img id="chat-notif-avatar" src="" alt="">
                <div>
                    <div id="chat-notif-name"></div>
                    <div id="chat-notif-message"></div>
                </div>
            </div>
            <button id="chat-notif-close" onclick="window._closeChatNotif()">&times;</button>
        </div>
    </div>

    <meta name="auth-user-id" content="<?php echo e(Auth::user()->user_id); ?>">
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php echo app('Illuminate\Foundation\Vite')('resources/js/layouts/app.js'); ?>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>

</html>
<?php /**PATH C:\Users\User\Documents\ade\aneris\resources\views/layouts/app.blade.php ENDPATH**/ ?>