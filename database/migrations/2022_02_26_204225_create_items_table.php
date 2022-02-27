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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('ar_name');
            $table->tinyInteger('category_id')->unsigned();
            $table->foreign('category_id')->references('id')->on('inventory_categories')->onDelete('cascade');
            $table->string('barcode', 64)->unique()->nullable();
            $table->string('keywards');
            $table->string('description', 255);
            $table->string('ar_description', 255);
            $table->boolean('has_promotions')->default(false);
            $table->boolean('is_var')->default(false);
            $table->boolean('is_available')->default(true);
            $table->smallInteger('total_available_count')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->mediumInteger('supplier_id')->unsigned();
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
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
        Schema::dropIfExists('items');
    }
};
