<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use JWTAuth;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    public function course(){
        return $this->belongsTo('App\Course', 'id_course');
    }

    public function apto(){
        return $this->belongsTo('App\Apartament', 'id_apto');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fullName', 'email', 'password', 'registration', 'cpf', 'rg', 'age', 'genre', 'is_bse_active', 'is_admin', 'id_course', 'id_apto'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function handle() {

//        Schema::table('users', function ($table) {
//            $table->softDeletes();
//        });
//
        $this->fire();
    }

}
