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
        Schema::create('customers', function (Blueprint $table) {
            $table->integer('id', true, true);
            $table->bigInteger('user_id')->unsigned()->unique();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('shop_name');
            $table->string('l1_address')->unique();
            $table->string('l1_address_ar')->unique();
            $table->string('l2_address')->unique()->nullable();
            $table->string('l2_address_ar')->unique()->nullable();
            $table->string('location_coords', 50)->nullable();
            $table->string('vat_no', 20)->unique();
            $table->float('credit_limit', 12, 2)->nullable();
            $table->tinyInteger('category_id')->unsigned();
            $table->foreign('category_id')->references('id')->on('customer_categories');
            $table->float('shop_space', 6, 2)->nullable();
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
        Schema::dropIfExists('customers');
    }
};
