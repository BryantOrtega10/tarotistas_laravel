<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientesModel extends Model
{
    protected $table = 'clientes';

    protected $fillable = [
        'nombre',
        'fecha_nacimiento',
        'token_payu',
        'fk_user',
    ];

    public function user(){
        return $this->belongsTo(User::class, "fk_user", "id");
    }

    public function tarotistas(){
        return $this->hasManyThrough(TarotistasModel::class, ClienteTarotistaModel::class, "fk_cliente", "fk_tarotista", "id", "id");
    }
}
