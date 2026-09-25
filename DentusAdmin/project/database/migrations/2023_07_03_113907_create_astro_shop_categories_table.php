<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('astro_shop_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name_en')->comment('name in english');
            $table->string('name_hi')->comment('name in Hindi');
            $table->string('name_ta')->comment('name in Tamil');
            $table->string('name_ml')->comment('name in Malayalam');
            $table->string('name_te')->comment('name in Telugu');
            $table->string('name_kn')->comment('name in Kannada');
            $table->string('slug')->comment('slug name')->default(0);
            $table->string('image')->comment('image name stores')->default(0);
            $table->tinyInteger('in_home')->default(0)->comment('1=yes,2=no');
            $table->integer('position')->comment('positions for listing');
            $table->tinyInteger('status')->comment('1=active,0=inactive,2=delete in archive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('astro_shop_categories');
    }
};
