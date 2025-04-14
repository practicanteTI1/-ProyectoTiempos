<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PausaProduccion extends Model
{
    use HasFactory;
    
    protected $table = 'pausas_produccion';
    protected $primaryKey = 'idpausa';

    protected $fillable = ['idorden', 'motivo', 'inicio_pausa', 'fin_pausa'];

    public $timestamps = false;
}
