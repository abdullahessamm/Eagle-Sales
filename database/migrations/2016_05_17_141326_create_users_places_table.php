<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_places', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('coords', 50);
            $table->string('country', 50);
            $table->string('country_ar', 50);
            $table->char('country_code', 2);
            $table->string('governorate', 50);
            $table->string('governorate_ar', 50);
            $table->string('zone', 50)->nullable();
            $table->string('zone_ar', 50)->nullable();
            $table->string('city', 50)->nullable();
            $table->string('city_ar', 50)->nullable();
            $table->string('street', 50)->nullable();
            $table->string('street_ar', 50)->nullable();
            $table->string('building_no', 50)->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_places');
    }
};
