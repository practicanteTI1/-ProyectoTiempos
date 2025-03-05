<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registros extends Model
{
    use HasFactory;

    protected $table = 'registros'; // Nombre de la tabla en la BD

    protected $primaryKey = 'idregistro'; // Clave primaria personalizada

    public $timestamps = true; // Laravel manejará automáticamente `created_at`

    protected $fillable = [
        'idorden',
        'inicio',
        'fin',
        'piezas_realizadas'
    ];

    protected $casts = [
        'inicio' => 'datetime',
        'fin' => 'datetime',
    ];

    // Relación con Orden (Si existe una tabla 'ordenes')
    public function orden()
    {
        return $this->belongsTo(OrdenProduccion::class, 'idorden');
    }
}

