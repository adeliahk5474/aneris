<?php
    $hideBottomNavbar = true;
?>


<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50 text-gray-800 p-6">


<div class="flex justify-between items-center mb-6">
    
    <a href="<?php echo e(route('profile.show', auth()->id())); ?>" class="text-indigo-600 hover:text-indigo-800">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
    </a>

    
    <h1 class="text-2xl md:text-3xl font-bold text-indigo-600">Dashboard Artist</h1>

    
    <div class="flex items-center space-x-3">
        <img src="<?php echo e($artist->avatar ?? asset('images/default-avatar.png')); ?>" class="w-12 h-12 md:w-14 md:h-14 rounded-full object-cover">
        <span class="text-xl md:text-2xl font-bold"><?php echo e($artist->name); ?></span>
    </div>
</div>


    
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
        <div class="bg-white p-5 rounded-xl text-center shadow">
            <p class="text-gray-500 text-sm">Komisi Aktif</p>
            <h3 class="text-2xl font-bold text-indigo-600"><?php echo e($activeCommissions); ?></h3>
        </div>

        <div class="bg-white p-5 rounded-xl text-center shadow">
            <p class="text-gray-500 text-sm">Pendapatan</p>
            <h3 class="text-2xl font-bold text-indigo-600">Rp<?php echo e(number_format($totalEarnings,0,',','.')); ?></h3>
        </div>

        <div class="bg-white p-5 rounded-xl text-center shadow">
            <p class="text-gray-500 text-sm">Rating</p>
            <h3 class="text-2xl font-bold text-yellow-500">
                <?php echo e($averageRating ? number_format($averageRating,1).'★' : 'Belum ada'); ?>

            </h3>
        </div>

        <div class="bg-white p-5 rounded-xl text-center shadow">
            <p class="text-gray-500 text-sm">Total Services</p>
            <h3 class="text-2xl font-bold text-indigo-600"><?php echo e($totalServices); ?></h3>
        </div>
    </div>

    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white p-5 rounded-xl shadow text-center">
            <p class="text-gray-500 text-sm">Klien Aktif</p>
            <h3 class="text-2xl font-bold text-indigo-600"><?php echo e($activeClients); ?></h3>
        </div>

        <div class="bg-white p-5 rounded-xl shadow text-center">
            <p class="text-gray-500 text-sm">Komisi Menunggu</p>
            <h3 class="text-2xl font-bold text-indigo-600"><?php echo e($pendingCommissions); ?></h3>
        </div>

        <div class="bg-white p-5 rounded-xl shadow text-center">
            <p class="text-gray-500 text-sm">Notifikasi Terbaru</p>
            <h3 class="text-2xl font-bold text-indigo-600"><?php echo e($recentNotifications); ?></h3>
        </div>
    </div>

    
    <div class="bg-white p-5 rounded-xl shadow mb-10">
        <h2 class="text-xl font-semibold mb-4">Pendapatan Per Bulan</h2>
        <canvas id="earningsChart" class="w-full h-64"></canvas>
    </div>

</div>


<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('earningsChart').getContext('2d');
    const earningsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($monthlyLabels, 15, 512) ?>, // ['Jan','Feb','Mar',...]
            datasets: [{
                label: 'Pendapatan',
                data: <?php echo json_encode($monthlyEarnings, 15, 512) ?>, // [100000, 200000,...]
                borderColor: '#6366F1',
                backgroundColor: 'rgba(99,102,241,0.2)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hana fathia\Documents\aneris\resources\views/dashboards/artist.blade.php ENDPATH**/ ?>