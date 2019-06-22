<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PinjamNotes extends Model
{
    protected $table='pinjam_notes';
    function pinjam()
    {
        return $this->belongsTo('App\Models\Pinjam','pinjam_id');
    }
    function user()
    {
        return $this->belongsTo('App\User','pengguna_pic_pinjam_id');
    }
}
