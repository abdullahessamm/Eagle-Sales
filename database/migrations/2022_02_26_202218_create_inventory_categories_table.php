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
        Schema::create('inventory_categories', function (Blueprint $table) {
            $table->smallInteger('id', true, true);
            $table->string('uri_prefix', 50)->nullable();
            $table->string('category_name', 20)->unique();
            $table->string('category_name_ar', 20)->unique();
            $table->string('description')->nullable();
            $table->string('description_ar')->nullable();
            $table->smallInteger('parent_category_id')->unsigned()->nullable();
            $table->foreign('parent_category_id')->references('id')->on('inventory_categories')->onDelete('cascade');
            $table->bigInteger('created_by')->unsigned();
            $table->foreign('created_by')->references('id')->on('users');
            $table->bigInteger('updated_by')->unsigned()->nullable();
            $table->foreign('updated_by')->references('id')->on('users');
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
        Schema::dropIfExists('inventory_categories');
    }
};
