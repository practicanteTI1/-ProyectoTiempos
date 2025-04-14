<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PausaProduccion;

class PausaProduccionController extends Controller
{
     // Iniciar una pausa
     public function iniciarPausa(Request $request)
     {
         $validated = $request->validate([
             'idorden' => 'required|exists:ordenproduccion,idorden',  // Asegura que el idorden exista en la tabla ordenproduccion
             'motivo' => 'required|string|max:255',  // Asegura que el motivo esté presente y tenga un formato válido
         ]);
     
         $pausa = PausaProduccion::create([
             'idorden' => $request->idorden,
             'motivo' => $request->motivo,
             'inicio_pausa' => now(),
         ]);
     
         return response()->json(['message' => 'Pausa iniciada', 'data' => $pausa], 201);
     }
     
 
     // Finalizar la última pausa activa
     public function finalizarPausa($idorden)
     {
         // Buscar la última pausa activa
         $pausa = PausaProduccion::where('idorden', $idorden)
                                 ->whereNull('fin_pausa')
                                 ->orderBy('inicio_pausa', 'desc')
                                 ->first();
     
         if ($pausa) {
             // Si el motivo es "Comida", actualizamos el fin_pausa
             if ($pausa->motivo === 'Comida') {
                 $pausa->update(['fin_pausa' => now()]);
                 return response()->json(['message' => 'Pausa de comida finalizada', 'data' => $pausa]);
             }
     
             // Si no es "Comida", solo se guarda el motivo pero no actualizamos fin_pausa
             return response()->json(['message' => 'Pausa no finalizada (el cronómetro sigue corriendo)', 'data' => $pausa]);
         }
     
         return response()->json(['message' => 'No hay pausas activas'], 404);
     }
     
 
 
}
