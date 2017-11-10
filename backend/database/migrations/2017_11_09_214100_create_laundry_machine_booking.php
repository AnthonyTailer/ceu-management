<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLaundryMachineBooking extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('laundry_machine_bookings', function (Blueprint $table) {
            $table->increments('id');
            $table->time('start');
            $table->time('end');
            $table->date('day');

            $table->integer('id_user')->unsigned();
            $table->integer('id_machine')->unsigned();
            $table->foreign('id_user')->references('id')->on('users')->onDelete("cascade");
            $table->foreign('id_machine')->references('id')->on('laundry_machines')->onDelete("cascade");
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
        //
    }
}

