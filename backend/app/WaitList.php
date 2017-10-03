<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WaitList extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_apto', 'id_user'
    ];
}