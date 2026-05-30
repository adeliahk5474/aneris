<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commission_services', function (Blueprint $table) {

            $table->uuid('service_id')->primary();

            // ── RELATIONS ──────────────────────────────────────────────
            $table->uuid('artist_id');
            $table->foreign('artist_id')
                ->references('user_id')->on('users')->cascadeOnDelete();

            $table->uuid('category_id')->nullable();
            $table->foreign('category_id')
                ->references('category_id')->on('categories')->nullOnDelete();

            // ── BASIC ──────────────────────────────────────────────────
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('will_do')->nullable();
            $table->text('wont_do')->nullable();

            // ── MEDIA ──────────────────────────────────────────────────
            $table->string('image_url')->nullable();
            $table->json('gallery_images')->nullable();

            // ── PRICING ────────────────────────────────────────────────
            $table->decimal('base_price', 12, 2)->default(0);
            $table->json('addons')->nullable(); // [{name, price, description}]

            // ── ORDER SETTINGS ─────────────────────────────────────────
            $table->unsignedSmallInteger('estimated_days')->default(7);
            $table->unsignedTinyInteger('max_revisions')->default(2);
            $table->unsignedInteger('queue_slots')->default(5);

            // ── STATUS ─────────────────────────────────────────────────
            $table->enum('status', ['active', 'inactive', 'closed'])->default('active');

            // ── STATS (denormalized) ───────────────────────────────────
            $table->decimal('avg_rating', 3, 2)->default(0);
            $table->unsignedInteger('review_count')->default(0);
            $table->unsignedInteger('order_count')->default(0);
            $table->unsignedInteger('like_count')->default(0);

            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_services');
    }
};
