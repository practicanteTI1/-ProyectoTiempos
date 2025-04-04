<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registros extends Model
{
    use HasFactory;

    protected $table = 'registros_produccion'; // Nombre de la tabla en la BD

    protected $primaryKey = 'idregistro'; // Clave primaria personalizada

    public $timestamps = true; // Laravel manejará automáticamente `created_at`
    const UPDATED_AT = null; // Evita que Laravel intente actualizar `updated_at`

    protected $fillable = [
        'idorden',
        'idlinea',
        'inicio',
        'registro_tiempo',
        'fin',
        'minutos',
        'piezas_realizadas'
    ];

    protected $casts = [
        'inicio' => 'datetime',
        'registro_tiempo' => 'datetime',
        'fin' => 'datetime',
        'minutos' => 'string'
    ];

    // Relación con Orden (Si existe una tabla 'ordenes')
    public function orden()
    {
        return $this->belongsTo(OrdenProduccion::class, 'idorden');
    }

    // Relación con Línea de Producción
    public function linea()
    {
        return $this->belongsTo(LineaProduccion::class, 'idlinea');
    }
}
