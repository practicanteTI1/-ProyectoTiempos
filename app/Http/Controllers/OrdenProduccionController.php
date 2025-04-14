<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrdenProduccion;  
use App\Models\Registros;

class OrdenProduccionController extends Controller
{
     // Obtener todas las órdenes de producción
    // OrdenProduccionController.php

// Mostrar todas las órdenes con sus relaciones de línea y modelo
    public function index()
    {
        // Recuperamos las órdenes con las relaciones de 'linea' y 'modelo'
        $ordenes = OrdenProduccion::with(['linea', 'modelo','registros'])->get();

        // Retornamos las órdenes en formato JSON
        return response()->json($ordenes);
    }

    // Mostrar una orden específica por ID
    public function show($id)
    {
        $orden = OrdenProduccion::with(['linea', 'modelo'])->findOrFail($id);
        return response()->json($orden);
    }

    // Crear una nueva orden
    public function store(Request $request)
    {
        $request->validate([
            'idlinea' => 'required|exists:linea_produccion,id',
            'idmodelo' => 'required|exists:modelo,idmodelo',
            'piezas_solicitadas' => 'required|integer|min:1',
        ]);
    
        // Crear la orden de producción
        $orden = OrdenProduccion::create([
            'orden' => $request->orden,
            'idlinea' => $request->idlinea,
            'idmodelo' => $request->idmodelo,
            'piezas_solicitadas' => $request->piezas_solicitadas,
        ]);
    
        // Crear un registro en la tabla registro_produccion con el idorden e idlinea
        Registros::create([
            'idorden' => $orden->idorden, // ID de la orden creada
            'idlinea' => $request->idlinea, // Línea de producción
        ]);
    
        return response()->json($orden, 201);
    }
    

    // Actualizar una orden existente
    public function update(Request $request, $id)
    {
        $request->validate([
            'idlinea' => 'required|exists:linea_produccion,id',
            'idmodelo' => 'required|exists:modelo,idmodelo',
            'piezas_solicitadas' => 'required|integer|min:1',
            
        ]);

        // Encontrar la orden
        $orden = OrdenProduccion::findOrFail($id);

        // Actualizar la orden con los datos recibidos
        $orden->update([
            'orden' => $request->orden,
            'idlinea' => $request->idlinea,
            'idmodelo' => $request->idmodelo,
            'piezas_solicitadas' => $request->piezas_solicitadas,
            
        ]);

        // Retornar la orden actualizada
        return response()->json($orden);
    }

    // Eliminar una orden
    public function destroy($id)
    {
        // Encontrar la orden
        $orden = OrdenProduccion::findOrFail($id);

        // Elimminutosxpieza,inar la orden
        $orden->delete();

        // Retornar una respuesta de éxito
        return response()->json(['message' => 'Orden eliminada exitosamente']);
    }

    // Obtener todas las líneas (para el formulario de creación/edición)
    public function getLineas()
    {
        $lineas = Linea::all();
        return response()->json($lineas);
    }

    // Obtener todos los modelos (para el formulario de creación/edición)
    public function getModelos()
    {
        $modelos = Modelo::all();
        return response()->json($modelos);
    }
}
