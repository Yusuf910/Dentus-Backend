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
        Schema::create('wallet_plans', function (Blueprint $table) {
            $table->id();
            
            $table->decimal("recharge_amount", 15, 2)->default(0)->comment('balance added to user');
            $table->integer('position')->comment('positions for listing');
            $table->tinyInteger('is_for_new_user')->comment('1=yes,0=no');
            $table->tinyInteger('status')->comment('1=active,0=inactive,2=delete in archive');
            $table->unsignedBigInteger('coupan_id')->comment('coupan id from coupans table');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_plans');
    }
};
