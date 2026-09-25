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
        Schema::create('astrologer_service_flags', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('astrologer_id');
            $table->tinyInteger('chat_flag')->comment('0=no,1=yes,2=not allow')->default(0);
            $table->tinyInteger('priority_chat')->comment('1=yes,0=no,2=not allow')->default(0);
            $table->tinyInteger('free_chat')->comment('1=yes,0=no,2=not allow')->default(0);
            $table->tinyInteger('call_flag')->comment('1=yes,0=no,2=not allow')->default(0);
            $table->tinyInteger('priority_call')->comment('1=yes,0=no,2=not allow')->default(0);
            $table->tinyInteger('free_call')->comment('1=yes,0=no,2=not allow')->default(0);
            $table->tinyInteger('video_call_flag')->comment('1=yes,0=no,2=not allow')->default(0);
            $table->tinyInteger('priority_call_flag')->comment('1=yes,0=no,2=not allow')->default(0);
            $table->tinyInteger('free_video')->comment('1=yes,0=no,2=not allow')->default(0);
            $table->tinyInteger('astrochart_flag')->comment('1=yes,0=no,2=not allow')->default(0);
            $table->tinyInteger('ask_a_question')->comment('1=yes,0=no,2=not allow')->default(0);
            $table->tinyInteger('can_take_schedule')->comment('1=yes,0=no,2=not allow')->default(0);
            $table->tinyInteger('can_take_live_broadcast')->comment('1=yes,0=no,2=not allow')->default(0);
            $table->foreign('astrologer_id')->references('id')->on('astrologers');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('astrologer_service_flags');
    }
};
