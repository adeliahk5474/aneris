<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed default values
        $defaults = [
            ['key' => 'hero_title',              'value' => 'For the love of human creativity'],
            ['key' => 'hero_subtitle',           'value' => ''],
            ['key' => 'hero_image',              'value' => ''],
            ['key' => 'hero_image_public_id',    'value' => ''],

            ['key' => 'banner1_title',           'value' => 'Made for creators'],
            ['key' => 'banner1_subtitle',        'value' => 'Illustrations, avatars, emotes, live2d — made by humans who love what they do.'],
            ['key' => 'banner1_color',           'value' => '#1a1a2e'],
            ['key' => 'banner1_image',           'value' => ''],
            ['key' => 'banner1_image_public_id', 'value' => ''],

            ['key' => 'banner2_title',           'value' => 'No Generative AI'],
            ['key' => 'banner2_subtitle',        'value' => 'Until generative AI is made with Consent, Credit, and Compensation, it is not welcome here.'],
            ['key' => 'banner2_color',           'value' => '#0d2818'],
            ['key' => 'banner2_image',           'value' => ''],
            ['key' => 'banner2_image_public_id', 'value' => ''],
        ];

        foreach ($defaults as $row) {
            DB::table('home_settings')->insert(array_merge($row, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('home_settings');
    }
};
