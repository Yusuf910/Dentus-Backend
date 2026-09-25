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
        Schema::create('astrologer_price_change_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->integer('normal_chat_price')->comment('price per mintues')->default(0);
            $table->integer('normal_chat_price_usd')->comment('price per mintues')->default(0);
            $table->integer('priority_chat_price')->comment('price per mintues')->default(0);
            $table->integer('priority_chat_price_usd')->comment('price per mintues')->default(0);
            $table->integer('scheduled_chat_price')->comment('price')->default(0);
            $table->integer('scheduled_chat_price_usd')->comment('price')->default(0);
            $table->integer('normal_call_price')->comment('price per mintues')->default(0);
            $table->integer('normal_call_price_usd')->comment('price per mintues')->default(0);
            $table->integer('priority_call_price')->comment('price per mintues')->default(0);
            $table->integer('priority_call_price_usd')->comment('price per mintues')->default(0);
            $table->integer('scheduled_call_price')->comment('price')->default(0);
            $table->integer('scheduled_call_price_usd')->comment('price')->default(0);
            $table->integer('normal_video_price')->comment('price per mintues')->default(0);
            $table->integer('normal_video_price_usd')->comment('price per mintues')->default(0);
            $table->integer('priority_video_price')->comment('price per mintues')->default(0);
            $table->integer('priority_video_price_usd')->comment('price per mintues')->default(0);
            $table->integer('scheduled_video_price')->comment('price')->default(0);
            $table->integer('scheduled_video_price_usd')->comment('price')->default(0);
            $table->integer('astrochart_price')->comment('price per mintues')->default(0);
            $table->integer('ask_a_question_price')->comment('price per mintues')->default(0);
            $table->tinyInteger('status')->default(0)->comment('0=pending,1=approved,2=delete,3=reject');
            $table->unsignedBigInteger('approved_by')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('astrologer_price_change_requests');
    }
};
