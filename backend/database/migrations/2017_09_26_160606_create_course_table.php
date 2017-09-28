<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('courseName', 255);
            $table->integer('duration');
            $table->enum('type', ['Graduação', 'Pós-Graduação', 'Mestrado', 'Doutorado', 'Ensino Médio', 'Ensino Técnico']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return voidd
     */
    public function down()
    {
        Schema::dropIfExists('courses');
    }
}
