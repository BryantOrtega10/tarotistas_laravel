<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class TarotistasModel extends Model
{
    protected $table = 'tarotistas';

    protected $fillable = [
        'nombre',
        'descripcion_corta',
        'estado',
        'estado_conexion',
        'horario',
        'anios_exp',
        'calificacion',
        'saldo',
        'tipo_cuenta',
        'cuenta',
        'fk_banco',
        'fk_pais',
        'fk_user',
    ];


    public function txtHorarioInicio(): Attribute
    {
        return Attribute::make(
            get: function() {
                if(!isset($this->horario)) return "";
                $horarios = explode(" - ",$this->horario);
                return date("H:i", strtotime($horarios[0]));
            }
        );
    }

    public function txtHorarioFin(): Attribute
    {
        return Attribute::make(
            get: function() {
                if(!isset($this->horario)) return "";
                $horarios = explode(" - ",$this->horario);
                return date("H:i", strtotime($horarios[1]));
            }
        );
    }

   
    public function txtEstado(): Attribute
    {
        return Attribute::make(
            get: fn() => [1 => "En Registro", 2 => "Esperando aprobación", 3 => "Activado", 4 => "Rechazado"][$this->estado] ?? null
        );
    }

    public function txtEstadoConexion(): Attribute
    {
        return Attribute::make(
            get: fn() => [1 => "Desconectado", 2 => "En llamada", 3 => "Conectado"][$this->estado_conexion] ?? null
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class, "fk_user", "id");
    }

    public function banco()
    {
        return $this->belongsTo(BancosModel::class, "fk_banco", "id");
    }

    public function pais()
    {
        return $this->belongsTo(PaisesModel::class, "fk_pais", "id");
    }

    public function especialidades()
    {
        return $this->belongsToMany(
            EspecialidadesModel::class,             // Modelo destino
            'especialidad_tarotista',               // Tabla pivote
            'fk_tarotista',                         // FK en pivote hacia tarotistas
            'fk_especialidad'                       // FK en pivote hacia especialidades
        )->withPivot('id');
    }

    public function pagos()
    {
        return $this->hasMany(PagosModel::class, "fk_tarotista", "id");
    }

    public function clientes()
    {
        return $this->hasManyThrough(ClientesModel::class, ClienteTarotistaModel::class, "fk_tarotista", "fk_cliente", "id", "id");
    }
}
