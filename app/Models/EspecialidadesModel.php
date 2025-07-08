<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EspecialidadesModel extends Model
{
    protected $table = 'especialidades';

    public $timestamps = false;
    
    protected $fillable = [
        'nombre'
    ];

}
