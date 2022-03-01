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
        Schema::create('order_notifications_listeners', function (Blueprint $table) {
            $table->bigInteger('notification_id')->unsigned();
            $table->foreign('notification_id')->references('id')->on('order_notifications')->onDelete('cascade');
            $table->bigInteger('listener_id')->unsigned();
            $table->foreign('listener_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('seen_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_notifications_listeners');
    }
};
