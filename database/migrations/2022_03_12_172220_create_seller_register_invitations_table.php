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
        Schema::create('seller_register_invitations', function (Blueprint $table) {
            $table->integer('id', true, true);
            $table->integer('created_seller_id')->unsigned();
            $table->foreign('created_seller_id')->references('id')->on('sellers');
            $table->integer('invitation_owner_id')->unsigned();
            $table->foreign('invitation_owner_id')->references('id')->on('sellers');
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
        Schema::dropIfExists('seller_register_rewards');
    }
};
