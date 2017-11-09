<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LaundryMachine extends Model
{
    protected $fillable = [
        'capacity'
    ];

}
