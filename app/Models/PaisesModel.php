<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaisesModel extends Model
{
    protected $table = 'paises';

    protected $fillable = [
        'nombre',
        'bandera'
    ];

}
