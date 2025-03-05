<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Registros;
use App\Models\OrdenProduccion;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Carbon\Carbon;

class RegistroController extends Controller
{
    /**
     * Obtener todos los registros.
     */
   // Obtener todos los registros
   public function index()
   {
       return response()->json(Registros::all());
   }

   // Obtener un registro por ID de orden
   public function RegistroOrden($idorden)
   {
       $registro = DB::table('ordenproduccion')
           ->join('modelo', 'ordenproduccion.idmodelo', '=', 'modelo.idmodelo')
           ->leftJoin('registros_produccion', 'ordenproduccion.idorden', '=', 'registros_produccion.idorden')
           ->select(
               'ordenproduccion.idorden',
               'ordenproduccion.piezas_solicitadas',
               'modelo.minutosxequipo',
               DB::raw('FLOOR(ordenproduccion.piezas_solicitadas * modelo.minutosxequipo / 60) as tiempo_estimado_horas'),
               DB::raw('COALESCE(registros_produccion.piezas_realizadas, 0) as piezas_realizadas'),
               DB::raw('(COALESCE(registros_produccion.piezas_realizadas, 0) / ordenproduccion.piezas_solicitadas) * 100 AS productividad')
           )
           ->where('ordenproduccion.idorden', $idorden)
           ->first();
   
       if (!$registro) {
           return response()->json(['message' => 'Orden no encontrada'], 404);
       }
   
       return response()->json($registro);
   }
   
   
   

   

   // Crear un nuevo registro
   public function store(Request $request)
   {
       $registro = Registros::create($request->all());

       return response()->json(['message' => 'Registro creado', 'registro' => $registro], 201);
   }

   // Actualizar un registro existente
   public function update(Request $request, $id)
   {
       $registro = Registros::find($id);

       if (!$registro) {
           return response()->json(['message' => 'Registro no encontrado'], 404);
       }

       $registro->update($request->all());

       return response()->json(['message' => 'Registro actualizado', 'registro' => $registro]);
   }

   // Eliminar un registro
   public function destroy($id)
   {
       $registro = Registros::find($id);

       if (!$registro) {
           return response()->json(['message' => 'Registro no encontrado'], 404);
       }

       $registro->delete();

       return response()->json(['message' => 'Registro eliminado']);
   }

   public function actualizarPiezas(Request $request)
{
    $idorden = $request->input('idorden');
    $piezas_realizadas = $request->input('piezas_realizadas');

    if (!$idorden || !$piezas_realizadas) {
        return response()->json(['message' => 'Faltan datos'], 400);
    }

    DB::table('registros_produccion')->updateOrInsert(
        ['idorden' => $idorden],
        ['piezas_realizadas' => $piezas_realizadas]
    );

    return response()->json(['message' => 'Piezas actualizadas correctamente']);
}

}