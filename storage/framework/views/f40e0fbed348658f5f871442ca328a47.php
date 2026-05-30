<?php $__env->startSection('title', 'Pengaturan Homepage'); ?>

<?php $__env->startSection('content'); ?>
<?php echo app('Illuminate\Foundation\Vite')('resources/css/admin/home-setting.css'); ?>

<div class="hs-wrap">

    <div class="hs-breadcrumb">
        <a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a>
        <span class="sep">/</span>
        <span>Tampilan Home</span>
    </div>

    <div class="hs-title">Tampilan Homepage</div>

    <?php if(session('success')): ?>
    <div class="hs-alert">
        <i class="bi bi-check-circle-fill"></i> <?php echo e(session('success')); ?>

    </div>
    <?php endif; ?>

    <form action="<?php echo e(route('admin.home-setting.update')); ?>" method="POST" id="hsForm">
        <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>

        
        <div class="hs-card">
            <div class="hs-card-head">
                <i class="bi bi-type-h1"></i> Hero Section
            </div>

            <div class="hs-field">
                <label class="hs-label">Judul Hero <span>(wajib)</span></label>
                <input type="text" name="hero_title" class="hs-input"
                    value="<?php echo e(old('hero_title', $settings['hero_title'] ?? '')); ?>"
                    placeholder="For the love of human creativity" required>
                <?php $__errorArgs = ['hero_title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="hs-error"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="hs-field">
                <label class="hs-label">Subjudul Hero <span>(opsional)</span></label>
                <textarea name="hero_subtitle" class="hs-textarea"
                    placeholder="Deskripsi singkat di bawah judul..."><?php echo e(old('hero_subtitle', $settings['hero_subtitle'] ?? '')); ?></textarea>
            </div>
        </div>

        
        <div class="hs-card">
            <div class="hs-card-head">
                <i class="bi bi-card-image"></i> Banner Kiri
            </div>

            <div class="hs-field">
                <label class="hs-label">Judul</label>
                <input type="text" name="banner1_title" class="hs-input"
                    value="<?php echo e(old('banner1_title', $settings['banner1_title'] ?? '')); ?>"
                    placeholder="Made for creators" required
                    data-preview="b1title">
                <?php $__errorArgs = ['banner1_title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="hs-error"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="hs-field">
                <label class="hs-label">Deskripsi</label>
                <textarea name="banner1_subtitle" class="hs-textarea"
                    placeholder="Deskripsi banner kiri..."
                    data-preview="b1sub"><?php echo e(old('banner1_subtitle', $settings['banner1_subtitle'] ?? '')); ?></textarea>
            </div>

            <div class="hs-field">
                <label class="hs-label">Warna Latar</label>
                <div class="hs-color-row">
                    <input type="text" name="banner1_color" id="banner1_color_text" class="hs-input"
                        value="<?php echo e(old('banner1_color', $settings['banner1_color'] ?? '#1a1a2e')); ?>"
                        placeholder="#1a1a2e"
                        data-color="banner1">
                    <div class="hs-color-preview" id="banner1_preview_box"
                        style="background:<?php echo e(old('banner1_color', $settings['banner1_color'] ?? '#1a1a2e')); ?>"
                        data-picker="banner1_color_picker">
                    </div>
                    <input type="color" id="banner1_color_picker"
                        value="<?php echo e(old('banner1_color', $settings['banner1_color'] ?? '#1a1a2e')); ?>"
                        data-color="banner1">
                </div>
            </div>

            <div class="hs-preview" id="banner1_card"
                style="background:<?php echo e(old('banner1_color', $settings['banner1_color'] ?? '#1a1a2e')); ?>">
                <div class="hs-preview-title" id="b1title"><?php echo e(old('banner1_title', $settings['banner1_title'] ?? 'Made for creators')); ?></div>
                <div class="hs-preview-sub" id="b1sub"><?php echo e(old('banner1_subtitle', $settings['banner1_subtitle'] ?? '')); ?></div>
            </div>
        </div>

        
        <div class="hs-card">
            <div class="hs-card-head">
                <i class="bi bi-card-image"></i> Banner Kanan
            </div>

            <div class="hs-field">
                <label class="hs-label">Judul</label>
                <input type="text" name="banner2_title" class="hs-input"
                    value="<?php echo e(old('banner2_title', $settings['banner2_title'] ?? '')); ?>"
                    placeholder="No Generative AI" required
                    data-preview="b2title">
                <?php $__errorArgs = ['banner2_title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="hs-error"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="hs-field">
                <label class="hs-label">Deskripsi</label>
                <textarea name="banner2_subtitle" class="hs-textarea"
                    placeholder="Deskripsi banner kanan..."
                    data-preview="b2sub"><?php echo e(old('banner2_subtitle', $settings['banner2_subtitle'] ?? '')); ?></textarea>
            </div>

            <div class="hs-field">
                <label class="hs-label">Warna Latar</label>
                <div class="hs-color-row">
                    <input type="text" name="banner2_color" id="banner2_color_text" class="hs-input"
                        value="<?php echo e(old('banner2_color', $settings['banner2_color'] ?? '#0d2818')); ?>"
                        placeholder="#0d2818"
                        data-color="banner2">
                    <div class="hs-color-preview" id="banner2_preview_box"
                        style="background:<?php echo e(old('banner2_color', $settings['banner2_color'] ?? '#0d2818')); ?>"
                        data-picker="banner2_color_picker">
                    </div>
                    <input type="color" id="banner2_color_picker"
                        value="<?php echo e(old('banner2_color', $settings['banner2_color'] ?? '#0d2818')); ?>"
                        data-color="banner2">
                </div>
            </div>

            <div class="hs-preview" id="banner2_card"
                style="background:<?php echo e(old('banner2_color', $settings['banner2_color'] ?? '#0d2818')); ?>">
                <div class="hs-preview-title" id="b2title"><?php echo e(old('banner2_title', $settings['banner2_title'] ?? 'No Generative AI')); ?></div>
                <div class="hs-preview-sub" id="b2sub"><?php echo e(old('banner2_subtitle', $settings['banner2_subtitle'] ?? '')); ?></div>
            </div>
        </div>

        <div class="hs-actions">
            <button type="submit" class="btn-save">
                <i class="bi bi-floppy"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<?php echo app('Illuminate\Foundation\Vite')('resources/js/admin/home-setting.js'); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Documents\ade\aneris\resources\views/admin/home-setting.blade.php ENDPATH**/ ?>