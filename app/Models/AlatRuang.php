<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlatRuang extends Model
{
    protected $table='alat_ruang';
    protected $hidden = [
        'created_at', 'updated_at',
    ];
}
