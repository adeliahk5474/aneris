<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('follows', function (Blueprint $table) {

            $table->uuid('follow_id')->primary();

            // USER YANG FOLLOW
            $table->uuid('follower_id');

            $table->foreign('follower_id')
                ->references('user_id')
                ->on('users')
                ->cascadeOnDelete();

            // USER YANG DIFOLLOW
            $table->uuid('following_id');

            $table->foreign('following_id')
                ->references('user_id')
                ->on('users')
                ->cascadeOnDelete();

            // anti duplicate
            $table->unique(['follower_id', 'following_id']);

            $table->timestampsTz();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('follows');
    }
};
