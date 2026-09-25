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
        Schema::create('astrologer_leave_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('start_time',255)->default('');
            $table->string('end_time',255);
            $table->string('reason',500);
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
        Schema::dropIfExists('astrologer_leave_requests');
    }
};
