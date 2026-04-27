<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartItemsTable extends Migration
{
    public function up()
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->uuid('cart_item_id')->primary();
            $table->uuid('cart_id');
            $table->foreign('cart_id')->references('cart_id')->on('carts')->onDelete('cascade');
            $table->uuid('artwork_id');
            $table->foreign('artwork_id')->references('artwork_id')->on('artworks')->onDelete('restrict');
            $table->integer('quantity')->default(1);
            $table->timestampsTz();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cart_items');
    }
}
