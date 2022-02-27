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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('item_id')->unsigned()->nullable();
            $table->foreign('item_id')->references('id')->on('items')->onDelete('set null');
            $table->bigInteger('order_id')->unsigned();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->bigInteger('promotion_id')->unsigned()->nullable();
            $table->foreign('promotion_id')->references('id')->on('promotions');
            $table->bigInteger('uom_id')->unsigned();
            $table->foreign('uom_id')->references('id')->on('uoms');
            $table->smallInteger('quantity')->unsigned();
            $table->float('total_before_discount', 12, 2)->unsigned();
            $table->float('total', 12, 2)->unsigned();
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
        Schema::dropIfExists('order_items');
    }
};
