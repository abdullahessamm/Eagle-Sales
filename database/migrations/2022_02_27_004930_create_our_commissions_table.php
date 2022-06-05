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
        Schema::create('our_commissions', function (Blueprint $table) {
            $table->id();
            $table->mediumInteger('supplier_id')->unsigned();
            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->bigInteger('order_id')->unsigned();
            $table->foreign('order_id')->references('id')->on('orders');
            $table->float('amount', 12, 2)->unsigned();
            $table->boolean('obtained')->default(false);
            $table->timestamp('obtained_at')->nullable();
            $table->bigInteger('obtained_by')->unsigned()->nullable();
            $table->foreign('obtained_by')->references('id')->on('users');
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
        Schema::dropIfExists('our_commissions');
    }
};
