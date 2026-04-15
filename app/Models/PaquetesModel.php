<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class PaquetesModel extends Model
{
    protected $table = 'paquetes';

    protected $fillable = [
        'nombre',
        'imagen',
        'tokens',
        'estado',
        'valor',
        'fk_last_user',
    ];

    public function last_user(){
        return $this->belongsTo(User::class, "fk_last_user", "id");
    }

    public function txtEstado(): Attribute {
        return Attribute::make(
            get: fn () => [0 => "Oculto", 1 => "Visible", 2 => "Eliminado"][$this->estado]
        );
    }

}
