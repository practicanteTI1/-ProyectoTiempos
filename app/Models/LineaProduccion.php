<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LineaProduccion extends Model
{
    use HasFactory;

    protected $table = 'linea_produccion';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = ['nombre'];

   // Relación con OrdenProduccion
public function ordenes()
{
    return $this->hasMany(Orden::class, 'idlinea', 'id');
}

}
