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
        Schema::create('price_lists', function (Blueprint $table) {
            $table->smallInteger('id', true, true);
            $table->string('list_name', 20)->unique();
            $table->string('ar_list_name', 20)->unique();
            $table->string('description');
            $table->string('ar_description');
            $table->string('kind_of_products');
            $table->smallInteger('created_by')->unsigned()->nullable();
            $table->foreign('created_by')->references('id')->on('back_office_users')->onDelete('set null');
            $table->smallInteger('updated_by')->unsigned()->nullable();
            $table->foreign('updated_by')->references('id')->on('back_office_users')->onDelete('set null');
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
        Schema::dropIfExists('price_lists');
    }
};
