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
        Schema::create('social_connect_views', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('blog_id')->nullable(); 
            $table->unsignedBigInteger('user_id')->nullable(); 
            $table->tinyInteger('status')->comment('1=active,0=inactive,2=delete in archive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_connect_views');
    }
};
