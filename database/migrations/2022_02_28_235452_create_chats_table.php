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
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('admin_id')->unsigned();
            $table->foreign('admin_id')->references('id')->on('back_office_users');
            $table->bigInteger('client_id')->unsigned()->nullable();
            $table->foreign('client_id')->references('id')->on('users')->onDelete('set null');
            $table->string('full_name', 50)->nullable();
            $table->string('email', 70)->unique()->nullable();
            $table->string('phone', 15)->unique()->nullable();
            $table->boolean('is_our_client')->default(false);
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
        Schema::dropIfExists('chats');
    }
};
