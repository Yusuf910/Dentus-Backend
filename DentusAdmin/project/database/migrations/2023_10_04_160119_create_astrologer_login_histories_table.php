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
        Schema::create('astrologer_login_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('user_agent')->comment('device_id')->nullable();
            $table->string('ip_address')->comment('device_id')->nullable();
            $table->string('device_id')->comment('device_id')->nullable();
            $table->string('device_type')->comment('device_type')->nullable();
            $table->string('device_token')->comment('device_token')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('astrologer_login_histories');
    }
};
