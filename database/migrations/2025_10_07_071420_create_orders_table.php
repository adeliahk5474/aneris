<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {

            $table->uuid('order_id')->primary();

            // ── RELATIONS ──────────────────────────────────────────────
            $table->uuid('client_id');
            $table->foreign('client_id')
                ->references('user_id')->on('users')->cascadeOnDelete();

            $table->uuid('artist_id');
            $table->foreign('artist_id')
                ->references('user_id')->on('users')->cascadeOnDelete();

            $table->uuid('service_id');
            $table->foreign('service_id')
                ->references('service_id')->on('commission_services')->cascadeOnDelete();

            // ── ORDER DATA ─────────────────────────────────────────────
            $table->integer('qty')->default(1);
            $table->text('note')->nullable();
            $table->json('selected_addons')->nullable();
            $table->decimal('subtotal_price', 12, 2)->default(0);
            $table->decimal('total_price', 12, 2)->default(0);

            // ── PAYMENT ────────────────────────────────────────────────
            $table->string('payment_method')->nullable();
            $table->enum('payment_status', [
                'unpaid',
                'paid',
                'failed',
                'refunded'
            ])->default('unpaid');
            $table->timestamp('paid_at')->nullable();

            // ── ORDER STATUS ───────────────────────────────────────────
            $table->enum('status', [
                'pending',
                'paid',
                'in_progress',
                'revision_requested',   // client minta revisi
                'revision',             // artist sedang revisi
                'waiting_client',       // artist kirim hasil, nunggu review
                'completed',
                'canceled',
            ])->default('pending');

            // ── PHASE (tahap pengerjaan) ────────────────────────────────
            $table->enum('phase', [
                'sketch',
                'coloring',
                'rendering',
                'final'
            ])->nullable();

            // ── REVISION ───────────────────────────────────────────────
            $table->unsignedTinyInteger('revision_count')->default(0);
            $table->unsignedTinyInteger('revision_limit')->default(2); // copy dari service saat order dibuat

            // ── DEADLINE & LATE STATUS ─────────────────────────────────
            $table->timestamp('deadline_at')->nullable();          // dihitung saat in_progress
            $table->enum('late_status', [
                'late',      // < 24 jam terlambat
                'overdue',   // 24-72 jam terlambat
                'delayed',   // > 72 jam terlambat
            ])->nullable();

            // ── EXTENSION REQUEST (1x per order) ───────────────────────
            $table->timestamp('extension_requested_at')->nullable();
            $table->text('extension_reason')->nullable();
            $table->unsignedTinyInteger('extension_days')->nullable();
            $table->enum('extension_status', [
                'pending',
                'approved',
                'rejected'
            ])->nullable();

            // ── FILES ──────────────────────────────────────────────────
            $table->string('final_file')->nullable();
            $table->string('result_file')->nullable();

            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
