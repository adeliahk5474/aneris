<?php
// app/Observers/ReviewObserver.php
// Daftarkan di AppServiceProvider:
//   Review::observe(ReviewObserver::class);

namespace App\Observers;

use App\Models\Review;
use App\Models\CommissionService;
use Illuminate\Support\Facades\Log;

class ReviewObserver
{
    /**
     * Setelah review disimpan (created atau updated),
     * recalculate avg_rating & review_count di commission_services.
     */
    public function saved(Review $review): void
    {
        $this->recalculate($review);
    }

    public function deleted(Review $review): void
    {
        $this->recalculate($review);
    }

    private function recalculate(Review $review): void
    {
        try {
            $serviceId = $review->order?->service_id;
            if (!$serviceId) return;

            $service = CommissionService::find($serviceId);
            $service?->recalculateStats();
        } catch (\Exception $e) {
            Log::warning('ReviewObserver recalculate failed: ' . $e->getMessage());
        }
    }
}
