<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SegmentosModel extends Model
{
    protected $table = 'segmentos';

    public $timestamps = false;
    
    protected $fillable = [
        'fecha_inicio',
        'fecha_fin',
        'tiempo_seg',
        'fk_llamada'
    ];

    public function llamada()
    {
        return $this->belongsTo(LlamadasModel::class, "fk_llamada", "id");
    }

}
