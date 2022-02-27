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
        Schema::create('var_items_attr_vals', function (Blueprint $table) {
            $table->id();
            $table->string('attr_val', 50);
            $table->string('ar_attr_val', 50);
            $table->bigInteger('attr_id')->unsigned();
            $table->foreign('attr_id')->references('id')->on('var_items_attrs')->onDelete('cascade');
            $table->bigInteger('for')->unsigned()->nullable();
            $table->foreign('for')->references('id')->on('var_items_attr_vals')->onDelete('cascade');
            $table->boolean('is_available')->nullable();
            $table->smallInteger('available_count')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('var_items_attr_vals');
    }
};
