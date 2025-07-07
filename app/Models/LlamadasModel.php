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
        'tarifa',
        'por_comision',
        'tiempo_mins',
        'subtotal',
        'comision',
        'total',
        'estado_llamada',
        'estado_pago_cli',
        'estado_pago_tar',
        'respuesta_payu',
        'calificacion',
        'comentario',
        'respuesta_com',
        'fk_cliente_tarotista',
        'fk_pago'
    ];

    public function cliente_tarotista(){
        $this->belongsTo(ClienteTarotistaModel::class, "fk_cliente_tarotista", "id");
    }

    public function pago(){
        $this->belongsTo(PagosModel::class, "fk_pago", "id");
    }
    

    public function txtEstadoLlamada(): Attribute {
        return Attribute::make(
            get: fn () => [1 => "Solicitada", 2 => "Cancelada", 3 => "En llamada", 4 => "Terminada"][$this->estado_llamada]
        );
    }

    public function txtEstadoPagoCliente(): Attribute {
        return Attribute::make(
            get: fn () => [1 => "Pago Innecesario", 2 => "Pendiente", 3 => "Pagado", 4 => "Rechazado"][$this->estado_pago_cli]
        );
    }

    public function txtEstadoPagoTarotista(): Attribute {
        return Attribute::make(
            get: fn () => [1 => "Pago Innecesario", 2 => "Sin Pagar", 3 => "Pagado"][$this->estado_pago_tar]
        );
    }

}
