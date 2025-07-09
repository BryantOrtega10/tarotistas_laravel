<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagosModel extends Model
{
    //
    protected $table = 'pagos';

    protected $fillable = [
        'valor',
        'descripcion',
        'fk_entry_user',
        'fk_tarotista',
    ];

    public function entry_user(){
        return $this->belongsTo(User::class, "fk_entry_user", "id");
    }
    
    public function tarotista(){
        return $this->belongsTo(TarotistasModel::class, "fk_tarotista", "id");
    }

    public function llamadas(){
        return $this->hasMany(LlamadasModel::class, "fk_pago", "id");
    }
}
