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
        Schema::create('available_countries', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('name', 50);
            $table->string('code', 10);
            $table->string('flag')->nullable();
            $table->string('currency', 5);
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
        Schema::dropIfExists('available_countries');
    }
};
