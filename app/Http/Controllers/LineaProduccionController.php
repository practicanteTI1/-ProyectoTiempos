<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LineaProduccion;

class LineaProduccionController extends Controller
{
     // Obtener todas las líneas de producción
     public function index()
     {
         return response()->json(LineaProduccion::all());
     }
 
     // Obtener una línea de producción por ID
     public function show($id)
     {
         $linea = LineaProduccion::find($id);
 
         if (!$linea) {
             return response()->json(['message' => 'Línea de producción no encontrada'], 404);
         }
 
         return response()->json($linea);
     }
 
     // Crear una nueva línea de producción
     public function store(Request $request)
     {
         $linea = LineaProduccion::create($request->all());
 
         return response()->json(['message' => 'Línea creada', 'linea' => $linea], 201);
     }
 
     // Actualizar una línea de producción
     public function update(Request $request, $id)
     {
         $linea = LineaProduccion::find($id);
 
         if (!$linea) {
             return response()->json(['message' => 'Línea de producción no encontrada'], 404);
         }
 
         $linea->update($request->all());
 
         return response()->json(['message' => 'Línea actualizada', 'linea' => $linea]);
     }
 
     // Eliminar una línea de producción
     public function destroy($id)
     {
         $linea = LineaProduccion::find($id);
 
         if (!$linea) {
             return response()->json(['message' => 'Línea de producción no encontrada'], 404);
         }
 
         $linea->delete();
 
         return response()->json(['message' => 'Línea eliminada']);
     }
}
