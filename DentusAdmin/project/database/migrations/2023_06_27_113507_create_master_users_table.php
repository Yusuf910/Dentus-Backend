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
        Schema::create('master_users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('name in english');
            $table->string('name_hi')->comment('name in Hindi');
            $table->string('name_tm')->comment('name in Tamil');
            $table->string('name_ml')->comment('name in Malayalam');
            $table->string('name_tl')->comment('name in Telugu');
            $table->string('name_ka')->comment('name in Kannada');
            $table->string('image')->default('default.png')->comment('image');
            $table->integer('position')->comment('positions for listing');
            $table->tinyInteger('status')->comment('1=active,0=inactive,2=delete in archive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_users');
    }
};
