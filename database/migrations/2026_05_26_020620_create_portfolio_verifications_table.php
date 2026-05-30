<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolio_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('artist_id')->constrained('users', 'user_id')->cascadeOnDelete();

            $table->enum('status', ['pending', 'in_review', 'approved', 'rejected'])
                ->default('pending');

            // File portofolio & sosmed
            $table->json('portfolio_files')->nullable();
            $table->json('social_media_links')->nullable();

            // AI pre-screening
            $table->unsignedTinyInteger('ai_score_reference')->nullable();
            $table->text('ai_score_notes')->nullable();

            // Skor admin — sosmed (total 40)
            $table->unsignedTinyInteger('score_social_style')->nullable();    // 0–10
            $table->unsignedTinyInteger('score_social_age')->nullable();      // 0–10
            $table->unsignedTinyInteger('score_social_wip')->nullable();      // 0–10
            $table->unsignedTinyInteger('score_social_comments')->nullable(); // 0–10
            $table->text('admin_notes_social')->nullable();

            // Skor admin — portofolio (total 60)
            $table->unsignedTinyInteger('score_portfolio')->nullable();       // 0–60
            $table->text('admin_notes_portfolio')->nullable();

            // Hasil
            $table->unsignedTinyInteger('total_score')->nullable();           // 0–100
            $table->text('admin_notes_final')->nullable();                    // wajib saat kirim

            // Meta
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('next_eligible_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_verifications');
    }
};
