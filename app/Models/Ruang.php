<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ruang extends Model
{
    protected $table='ruang';
    
    function eselon1()
    {
        return $this->belongsTo('App\Models\Eselon1','eselon1_id');
    }
    
    function pengguna()
    {
        return $this->belongsTo('App\User','pengguna_pic_ruang_id');
    }
    
}
