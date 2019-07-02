<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table='notifikasi';
    
    function pinjam()
    {
        return $this->belongsTo('App\Models\Pinjam','pinjam_id');
    }
    function user()
    {
        return $this->belongsTo('App\User','user_id');
    }
}
