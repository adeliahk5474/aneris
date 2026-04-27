<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('orders')) {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('order_id'); // relasi ke order
            $table->string('method')->nullable();   // bank/ewallet/manual
            $table->string('status')->default('pending'); // pending, paid, failed, refunded
            $table->string('payment_proof')->nullable();  // bukti transfer (opsional)
            $table->string('transaction_id')->nullable(); // id transaksi dari gateway
            $table->json('raw_response')->nullable();     // response API payment

            $table->timestamps();

            // relasi
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }}

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
