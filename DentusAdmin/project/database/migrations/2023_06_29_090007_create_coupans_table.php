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
        Schema::create('coupans', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('title');
            $table->string('code')->comment('coupan code');
            $table->integer("coupon_value")->default(0);
            $table->tinyInteger('discount_type')->comment('1=percentage,2=flat');
            $table->decimal("cashback_amount", 15, 2)->default(0);
            $table->integer("total_coupan")->default(0)->comment('coupan quntity');
            $table->string('start_date')->comment('date for start');
            $table->string('expiry_date')->comment('date for expiry');
            $table->string('coupan_for')->comment('walle and others');
            $table->tinyInteger('status')->comment('1=active,0=inactive,2=delete in archive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupans');
    }
};
