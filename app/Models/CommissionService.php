<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CommissionService extends Model
{
    protected $table = 'commission_services';

    protected $primaryKey = 'service_id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'artist_id',
        'category_id',

        'title',
        'description',
        'will_do',
        'wont_do',

        'image_url',
        'gallery_images',

        'base_price',
        'addons',

        'estimated_days',
        'max_revisions',
        'queue_slots',

        'status',

        'avg_rating',
        'review_count',
        'order_count',
        'like_count',
    ];

    protected $casts = [
        'gallery_images' => 'array',
        'addons'         => 'array',

        'base_price' => 'float',
        'avg_rating' => 'float',
    ];

    protected static function booted(): void
    {
        static::creating(function ($service) {

            if (empty($service->service_id)) {
                $service->service_id = (string) Str::uuid();
            }
        });
    }

    /* =========================================================
     | RELATIONS
    ========================================================= */

    public function artist()
    {
        return $this->belongsTo(User::class, 'artist_id', 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'service_id', 'service_id');
    }

    public function reviews()
    {
        return $this->hasManyThrough(
            Review::class,
            Order::class,
            'service_id',
            'order_id',
            'service_id',
            'order_id'
        );
    }

    /* =========================================================
     | HELPERS
    ========================================================= */

    public function estimatedDeadline(): Carbon
    {
        return now()->addDays($this->estimated_days ?? 7);
    }

    public function willDoList(): array
    {
        if (!$this->will_do) {
            return [];
        }

        return array_values(array_filter(
            array_map('trim', explode("\n", $this->will_do))
        ));
    }

    public function wontDoList(): array
    {
        if (!$this->wont_do) {
            return [];
        }

        return array_values(array_filter(
            array_map('trim', explode("\n", $this->wont_do))
        ));
    }

    public function recalculateStats(): void
    {
        $stats = $this->reviews()
            ->where('is_visible', true)
            ->where('reviewer_type', 'client')
            ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as total_review')
            ->first();

        $completedOrders = $this->orders()
            ->where('status', 'completed')
            ->count();

        $this->update([
            'avg_rating' => round($stats->avg_rating ?? 0, 2),
            'review_count' => (int) ($stats->total_review ?? 0),
            'order_count' => $completedOrders,
        ]);
    }

    /* =========================================================
     | SCOPES
    ========================================================= */

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeRanked($query)
    {
        return $query->orderByRaw("
            (
                (avg_rating * LOG(review_count + 2))
                + (order_count * 0.1)
            ) DESC
        ");
    }

    public function scopeHasSlot($query)
    {
        return $query->whereRaw("
            queue_slots > (
                SELECT COUNT(*)
                FROM orders
                WHERE orders.service_id = commission_services.service_id
                AND orders.status NOT IN ('completed', 'canceled')
            )
        ");
    }
}
