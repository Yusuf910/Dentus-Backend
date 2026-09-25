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
        Schema::create('wallet_add_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('booking_id')->default(0);
            $table->unsignedBigInteger('temp_id')->default(0);
            $table->string('currency',255)->default('INR');
            $table->string('amount',1000);
            $table->tinyInteger('status')->default(0)->comment('0=pending,1=completed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_add_requests');
    }
};
