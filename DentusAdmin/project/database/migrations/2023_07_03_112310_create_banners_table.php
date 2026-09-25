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
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('image')->comment('banner image name stores')->default(0);
            $table->tinyInteger('maintype')->default(0)->comment('1=top,2=middle');
            $table->tinyInteger('type')->default(0)->comment('1=website,2=app');
            $table->tinyInteger('linktype')->default(0)->comment('1=astrologer,2=eshop product,3=blogs,4=astro video,5=tutorial video');
            $table->unsignedBigInteger('link_id')->default(0);
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
        Schema::dropIfExists('banners');
    }
};
