<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('order_id')->primary();
            $table->uuid('client_id')->nullable();
            $table->foreign('client_id')->references('user_id')->on('users')->nullOnDelete();
            $table->uuid('artist_id')->nullable();
            $table->foreign('artist_id')->references('user_id')->on('users')->nullOnDelete();
            $table->uuid('category_id')->nullable();
            $table->foreign('category_id')->references('category_id')->on('categories')->nullOnDelete();
            $table->decimal('total_price', 12, 2)->default(0);
            $table->enum('status', ['pending','in_progress','revision','completed','canceled'])->default('pending');
            $table->timestampsTz();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
