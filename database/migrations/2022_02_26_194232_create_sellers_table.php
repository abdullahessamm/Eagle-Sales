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
        Schema::create('sellers', function (Blueprint $table) {
            $table->integer('id', true, true);
            $table->bigInteger('user_id')->unsigned()->unique();
            $table->foreign('user_id')->references('id')->on('users');
            $table->tinyInteger('age');
            $table->string('education');
            $table->string('l1_address')->unique();
            $table->string('l1_address_ar')->unique();
            $table->string('l2_address')->unique()->nullable();
            $table->string('l2_address_ar')->unique()->nullable();
            $table->string('location_coords', 50)->nullable();
            $table->float('salary', 7, 2, true)->nullable();
            $table->float('tax', 6, 2, true)->nullable();
            $table->string('bank_account_number', 50)->nullable();
            $table->string('bank_name', 50)->nullable();
            $table->boolean('is_freelancer')->default(true);
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
        Schema::dropIfExists('sellers');
    }
};
