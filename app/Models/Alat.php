<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alat extends Model
{
    protected $table='alat';
    protected $hidden = [
        'created_at', 'updated_at',
    ];
}
