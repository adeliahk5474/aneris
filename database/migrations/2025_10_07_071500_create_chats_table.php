<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chats', function (Blueprint $table) {

            // PRIMARY KEY
            $table->uuid('chat_id')->primary();

            // ===============================
            // OPTIONAL ORDER CHAT
            // ===============================
            $table->uuid('order_id')->nullable();

            $table->foreign('order_id')
                ->references('order_id')
                ->on('orders')
                ->cascadeOnDelete();

            // ===============================
            // USERS
            // ===============================
            $table->uuid('sender_id');

            $table->foreign('sender_id')
                ->references('user_id')
                ->on('users')
                ->cascadeOnDelete();

            $table->uuid('receiver_id');

            $table->foreign('receiver_id')
                ->references('user_id')
                ->on('users')
                ->cascadeOnDelete();

            // ===============================
            // CONTENT
            // ===============================
            $table->text('message')->nullable();

            // image upload
            $table->string('image')->nullable();

            // ===============================
            // STATUS
            // ===============================
            $table->boolean('is_read')
                ->default(false);

            // ===============================
            // TIMESTAMPS
            // ===============================
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
