<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaisesModel extends Model
{
    protected $table = 'paises';

    public $timestamps = false;
    
    protected $fillable = [
        'nombre',
        'bandera'
    ];

    public function bancos(){
        return $this->hasMany(BancosModel::class, "fk_pais", "id");
    }
}
