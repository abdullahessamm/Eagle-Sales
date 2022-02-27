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
        Schema::create('permissions', function (Blueprint $table) {
            $table->smallInteger('backoffice_user_id')->unsigned();
            $table->foreign('backoffice_user_id')->references('id')->on('back_office_users');
            $table->tinyInteger('suppliers_access_level')->unsigned();
            $table->tinyInteger('customers_access_level')->unsigned();
            $table->tinyInteger('sellers_access_level')->unsigned();
            $table->tinyInteger('categorys_access_level')->unsigned();
            $table->tinyInteger('items_access_level')->unsigned();
            $table->tinyInteger('backoffice_emps_access_level')->unsigned();
            $table->tinyInteger('orders_access_level')->unsigned();
            $table->tinyInteger('commissions_access_level')->unsigned();
            $table->tinyInteger('journey_plan_access_level')->unsigned();
            $table->tinyInteger('pricelists_access_level')->unsigned();
            $table->boolean('app_config_access');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permissions');
    }
};
