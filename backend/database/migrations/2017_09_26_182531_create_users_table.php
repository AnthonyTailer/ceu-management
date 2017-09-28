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
     *
     */

    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('fullName', 80);
            $table->string('email')->unique();
            $table->string('registration', 9)->unique();
            $table->string('password');
            $table->string('cpf', 11)->unique();
            $table->string('rg', 10)->unique();
            $table->enum('genre', ['M', 'F']);
            $table->boolean('is_bse_active')->nullable();
            $table->boolean('is_admin');

            $table->integer('id_course')->unsigned()->nullable();
            $table->integer('id_apto')->unsigned()->nullable();
            $table->foreign('id_course')->references('id')->on('courses');
            $table->foreign('id_apto')->references('id')->on('apartaments')->nullable();

            $table->timestamps();
            $table->rememberToken();
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
