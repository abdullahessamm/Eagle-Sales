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
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('shop_name');
            $table->float('credit_limit', 12, 2)->nullable();
            $table->tinyInteger('category_id')->unsigned();
            $table->foreign('category_id')->references('id')->on('customer_categories');
            $table->string('vat_uri', 191)->nullable();
            $table->float('shop_space', 6, 2)->nullable();
            $table->bigInteger('linked_seller')->unsigned()->nullable();
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
