<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aneris</title>

    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #fafafa;
        }

        .insta-nav {
            height: 56px;
        }

        .insta-brand {
            font-size: 22px;
            font-weight: 700;
        }
    </style>
</head>

<body>

    
    <nav class="navbar navbar-light bg-white border-bottom shadow-sm sticky-top insta-nav px-3">
        <div class="container-fluid d-flex justify-content-between align-items-center">

            
            <a href="<?php echo e(route('home')); ?>" class="navbar-brand text-dark insta-brand">
                Aneris
            </a>

            
            <div class="d-flex align-items-center">

                
                <a href="<?php echo e(route('explore')); ?>" class="text-dark mx-2">
                    <i class="bi bi-heart fs-4"></i>
                </a>

                
                <?php if(Route::has('cart.index')): ?>
                    <a href="<?php echo e(route('cart.index')); ?>" class="text-dark mx-2">
                        <i class="bi bi-bag fs-4"></i>
                    </a>
                <?php else: ?>
                    <a href="#" class="text-dark mx-2">
                        <i class="bi bi-bag fs-4"></i>
                    </a>
                <?php endif; ?>

            </div>

        </div>
    </nav>

    
    <div class="container-fluid" style="padding-bottom: 80px;">
        <?php echo $__env->yieldContent('content'); ?>
    </div>

    
    <?php echo $__env->make('layouts.botnav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
<?php /**PATH C:\Users\hana fathia\Documents\aneris\resources\views/layouts/app.blade.php ENDPATH**/ ?>