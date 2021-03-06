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
        Schema::create('uom_conversion_rules', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('uom_id')->unsigned()->unique();
            $table->foreign('uom_id')->references('id')->on('uoms')->onDelete('cascade');
            $table->float('factor', 9, 2);
            $table->boolean('operation_is_multiply');
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
        Schema::dropIfExists('uom_conversion_rules');
    }
};
