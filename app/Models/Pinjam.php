<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pinjam extends Model
{
    protected $table='pinjam';

    function pinjamnotes()
    {
        return $this->belongsTo('App\Models\PinjamNotes','pinjam_notes_id');
    }
    function user()
    {
        return $this->belongsTo('App\User','pengguna_pic_pinjam_id');
    }
    function peminjam()
    {
        return $this->belongsTo('App\User','users_peminjam_id');
    }
    function ruang()
    {
        return $this->belongsTo('App\Models\Ruang','ruang_id');
    }
    function pinjamalat()
    {
        return $this->hasMany('App\Models\PinjamAlat','pinjam_id');
    }
}
