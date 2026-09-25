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
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('description_en',500)->comment('description_in english');
            $table->string('description_hi',500)->comment('description_in Hindi');
            $table->string('description_ta',500)->comment('description_in Tamil');
            $table->string('description_ml',500)->comment('description_in Malayalam');
            $table->string('description_te',500)->comment('description_in Telugu');
            $table->string('description_kn',500)->comment('description_in Kannada');
            $table->integer('position')->comment('positions for listing');
            $table->tinyInteger('in_home')->comment('seen in home page for app and website. 1=yes,0=no');
            $table->tinyInteger('status')->comment('1=active,0=inactive,2=delete in archive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
