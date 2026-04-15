<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class LlamadasModel extends Model
{
    protected $table = 'llamadas';

    protected $fillable = [
        'fecha_inicio',
        'fecha_fin',
        'por_comision',
        'tiempo_mins',
        'subtotal',
        'comision',
        'total',
        'estado_llamada',
        'estado_pago_tar',
        'calificacion',
        'comentario',
        'respuesta_com',
        'fk_cliente_tarotista',
        'fk_pago',
        'type',
        'tarifa_valor_min',
        'tarifa_token_min',
        'tokens_gastados',
    ];

    public function segmentos(){
        return $this->hasMany(SegmentosModel::class, "fk_llamada", "id");
    }

    public function cliente_tarotista(){
        return $this->belongsTo(ClienteTarotistaModel::class, "fk_cliente_tarotista", "id");
    }

    public function pago(){
        return $this->belongsTo(PagosModel::class, "fk_pago", "id");
    }
    

    public function txtEstadoLlamada(): Attribute {
        return Attribute::make(
            get: fn () => [1 => "Solicitada", 2 => "Cancelada", 3 => "En llamada", 4 => "Terminada"][$this->estado_llamada]
        );
    }

    public function txtEstadoPagoTarotista(): Attribute {
        return Attribute::make(
            get: fn () => [1 => "Pago Innecesario", 2 => "Sin Pagar", 3 => "Pagado"][$this->estado_pago_tar]
        );
    }

}
