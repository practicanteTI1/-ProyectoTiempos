<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Modelo;

class ModeloController extends Controller
{
    public function index()
    {
        return response()->json(Modelo::all());
    }

    public function show($id)
    {
        $modelo = Modelo::find($id);
        if (!$modelo) {
            return response()->json(['message' => 'Modelo no encontrado'], 404);
        }
        return response()->json($modelo);
    }

    public function store(Request $request)
    {
        $modelo = Modelo::create($request->all());
        return response()->json(['message' => 'Modelo creado', 'modelo' => $modelo], 201);
    }

    public function update(Request $request, $id)
    {
        $modelo = Modelo::find($id);
        if (!$modelo) {
            return response()->json(['message' => 'Modelo no encontrado'], 404);
        }
        
        $modelo->update($request->all());
        return response()->json(['message' => 'Modelo actualizado', 'modelo' => $modelo]);
    }

    public function destroy($id)
    {
        $modelo = Modelo::find($id);
        if (!$modelo) {
            return response()->json(['message' => 'Modelo no encontrado'], 404);
        }
        
        $modelo->delete();
        return response()->json(['message' => 'Modelo eliminado']);
    }
}
