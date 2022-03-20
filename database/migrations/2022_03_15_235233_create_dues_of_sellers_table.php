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
        Schema::create('dues_of_sellers', function (Blueprint $table) {
            $table->id();
            $table->integer('seller_id')->unsigned();
            $table->foreign('seller_id')->references('id')->on('sellers');
            $table->float('cash', 8, 2)->unsigned();
            $table->boolean('is_reward')->default(false);
            $table->integer('register_invitation_id')->unsigned();
            $table->foreign('register_invitation_id')->references('id')->on('seller_register_invitations');
            $table->string('reward_type', 80)->nullable();
            $table->boolean('is_salary')->default(false);
            $table->boolean('is_commission')->default(true);
            $table->float('tax', 4, 2)->unsigned();
            $table->bigInteger('order_id')->unsigned()->nullable();
            $table->foreign('order_id')->references('id')->on('orders');
            $table->boolean('was_withdrawn')->default(false);
            $table->string('notes',255)->nullable();
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
        Schema::dropIfExists('dues_of_sellers');
    }
};
