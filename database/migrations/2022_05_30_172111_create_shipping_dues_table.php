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
        Schema::create('shipping_dues', function (Blueprint $table) {
            $table->id();
            $table->mediumInteger('supplier_id')->unsigned()->nullable();
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
            $table->bigInteger('order_id')->unsigned()->nullable();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
            $table->float('amount', 12, 2)->unsigned();
            $table->float('tax', 3, 1)->unsigned()->default(0);
            $table->float('discount', 3, 1)->unsigned()->default(0);
            $table->float('total_amount', 12, 2)->unsigned();
            $table->boolean('obtained')->default(false);
            $table->timestamp('obtained_at')->nullable();
            $table->bigInteger('obtained_by')->unsigned()->nullable();
            $table->foreign('obtained_by')->references('id')->on('users')->onDelete('set null');
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
        Schema::dropIfExists('shipping_dues');
    }
};
