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
            $table->smallInteger('backoffice_user_id')->unsigned()->unique();
            $table->foreign('backoffice_user_id')->references('id')->on('back_office_users');
            $table->char('suppliers_access_level', 4)->default('0000');
            $table->char('customers_access_level', 4)->default('0000');
            $table->char('sellers_access_level', 4)->default('0000');
            $table->char('categorys_access_level', 4)->default('0000');
            $table->char('items_access_level', 4)->default('0000');
            $table->char('backoffice_emps_access_level', 4)->default('0000');
            $table->char('orders_access_level', 4)->default('0000');
            $table->char('commissions_access_level', 4)->default('0000');
            $table->char('journey_plan_access_level', 4)->default('0000');
            $table->char('pricelists_access_level', 4)->default('0000');
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
