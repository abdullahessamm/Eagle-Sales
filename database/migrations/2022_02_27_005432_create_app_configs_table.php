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
        Schema::create('app_configs', function (Blueprint $table) {
            $table->smallInteger('id', true, true);
            $table->string('key', 50)->unique();
            $table->string('value');
            $table->smallInteger('updated_by')->unsigned()->nullable();
            $table->foreign('updated_by')->references('id')->on('back_office_users')->onDelete('set null');
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('app_configs');
    }
};
