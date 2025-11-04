<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderItemsTable extends Migration
{
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->uuid('order_item_id')->primary();
            $table->uuid('order_id');
            $table->foreign('order_id')->references('order_id')->on('orders')->onDelete('cascade');

            $table->uuid('artwork_id')->nullable();
            $table->foreign('artwork_id')->references('artwork_id')->on('artworks')->nullOnDelete();

            $table->string('file_result_url')->nullable();
            $table->text('note')->nullable();
            $table->text('revision_request')->nullable();
            $table->dateTimeTz('final_delivery_date')->nullable();

            // Tambahkan kolom price
            $table->decimal('price', 12, 2)->default(0);

            $table->timestampsTz();
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_items');
    }
}
