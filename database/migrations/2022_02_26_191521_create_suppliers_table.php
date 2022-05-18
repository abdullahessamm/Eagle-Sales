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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->mediumInteger('id', true, true);
            $table->bigInteger('user_id')->unique()->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('vat_no', 20)->unique()->nullable();
            $table->string('shop_name', 50);
            $table->string('whatsapp_no', 15)->unique()->nullable();
            $table->string('fb_page')->unique()->nullable();
            $table->string('website_domain')->unique()->nullable();
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
        Schema::dropIfExists('suppliers');
    }
};
