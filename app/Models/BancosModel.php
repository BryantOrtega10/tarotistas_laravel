<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BancosModel extends Model
{
    protected $table = 'bancos';

    protected $fillable = [
        'nombre',
        'ap_tipo_cuenta',
        'fk_pais'
    ];

    public function pais(){
        return $this->belongsTo(PaisesModel::class, "fk_pais","id");
    }
}
