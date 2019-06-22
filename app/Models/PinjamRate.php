<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PinjamRate extends Model
{
    protected $table='pinjam_rate';
    function pinjam()
    {
        return $this->belongsTo('App\Models\Pinjam','pinjam_id');
    }
}
