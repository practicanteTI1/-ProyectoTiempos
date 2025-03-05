<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PausaProduccion;

class PausaProduccionController extends Controller
{
     // Iniciar una pausa
     public function iniciarPausa(Request $request)
     {
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
         $pausa = PausaProduccion::where('idorden', $idorden)
                                 ->whereNull('fin_pausa')
                                 ->orderBy('inicio_pausa', 'desc')
                                 ->first();
 
         if ($pausa) {
             $pausa->update(['fin_pausa' => now()]);
             return response()->json(['message' => 'Pausa finalizada', 'data' => $pausa]);
         }
 
         return response()->json(['message' => 'No hay pausas activas'], 404);
    }
 
 
}
