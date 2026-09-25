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
        Schema::create('booking_remedies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('astrologer_id');
            $table->string('purpose')->comment('purpose')->nullable();
            $table->string('mantras_ids')->comment('mantra from mantras table')->nullable();
            $table->string('thread_colors_ids')->comment('thread_colors')->nullable();
            $table->string('donations_ids')->comment('donations')->nullable();
            $table->string('rudrakshi_ids')->comment('rudrakshi')->nullable();
            $table->string('precious_stone_ids')->comment('precious_stone')->nullable();
            $table->string('semi_precious_stone_ids')->comment('semi_precious_stone')->nullable();
            $table->string('message')->comment('message')->nullable();
            $table->tinyInteger('status')->default(0)->comment('1=active,2=inactive,3=delete');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_remedies');
    }
};
