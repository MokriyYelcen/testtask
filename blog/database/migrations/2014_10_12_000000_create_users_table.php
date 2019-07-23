<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('login');
            $table->string('password');
            $table->string('token')->nullable(true);
            $table->bigInteger('color_id')->unsigned()->nullable(true);
            $table->boolean('isAdmin')->default(false);
            $table->boolean('muted')->default(false);
            $table->boolean('banned')->default(false);
            $table->foreign('color_id')
                ->references('id')->on('colors')
                ->onDelete('cascade');

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
}
