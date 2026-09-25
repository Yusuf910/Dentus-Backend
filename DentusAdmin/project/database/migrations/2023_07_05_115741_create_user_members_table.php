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
        Schema::create('user_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('name')->comment('name for member');
            $table->string('email')->comment('email for member');
            $table->string('mobile')->comment('mobile for member');
            $table->string('alternate_mobile')->comment('alternate_mobile for member');
            $table->string('gender')->comment('gender for member');
            $table->string('dob')->comment('date of birth for member');
            $table->string('tob')->comment('time of birth for member');
            $table->string('pob')->comment('place of birth for member');
            $table->string('relation')->comment('relation with user');
            $table->tinyInteger('status')->comment('1=active,0=inactive,2=delete in archive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_members');
    }
};
