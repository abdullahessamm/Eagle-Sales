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
            $table->string('f_name', 20);
            $table->string('l_name', 20);
            $table->string('avatar_uri', 255)->nullable();
            $table->json('avatar_pos')->nullable();
            $table->string('email', 50)->unique();
            $table->string('username', 50)->unique();
            $table->char('password', 60);
            $table->boolean('is_active')->default(true);
            $table->tinyInteger('job')->unsigned();
            $table->char('serial_code', 25)->unique();
            $table->bigInteger('created_by')->nullable()->unsigned();
            $table->foreign('created_by')->references('id')->on('users');
            $table->bigInteger('updated_by')->nullable()->unsigned();
            $table->foreign('updated_by')->references('id')->on('users');
            $table->boolean('is_approved')->nullable();
            $table->bigInteger('approved_by')->nullable()->unsigned();
            $table->foreign('approved_by')->references('id')->on('users');
            $table->timestamp('approved_at')->nullable();
            $table->enum('lang', ['en', 'ar', 'in'])->default('en');
            $table->enum('gender', ['male', 'female']);
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('last_seen')->nullable();
            $table->bigInteger('linked_seller')->unsigned()->nullable();
            $table->foreign('linked_seller')->references('id')->on('users');
            $table->tinyInteger('max_commissions_num_for_seller')->unsigned()->nullable();
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
