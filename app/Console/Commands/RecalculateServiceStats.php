<?php
// app/Console/Commands/RecalculateServiceStats.php
// Jalankan SEKALI setelah migrate: php artisan services:recalculate-stats

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CommissionService;

class RecalculateServiceStats extends Command
{
    protected $signature   = 'services:recalculate-stats';
    protected $description = 'Recalculate avg_rating, review_count, order_count for all commission services';

    public function handle(): int
    {
        $services = CommissionService::all();
        $bar      = $this->output->createProgressBar($services->count());
        $bar->start();

        foreach ($services as $service) {
            $service->recalculateStats();
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Done! {$services->count()} services updated.");

        return self::SUCCESS;
    }
}
