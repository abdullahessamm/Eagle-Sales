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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('f_name', 50);
            $table->string('l_name', 50);
            $table->string('avatar_uri', 255)->nullable();
            $table->string('personal_id_uri', 255)->nullable();
            $table->string('email')->unique();
            $table->char('country', 2);
            $table->string('city', 10);
            $table->string('username', 80)->unique();
            $table->char('password', 60);
            $table->boolean('is_active')->default(true);
            $table->tinyInteger('job')->unsigned();
            $table->char('serial_code', 50)->unique();
            $table->rememberToken();
            $table->bigInteger('created_by')->nullable()->unsigned();
            $table->foreign('created_by')->references('id')->on('users');
            $table->bigInteger('updated_by')->nullable()->unsigned();
            $table->foreign('updated_by')->references('id')->on('users');
            $table->boolean('is_approved')->nullable();
            $table->bigInteger('approved_by')->nullable()->unsigned();
            $table->foreign('approved_by')->references('id')->on('users');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('last_seen')->nullable();
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
        Schema::dropIfExists('users');
    }
};
