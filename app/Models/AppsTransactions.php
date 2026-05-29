<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class AppsTransactions extends Model
{
   protected $table = 'apps_transactions';

    protected $fillable = [
        'platform',
        'purchase_token',
        'order_id',
        'tokens',
        'valor',
        'fk_paquete',
        'status',
        'fk_cliente',
    ];

    public function cliente()
    {
        return $this->belongsTo(ClientesModel::class, "fk_cliente", "id");
    }

    public function paquete()
    {
        return $this->belongsTo(PaquetesModel::class, "fk_paquete", "id");
    }


    public function txtStatus(): Attribute
    {
        return Attribute::make(
            get: fn() => [0 => "Pendiente", 1 => "Verificando pago", 2 => "Aprobado", 3 => "Rechazado", 4 => "Anulado", 5 => "Error"][$this->status]
        );
    }
}
