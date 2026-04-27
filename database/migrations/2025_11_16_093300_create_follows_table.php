<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('orders')) {
        Schema::create('follows', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('follower_id');  // user yang mengikuti
            $table->unsignedBigInteger('following_id'); // user yang diikuti

            $table->timestamps();

            // relasi ke users
            $table->foreign('follower_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('following_id')->references('id')->on('users')->onDelete('cascade');

            // cegah duplicate follow
            $table->unique(['follower_id', 'following_id']);
        });
    }}

    public function down(): void
    {
        Schema::dropIfExists('follows');
    }
};
