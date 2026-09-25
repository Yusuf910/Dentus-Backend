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
        Schema::create('astrologer_galleries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('astrologer_id');
            $table->string('type')->comment('type=image or video');
            $table->string('name')->comment('name for file image and video');
            $table->integer('position')->comment('positions for listing');
            $table->tinyInteger('status')->comment('1=active,0=inactive,2=delete in archive');
            $table->foreign('astrologer_id')->references('id')->on('astrologers');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('astrologer_galleries');
    }
};
