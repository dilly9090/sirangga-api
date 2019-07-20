<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','created_at','updated_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    function eselon1()
    {
        return $this->belongsTo('App\Models\Eselon1','eselon1_id');
    }
    public function eselon2()
    {
        return $this->belongsTo('App\Models\Eselon2','eselon2_id');
        // return $this->hasOne('App\Models\Eselon2','eselon2_id');
    }
    function role()
    {
        return $this->belongsTo('App\Models\Role','role_id');
    }
}
