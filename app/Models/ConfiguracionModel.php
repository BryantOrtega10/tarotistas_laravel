<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionModel extends Model
{
    
    protected $table = 'configuracion';

    protected $fillable = [
        'precio_min',
        'por_comision',
        'fk_last_user'
    ];

    public function last_user(){
        return $this->belongsTo(User::class, "fk_last_user", "id");
    }

}
