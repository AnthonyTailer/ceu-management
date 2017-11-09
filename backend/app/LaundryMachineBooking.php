<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LaundryMachineBooking extends Model
{
    protected $fillable = [
        'start', 'end', 'day', 'id_user', 'id_machine'
    ];
}
