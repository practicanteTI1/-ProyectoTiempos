<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modelo extends Model
{
    use HasFactory;

    protected $table = 'modelo';
    protected $primaryKey = 'idmodelo';
    public $timestamps = false;

    protected $fillable = ['nombre', 'tiempo_ensamble', 'tiempo_preparacion'];

    // Relación con OrdenProduccion
public function ordenes()
{
    return $this->hasMany(OrdenProduccion::class);
}

}
