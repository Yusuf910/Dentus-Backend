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
        Schema::create('astrologers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('name of astrologer');
            $table->string('slug')->comment('slug value for seo and for uri');
            $table->integer('screen_name')->comment('screen_name for display to client side');
            $table->string('email')->unique()->comment('unique email');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('country_code')->nullable();
            $table->string('mobile')->unique()->comment('unique mobile for login');
            $table->string('alternate_mobile')->nullable();
            $table->string('password')->nullable();
            $table->string('gender')->comment('male or female');
            $table->longText('about')->nullable()->comment('about us for astrologer');
            $table->string('image')->default('default.png')->comment('display image for astrologer!');
            $table->string('experience')->default(0)->comment('experience');
            $table->longText('language_spoken_id')->nullable()->comment('language come from master_langauages for display in astrologer profile');
            $table->longText('areaofexpertise_id')->nullable()->comment('area of expertise come from area_of_expertises for display in astrologer profile');
            $table->rememberToken();
            $table->longText('tags')->nullable()->comment('tags for searching astrologer');
            $table->tinyInteger('in_homepage')->comment('1=yes,0=no')->default(0);
            $table->tinyInteger('in_house_astrologer')->comment('1=yes,0=no')->default(0);
            $table->tinyInteger('status')->comment('1=active and verified,0=inactive,2=delete in archive,3=unverified');
            $table->integer('position')->comment('positions for listing');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('astrologers');
    }
};
