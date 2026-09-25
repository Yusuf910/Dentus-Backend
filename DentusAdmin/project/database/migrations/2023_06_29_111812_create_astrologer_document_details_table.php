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
        Schema::create('astrologer_document_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('astrologer_id');
            $table->string('pancard')->comment('pancard number');
            $table->string('aadhar_number')->comment('aadhar_number');
            $table->string('aadharcard_image')->comment('aadharcard_image');
            $table->string('pancard_image')->comment('pancard_image');
            $table->longText('qualification')->comment('qualification');
            $table->string('bank_account_number')->comment('bank_account_number');
            $table->string('account_type')->comment('account_type');
            $table->string('ifsc_code')->comment('ifsc_code');
            $table->string('account_holder_name')->comment('account_holder_name');
            $table->foreign('astrologer_id')->references('id')->on('astrologers');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('astrologer_document_details');
    }
};
