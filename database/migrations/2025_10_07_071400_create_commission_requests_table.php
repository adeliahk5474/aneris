<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('commission_requests', function (Blueprint $table) {
            $table->uuid('commission_request_id')->primary();

            $table->uuid('client_id')->nullable();
            $table->foreign('client_id')
                  ->references('user_id')
                  ->on('users')
                  ->nullOnDelete();

            $table->uuid('artist_id')->nullable();
            $table->foreign('artist_id')
                  ->references('user_id')
                  ->on('users')
                  ->nullOnDelete();

            $table->uuid('category_id')->nullable();
            $table->foreign('category_id')
                  ->references('category_id')
                  ->on('categories')
                  ->nullOnDelete();

            $table->text('description')->nullable();
            $table->decimal('proposed_price', 12, 2)->default(0);
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_requests');
    }
};
