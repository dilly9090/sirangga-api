<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PinjamAlat extends Model
{
    protected $table='pinjam_alat';

    function pinjam()
    {
        return $this->belongsTo('App\Models\Pinjam','pinjam_id');
    }
    function alat()
    {
        return $this->belongsTo('App\Models\Alat','alat_id');
    }
}
