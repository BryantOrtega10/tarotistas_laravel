<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EspecialidadesTatoristaModel extends Model
{
    
    protected $table = 'especialidad_tarotista';

    public $timestamps = false;

    protected $fillable = [
        'fk_especialidad',
        'fk_tarotista',
    ];
    

    public function especialidad(){
        return $this->belongsTo(EspecialidadesModel::class, "fk_especialidad", "id");
    }

    public function tarotista(){
        return $this->belongsTo(TarotistasModel::class, "fk_tarotista", "id");
    }
}
