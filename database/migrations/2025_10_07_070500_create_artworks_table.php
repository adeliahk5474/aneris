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
            $table->uuid('artist_id');
            $table->foreign('artist_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->uuid('category_id')->nullable();
            $table->foreign('category_id')->references('category_id')->on('categories')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_url')->nullable();
            $table->string('preview_url')->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->enum('status', ['public','private','draft'])->default('public');
            $table->timestampsTz();
        });
    }

    public function down()
    {
        Schema::dropIfExists('artworks');
    }
}
