<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionModel extends Model
{
    
    protected $table = 'configuracion';

    protected $fillable = [
        'token_min',
        'valor_min',
        'por_comision',
        'fk_last_user'
    ];

    public function last_user(){
        return $this->belongsTo(User::class, "fk_last_user", "id");
    }

}
