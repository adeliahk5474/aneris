<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartsTable extends Migration
{
    public function up()
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->uuid('cart_id')->primary();
            $table->uuid('client_id');
            $table->foreign('client_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->timestampsTz();
        });
    }

    public function down()
    {
        Schema::dropIfExists('carts');
    }
}
