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
            
            $table->uuid('artist_id');
            $table->foreign('artist_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');
            
            $table->uuid('category_id')->nullable();
            $table->foreign('category_id')
                  ->references('category_id')
                  ->on('categories')
                  ->nullOnDelete();

            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->string('reference_file')->nullable();
            $table->enum('status', ['active','inactive'])->default('active');
            
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_services');
    }
};
