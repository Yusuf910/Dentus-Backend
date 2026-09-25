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
        Schema::create('astrologer_percentage_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->integer('share_percentage')->comment('share_percentage per booking')->default(0);
            $table->integer('share_fixed')->comment('share_fixed per booking')->default(0);
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
        Schema::dropIfExists('astrologer_percentage_requests');
    }
};
