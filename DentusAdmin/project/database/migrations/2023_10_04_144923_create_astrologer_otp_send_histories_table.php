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
        Schema::create('astrologer_otp_send_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('otp')->comment('otp')->nullable();
            $table->string('device_id')->comment('device_id')->nullable();
            $table->string('device_type')->comment('device_type')->nullable();
            $table->string('device_token')->comment('device_token')->nullable();
            $table->string('country_code')->comment('country_code')->nullable();
            $table->string('mobile')->comment('mobile')->nullable();
            $table->integer('resendcount')->comment('resendcount')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('astrologer_otp_send_histories');
    }
};
