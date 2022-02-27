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
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->string('ar_description');
            $table->date('start_date');
            $table->date('expire_date');
            $table->bigInteger('item_id')->unsigned();
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->boolean('is_discount')->default(true);
            $table->boolean('has_promocodes')->default(false);
            $table->tinyInteger('conditional_quantity');
            $table->tinyInteger('number_of free_elements')->nullable();
            $table->smallInteger('amount_of_discount')->nullable();
            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('promotions');
    }
};
