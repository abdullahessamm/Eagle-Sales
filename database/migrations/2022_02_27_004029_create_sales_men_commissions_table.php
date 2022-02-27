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
        Schema::create('sales_men_commissions', function (Blueprint $table) {
            $table->id();
            $table->integer('seller_id')->unsigned()->nullable();
            $table->foreign('seller_id')->references('id')->on('sellers')->onDelete('set null');
            $table->bigInteger('order_id')->unsigned();
            $table->foreign('order_id')->references('id')->on('orders');
            $table->float('commission')->unsigned();
            $table->float('tax')->unsigned()->default(0);
            $table->float('total_commission')->unsigned();
            $table->boolean('was_withdrawn')->default(false);
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
        Schema::dropIfExists('sales_men_commissions');
    }
};
