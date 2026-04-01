<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArtworksTable extends Migration
{
    public function up()
    {
        Schema::create('artworks', function (Blueprint $table) {
            $table->uuid('artwork_id')->primary();
            $table->uuid('user_id');
            $table->foreign('user_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('cascade');
            $table->uuid('category_id')->nullable();
            $table->foreign('category_id')->references('category_id')->on('categories')->nullOnDelete();
            $table->string('image_url')->nullable();
            $table->text('caption')->nullable();
            $table->timestampsTz();
        });
    }

    public function down()
    {
        Schema::dropIfExists('artworks');
    }
}
