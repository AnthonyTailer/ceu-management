<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StandardWorkingTime extends Model
{
    protected $fillable = [
        'init', 'end', 'period', 'day'
    ];
}
