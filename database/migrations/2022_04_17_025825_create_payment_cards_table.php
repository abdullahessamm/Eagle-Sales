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
        Schema::connection('sqlite')->create('payment_cards', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('card_number', 20);
            $table->string('cvv', 3);
            $table->enum('card_type', ['visa', 'mastercard']);
            $table->string('card_holder_name', 50);
            $table->timestamp('expiry_date');
            $table->boolean('is_default')->default(false);
            $table->string('l1_address', 30);
            $table->string('l2_address', 30);
            $table->string('city', 26);
            $table->string('country', 20);
            $table->string('postal_code', 10);
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
        Schema::connection('sqlite')->dropIfExists('payment_cards');
    }
};
