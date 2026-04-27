<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->uuid('review_id')->primary();
            $table->uuid('order_id')->nullable();
            $table->foreign('order_id')->references('order_id')->on('orders')->nullOnDelete();
            $table->uuid('reviewer_id');
            $table->foreign('reviewer_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->tinyInteger('rating')->unsigned()->nullable();
            $table->text('comment')->nullable();
            $table->timestampsTz();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reviews');
    }
}
