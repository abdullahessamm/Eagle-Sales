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
            $table->string('name', 50);
            $table->string('ar_name', 50);
            $table->smallInteger('category_id')->unsigned();
            $table->foreign('category_id')->references('id')->on('inventory_categories')->onDelete('cascade');
            $table->string('brand', 50);
            $table->string('ar_brand', 50);
            $table->string('barcode', 64)->nullable();
            $table->string('keywords');
            $table->mediumText('description');
            $table->mediumText('ar_description');
            $table->boolean('has_promotions')->default(false);
            $table->boolean('is_var')->default(false);
            $table->json('vars')->nullable();
            $table->boolean('is_available')->default(true);
            $table->smallInteger('total_available_count')->unsigned();
            $table->boolean('is_approved')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('video_uri', 100)->nullable();
            $table->mediumInteger('supplier_id')->unsigned();
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->float('price', 8, 2)->unsigned();
            $table->char('currency', 3);
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
