<?php
// database/migrations/xxxx_xx_xx_add_twoway_review_fields.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {

            // Reviewer type: 'client' reviewing artist, or 'artist' reviewing client
            if (!Schema::hasColumn('reviews', 'reviewer_type')) {
                $table->enum('reviewer_type', ['client', 'artist'])->nullable()->after('reviewer_id');
            }

            // Sub-ratings for client→artist
            if (!Schema::hasColumn('reviews', 'rating_quality')) {
                $table->tinyInteger('rating_quality')->unsigned()->nullable()->after('rating');
            }
            if (!Schema::hasColumn('reviews', 'rating_timeliness')) {
                $table->tinyInteger('rating_timeliness')->unsigned()->nullable()->after('rating_quality');
            }
            if (!Schema::hasColumn('reviews', 'rating_communication')) {
                $table->tinyInteger('rating_communication')->unsigned()->nullable()->after('rating_timeliness');
            }

            // Sub-ratings for artist→client
            if (!Schema::hasColumn('reviews', 'rating_brief')) {
                $table->tinyInteger('rating_brief')->unsigned()->nullable()->after('rating_communication');
            }
            if (!Schema::hasColumn('reviews', 'rating_attitude')) {
                $table->tinyInteger('rating_attitude')->unsigned()->nullable()->after('rating_brief');
            }
            if (!Schema::hasColumn('reviews', 'rating_revision')) {
                $table->tinyInteger('rating_revision')->unsigned()->nullable()->after('rating_attitude');
            }

            // Hidden until both submit or time expires
            if (!Schema::hasColumn('reviews', 'is_visible')) {
                $table->boolean('is_visible')->default(false)->after('rating_revision');
            }

            if (!Schema::hasColumn('reviews', 'revealed_at')) {
                $table->timestamp('revealed_at')->nullable()->after('is_visible');
            }
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn([
                'reviewer_type',
                'rating_quality',
                'rating_timeliness',
                'rating_communication',
                'rating_brief',
                'rating_attitude',
                'rating_revision',
                'is_visible',
                'revealed_at',
            ]);
        });
    }
};
