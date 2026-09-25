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
        Schema::create('booking_feedback', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('astrologer_id');
            $table->string('user_consultation_language')->comment('user_consultation_language')->nullable();
            $table->string('main_profile')->comment('main_profile')->nullable();
            $table->string('rashi')->comment('rashi')->nullable();
            $table->string('nakshtra')->comment('nakshtra')->nullable();
            $table->string('feedback_on_user')->comment('feedback_on_user')->nullable();
            $table->string('applicable')->comment('applicable')->nullable();
            $table->tinyInteger('refund_amount')->default(0)->comment('refund_amount=0 intiate or pending,1=approved,2=reject');
            $table->string('enter_amount')->comment('enter_amount')->nullable();
            $table->string('reason')->comment('reason')->nullable();
            $table->tinyInteger('resolve')->default(0)->comment('resolve=0 intiate or pending,1=approved,2=reject');
            $table->tinyInteger('status')->default(0)->comment('1=active,2=inactive,3=delete');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_feedback');
    }
};
