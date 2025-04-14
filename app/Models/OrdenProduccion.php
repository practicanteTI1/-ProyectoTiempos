<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Registros;

class OrdenProduccion extends Model
{
    use HasFactory;

    protected $table = 'ordenproduccion'; // Nombre exacto de la tabla en la BD
    protected $primaryKey = 'idorden'; // Definir la clave primaria si no es 'id'
    public $timestamps = false; // Desactivar timestamps si no tienes `created_at` y `updated_at`

    // Definir los campos que se pueden asignar en masa
    protected $fillable = [
        'orden',
        'idlinea',
        'idmodelo',
        'piezas_solicitadas',
        'created_at'
    ];

    // Aseguramos que el accessor se incluya al convertir a JSON
    protected $appends = ['piezas_realizadas'];

    // Relaciones con otras tablas (Ejemplo)
    public function linea()
    {
        return $this->belongsTo(LineaProduccion::class, 'idlinea');
    }

    public function modelo()
    {
        return $this->belongsTo(Modelo::class, 'idmodelo');
    }


    // Relación con registros_produccion
    public function registros()
    {
        return $this->hasMany(Registros::class, 'idorden');
    }

    public function getPiezasRealizadasAttribute()
    {
        // Ordena los registros por created_at y toma el último registro
        $ultimoRegistro = $this->registros->sortBy('created_at')->last();
        return $ultimoRegistro ? $ultimoRegistro->piezas_realizadas : 0;
    }
    

}

