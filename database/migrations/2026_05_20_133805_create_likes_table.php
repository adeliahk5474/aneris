<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('likes', function (Blueprint $table) {

            $table->uuid('like_id')->primary();

            $table->uuid('user_id');
            $table->foreign('user_id')
                ->references('user_id')->on('users')->cascadeOnDelete();

            // Polymorphic: bisa like artwork atau commission_service
            $table->string('likeable_id');   // UUID sebagai string
            $table->string('likeable_type'); // 'artwork' | 'commission_service'

            $table->timestamp('created_at')->useCurrent();

            // Satu user hanya bisa like satu item satu kali
            $table->unique(['user_id', 'likeable_id', 'likeable_type'], 'likes_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('likes');
    }
};
