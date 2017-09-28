<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Apartament extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'number', 'vacancy', 'capacity', 'id_block'
    ];

}
