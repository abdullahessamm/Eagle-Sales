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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->mediumInteger('supplier_id')->unsigned()->nullable();
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
            $table->integer('customer_id')->unsigned()->nullable();
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
            $table->enum('state', [0, 1, 2, 3, 4, 5, 6])->default(0);
            $table->boolean('require_shipping')->default(false);
            $table->bigInteger('shipping_address_id')->unsigned()->nullable();
            $table->foreign('shipping_address_id')->references('id')->on('users_places')->onDelete('set null');
            $table->bigInteger('billing_address_id')->unsigned()->nullable();
            $table->foreign('billing_address_id')->references('id')->on('users_places')->onDelete('set null');
            $table->float('required', 12, 2)->unsigned();
            $table->float('tax', 3, 1)->unsigned()->default(0);
            $table->float('discount', 3, 1)->unsigned()->default(0);
            $table->float('total_required', 12, 2)->unsigned();
            $table->char('currency', 3);
            $table->boolean('is_credit')->default(false);
            $table->timestamp('credit_limit')->nullable();
            $table->float('deposit', 12, 2)->nullable();
            $table->float('remaining', 12, 2)->nullable();
            $table->Integer('created_by')->unsigned()->nullable();
            $table->foreign('created_by')->references('id')->on('sellers')->onDelete('set null');
            $table->bigInteger('updated_by')->unsigned()->nullable();
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->date('delivery_date')->nullable();
            $table->timestamp('delivered_at')->nullable();
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
        Schema::dropIfExists('orders');
    }
};
