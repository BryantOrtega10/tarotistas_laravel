<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class ChatsModel extends Model
{
    protected $table = 'chats';

    protected $fillable = [
        'mensaje',
        'origen',
        'tipo',
        'leido',
        'fk_cliente_tarotista',
    ];

    public function cliente_tarotista(){
        $this->belongsTo(ClienteTarotistaModel::class, "fk_cliente_tarotista", "id");
    }


    public function txtOrigen(): Attribute {
        return Attribute::make(
            get: fn () => [1 => "Cliente", 2 => "Tarotista"][$this->origen]
        );
    }
    
    public function txtTipo(): Attribute {
        return Attribute::make(
            get: fn () => [1 => "Mensaje", 2 => "Llamada"][$this->tipo]
        );
    }
    
    
    

}
