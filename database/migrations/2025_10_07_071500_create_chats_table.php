<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->uuid('chat_id')->primary();

            $table->uuid('commission_id')->nullable();
            $table->foreign('commission_id')
                  ->references('commission_request_id')
                  ->on('commission_requests')
                  ->onDelete('cascade');

            $table->uuid('sender_id')->nullable();
            $table->foreign('sender_id')
                  ->references('user_id')
                  ->on('users')
                  ->nullOnDelete();

            $table->uuid('receiver_id')->nullable();
            $table->foreign('receiver_id')
                  ->references('user_id')
                  ->on('users')
                  ->nullOnDelete();

            $table->text('message')->nullable();
            $table->timestampTz('sent_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
