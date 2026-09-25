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
        Schema::create('astrologer_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('astrologer_id');
            $table->integer('normal_chat_price')->comment('price per mintues')->default(0);
            $table->integer('priority_chat_price')->comment('price per mintues')->default(0);
            $table->integer('scheduled_chat_price')->comment('price')->default(0);
            $table->integer('chat_price_commision_type')->comment('type=>flat,percentage')->default(0);
            $table->integer('chat_price_commision_value')->comment('value')->default(0);
            $table->integer('normal_call_price')->comment('price per mintues')->default(0);
            $table->integer('priority_call_price')->comment('price per mintues')->default(0);
            $table->integer('scheduled_call_price')->comment('price')->default(0);
            $table->integer('call_price_commision_type')->comment('type=>flat,percentage')->default(0);
            $table->integer('call_price_commision_value')->comment('value')->default(0);
            $table->integer('normal_video_price')->comment('price per mintues')->default(0);
            $table->integer('priority_video_price')->comment('price per mintues')->default(0);
            $table->integer('scheduled_video_price')->comment('price')->default(0);
            $table->integer('video_price_commision_type')->comment('type=>flat,percentage')->default(0);
            $table->integer('video_price_commision_value')->comment('value')->default(0);
            $table->integer('astrochart_price')->comment('price per mintues')->default(0);
            $table->integer('astrochart_commision_type')->comment('type=>flat,percentage')->default(0);
            $table->integer('astrochart_commision_value')->comment('value')->default(0);
            $table->integer('ask_a_question_price')->comment('price per mintues')->default(0);
            $table->integer('ask_a_question_commision_type')->comment('type=>flat,percentage')->default(0);
            $table->integer('ask_a_question_commision_value')->comment('value')->default(0);
            $table->foreign('astrologer_id')->references('id')->on('astrologers');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('astrologer_prices');
    }
};
