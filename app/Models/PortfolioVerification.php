<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortfolioVerification extends Model
{
    protected $fillable = [
        'artist_id',
        'status',
        'portfolio_files',
        'social_media_links',
        'ai_score_reference',
        'ai_score_notes',
        'score_social_style',
        'score_social_age',
        'score_social_wip',
        'score_social_comments',
        'admin_notes_social',
        'score_portfolio',
        'admin_notes_portfolio',
        'total_score',
        'admin_notes_final',
        'admin_id',
        'reviewed_at',
        'next_eligible_at',
    ];

    protected function casts(): array
    {
        return [
            'portfolio_files'    => 'array',
            'social_media_links' => 'array',
            'reviewed_at'        => 'datetime',
            'next_eligible_at'   => 'datetime',
        ];
    }

    // ── RELATIONS ──
    public function artist()
    {
        return $this->belongsTo(User::class, 'artist_id', 'user_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    // ── HELPERS ──
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function canResubmit(): bool
    {
        if (!$this->isRejected()) return false;
        if (!$this->next_eligible_at) return true;
        return now()->greaterThanOrEqualTo($this->next_eligible_at);
    }

    public function calculateTotal(): int
    {
        return (int) (
            ($this->score_social_style    ?? 0) +
            ($this->score_social_age      ?? 0) +
            ($this->score_social_wip      ?? 0) +
            ($this->score_social_comments ?? 0) +
            ($this->score_portfolio       ?? 0)
        );
    }
}
