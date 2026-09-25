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
        Schema::create('astrologer_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('booking_id')->default(0);
            $table->string('payment_mode',1000);
            $table->string('booking_txn_id',1000);
            $table->string('txn_name',1000);
            $table->string('txn_for',1000);
            $table->string('type',1000);
            $table->string('txn_mode',1000);
            $table->string('currency',1000);
            $table->string('price',1000);
            $table->string('tax_price',1000);
            $table->string('tds_price',1000);
            $table->string('amount',1000);
            $table->tinyInteger('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('astrologer_transactions');
    }
};
