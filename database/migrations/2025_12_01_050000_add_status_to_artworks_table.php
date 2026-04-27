<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('artworks')) {
            Schema::table('artworks', function (Blueprint $table) {
                if (!Schema::hasColumn('artworks', 'status')) {
                    $table->string('status')->default('draft')->after('image_url');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('artworks')) {
            Schema::table('artworks', function (Blueprint $table) {
                if (Schema::hasColumn('artworks', 'status')) {
                    $table->dropColumn('status');
                }
            });
        }
    }
};
