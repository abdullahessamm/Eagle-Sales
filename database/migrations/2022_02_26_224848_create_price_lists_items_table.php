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
        Schema::create('price_lists_items', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('list_id')->unsigned();
            $table->foreign('list_id')->references('id')->on('price_lists');
            $table->bigInteger('item_id')->unsigned()->unique();
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->smallInteger('created_by')->unsigned()->nullable();
            $table->foreign('created_by')->references('id')->on('back_office_users')->onDelete('set null');
            $table->timestamp('created_at')->default(now());
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('price_lists_items');
    }
};
