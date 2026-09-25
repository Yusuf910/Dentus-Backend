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
        Schema::create('master_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Name of the Category');
            $table->string('image')->nullable()->comment('Image'); // Assuming image can be nullable
            $table->tinyInteger('status')->default(1)->comment('1=active, 0=inactive, 2=delete in archive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_categories');
    }
};
