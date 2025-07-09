<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClienteTarotistaModel extends Model
{
    protected $table = 'cliente_tarotista';

    public $timestamps = false;

    protected $fillable = [
        'mensajes_gratis',
        'fk_cliente',
        'fk_tarotista'
    ];

    public function tarotista()
    {
        return $this->belongsTo(TarotistasModel::class, "fk_tarotista", "id");
    }

    public function cliente()
    {
        return $this->belongsTo(ClientesModel::class, "fk_cliente", "id");
    }

    public function chats()
    {
        return $this->hasMany(ChatsModel::class, "fk_cliente_tarotista", "id");
    }

    public function llamadas()
    {
        return $this->hasMany(LlamadasModel::class, "fk_cliente_tarotista", "id");
    }

    public function ultimoChat()
    {
        return $this->hasOne(ChatsModel::class, 'fk_cliente_tarotista', 'id')->latestOfMany();
    }

    public function ultimoComentario()
    {
        return $this->hasOne(LlamadasModel::class, 'fk_cliente_tarotista', 'id')
            ->where('estado_llamada', 4)
            ->whereNotNull('comentario')
            ->latestOfMany();
    }
}
