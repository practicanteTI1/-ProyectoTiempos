<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Registros;
use App\Models\OrdenProduccion;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RegistroController extends Controller
{
    // Obtener todos los registros
    public function index()
    {
        return response()->json(Registros::all());
    }

    //Funcion  para obtener el tiempo de preparacion
    // Obtener un registro por ID de orden
    public function RegistroOrden($idorden)
    {
        $registro = DB::table('ordenproduccion')
            ->join('modelo', 'ordenproduccion.idmodelo', '=', 'modelo.idmodelo')
            ->leftJoin('registros_produccion', 'ordenproduccion.idorden', '=', 'registros_produccion.idorden')
            ->leftJoin('linea_produccion', 'ordenproduccion.idlinea', '=', 'linea_produccion.id')
            ->select(
                'ordenproduccion.idorden',
                'ordenproduccion.piezas_solicitadas',
                'modelo.tiempo_ensamble',
                'modelo.tiempo_preparacion',
                'modelo.nombre as nombre_modelo',
                'linea_produccion.nombre as nombre',
                DB::raw('
                CASE 
                    WHEN FLOOR(ordenproduccion.piezas_solicitadas * 
                        (HOUR(modelo.tiempo_ensamble) * 3600 + 
                         MINUTE(modelo.tiempo_ensamble) * 60 + 
                         SECOND(modelo.tiempo_ensamble))) >= 3600 
                    THEN CONCAT(
                        FLOOR(ordenproduccion.piezas_solicitadas * 
                            (HOUR(modelo.tiempo_ensamble) * 3600 + 
                             MINUTE(modelo.tiempo_ensamble) * 60 + 
                             SECOND(modelo.tiempo_ensamble)) / 3600), 
                        " ", IF(
                            FLOOR(ordenproduccion.piezas_solicitadas * 
                                (HOUR(modelo.tiempo_ensamble) * 3600 + 
                                 MINUTE(modelo.tiempo_ensamble) * 60 + 
                                 SECOND(modelo.tiempo_ensamble)) / 3600) = 1, "hora", "horas"),
                        IF(
                            FLOOR((ordenproduccion.piezas_solicitadas * 
                                (HOUR(modelo.tiempo_ensamble) * 3600 + 
                                 MINUTE(modelo.tiempo_ensamble) * 60 + 
                                 SECOND(modelo.tiempo_ensamble)) % 3600) / 60) > 0,
                            CONCAT(" ", FLOOR((ordenproduccion.piezas_solicitadas * 
                                (HOUR(modelo.tiempo_ensamble) * 3600 + 
                                 MINUTE(modelo.tiempo_ensamble) * 60 + 
                                 SECOND(modelo.tiempo_ensamble)) % 3600) / 60), " minutos"),
                            ""
                        ),
                        IF(
                            FLOOR((ordenproduccion.piezas_solicitadas * 
                                (HOUR(modelo.tiempo_ensamble) * 3600 + 
                                 MINUTE(modelo.tiempo_ensamble) * 60 + 
                                 SECOND(modelo.tiempo_ensamble)) % 60)) > 0,
                            CONCAT(" y ", FLOOR((ordenproduccion.piezas_solicitadas * 
                                (HOUR(modelo.tiempo_ensamble) * 3600 + 
                                 MINUTE(modelo.tiempo_ensamble) * 60 + 
                                 SECOND(modelo.tiempo_ensamble)) % 60)), " segundos"),
                            ""
                        )
                    )
                    ELSE CONCAT(
                        FLOOR(ordenproduccion.piezas_solicitadas * 
                            (HOUR(modelo.tiempo_ensamble) * 3600 + 
                             MINUTE(modelo.tiempo_ensamble) * 60 + 
                             SECOND(modelo.tiempo_ensamble)) / 60), 
                        " minutos",
                        IF(
                            FLOOR((ordenproduccion.piezas_solicitadas * 
                                (HOUR(modelo.tiempo_ensamble) * 3600 + 
                                 MINUTE(modelo.tiempo_ensamble) * 60 + 
                                 SECOND(modelo.tiempo_ensamble)) % 60)) > 0,
                            CONCAT(" ", FLOOR((ordenproduccion.piezas_solicitadas * 
                                (HOUR(modelo.tiempo_ensamble) * 3600 + 
                                 MINUTE(modelo.tiempo_ensamble) * 60 + 
                                 SECOND(modelo.tiempo_ensamble)) % 60)), " segundos"),
                            ""
                        )
                    )
                END as tiempo_estimado_horas_ensamble
            '),
            
DB::raw('
    CASE 
        WHEN FLOOR(ordenproduccion.piezas_solicitadas * 
            (HOUR(modelo.tiempo_preparacion) * 3600 + 
             MINUTE(modelo.tiempo_preparacion) * 60 + 
             SECOND(modelo.tiempo_preparacion))) >= 3600 
        THEN CONCAT(
            FLOOR(ordenproduccion.piezas_solicitadas * 
                (HOUR(modelo.tiempo_preparacion) * 3600 + 
                 MINUTE(modelo.tiempo_preparacion) * 60 + 
                 SECOND(modelo.tiempo_preparacion)) / 3600), 
            " ", IF(
                FLOOR(ordenproduccion.piezas_solicitadas * 
                    (HOUR(modelo.tiempo_preparacion) * 3600 + 
                     MINUTE(modelo.tiempo_preparacion) * 60 + 
                     SECOND(modelo.tiempo_preparacion)) / 3600) = 1, "hora", "horas"),
            IF(
                FLOOR((ordenproduccion.piezas_solicitadas * 
                    (HOUR(modelo.tiempo_preparacion) * 3600 + 
                     MINUTE(modelo.tiempo_preparacion) * 60 + 
                     SECOND(modelo.tiempo_preparacion)) % 3600) / 60) > 0,
                CONCAT(" ", FLOOR((ordenproduccion.piezas_solicitadas * 
                    (HOUR(modelo.tiempo_preparacion) * 3600 + 
                     MINUTE(modelo.tiempo_preparacion) * 60 + 
                     SECOND(modelo.tiempo_preparacion)) % 3600) / 60), " minutos"),
                ""
            ),
            IF(
                (ordenproduccion.piezas_solicitadas * 
                    (HOUR(modelo.tiempo_preparacion) * 3600 + 
                     MINUTE(modelo.tiempo_preparacion) * 60 + 
                     SECOND(modelo.tiempo_preparacion)) % 60) > 0,
                CONCAT(" y ", FLOOR((ordenproduccion.piezas_solicitadas * 
                    (HOUR(modelo.tiempo_preparacion) * 3600 + 
                     MINUTE(modelo.tiempo_preparacion) * 60 + 
                     SECOND(modelo.tiempo_preparacion)) % 60)), " segundos"),
                ""
            )
        )
        ELSE CONCAT(
            FLOOR(ordenproduccion.piezas_solicitadas * 
                (HOUR(modelo.tiempo_preparacion) * 3600 + 
                 MINUTE(modelo.tiempo_preparacion) * 60 + 
                 SECOND(modelo.tiempo_preparacion)) / 60), 
            " minutos",
            IF(
                (ordenproduccion.piezas_solicitadas * 
                    (HOUR(modelo.tiempo_preparacion) * 3600 + 
                     MINUTE(modelo.tiempo_preparacion) * 60 + 
                     SECOND(modelo.tiempo_preparacion)) % 60) > 0,
                CONCAT(" y ", FLOOR((ordenproduccion.piezas_solicitadas * 
                    (HOUR(modelo.tiempo_preparacion) * 3600 + 
                     MINUTE(modelo.tiempo_preparacion) * 60 + 
                     SECOND(modelo.tiempo_preparacion)) % 60)), " segundos"),
                ""
            )
        )
    END as tiempo_estimado_horas_preparacion
'),
 DB::raw('
    CASE 
        WHEN FLOOR(ordenproduccion.piezas_solicitadas * 
            ((HOUR(modelo.tiempo_ensamble) * 3600 + 
              MINUTE(modelo.tiempo_ensamble) * 60 + 
              SECOND(modelo.tiempo_ensamble)) +
             (HOUR(modelo.tiempo_preparacion) * 3600 + 
              MINUTE(modelo.tiempo_preparacion) * 60 + 
              SECOND(modelo.tiempo_preparacion)))) >= 3600
        THEN CONCAT(
            FLOOR(ordenproduccion.piezas_solicitadas * 
                ((HOUR(modelo.tiempo_ensamble) * 3600 + 
                  MINUTE(modelo.tiempo_ensamble) * 60 + 
                  SECOND(modelo.tiempo_ensamble)) +
                 (HOUR(modelo.tiempo_preparacion) * 3600 + 
                  MINUTE(modelo.tiempo_preparacion) * 60 + 
                  SECOND(modelo.tiempo_preparacion))) / 3600),
            " ", IF(
                FLOOR(ordenproduccion.piezas_solicitadas * 
                    ((HOUR(modelo.tiempo_ensamble) * 3600 + 
                      MINUTE(modelo.tiempo_ensamble) * 60 + 
                      SECOND(modelo.tiempo_ensamble)) +
                     (HOUR(modelo.tiempo_preparacion) * 3600 + 
                      MINUTE(modelo.tiempo_preparacion) * 60 + 
                      SECOND(modelo.tiempo_preparacion))) / 3600) = 1, "hora", "horas"),
            IF(
                FLOOR((ordenproduccion.piezas_solicitadas * 
                    ((HOUR(modelo.tiempo_ensamble) * 3600 + 
                      MINUTE(modelo.tiempo_ensamble) * 60 + 
                      SECOND(modelo.tiempo_ensamble)) +
                     (HOUR(modelo.tiempo_preparacion) * 3600 + 
                      MINUTE(modelo.tiempo_preparacion) * 60 + 
                      SECOND(modelo.tiempo_preparacion))) % 3600) / 60) > 0,
                CONCAT(" ", FLOOR((ordenproduccion.piezas_solicitadas * 
                    ((HOUR(modelo.tiempo_ensamble) * 3600 + 
                      MINUTE(modelo.tiempo_ensamble) * 60 + 
                      SECOND(modelo.tiempo_ensamble)) +
                     (HOUR(modelo.tiempo_preparacion) * 3600 + 
                      MINUTE(modelo.tiempo_preparacion) * 60 + 
                      SECOND(modelo.tiempo_preparacion))) % 3600) / 60), " minutos"),
                ""
            ),
            IF(
                FLOOR((ordenproduccion.piezas_solicitadas * 
                    ((HOUR(modelo.tiempo_ensamble) * 3600 + 
                      MINUTE(modelo.tiempo_ensamble) * 60 + 
                      SECOND(modelo.tiempo_ensamble)) +
                     (HOUR(modelo.tiempo_preparacion) * 3600 + 
                      MINUTE(modelo.tiempo_preparacion) * 60 + 
                      SECOND(modelo.tiempo_preparacion))) % 60)) > 0,
                CONCAT(" y ", FLOOR((ordenproduccion.piezas_solicitadas * 
                    ((HOUR(modelo.tiempo_ensamble) * 3600 + 
                      MINUTE(modelo.tiempo_ensamble) * 60 + 
                      SECOND(modelo.tiempo_ensamble)) +
                     (HOUR(modelo.tiempo_preparacion) * 3600 + 
                      MINUTE(modelo.tiempo_preparacion) * 60 + 
                      SECOND(modelo.tiempo_preparacion))) % 60)), " segundos"),
                ""
            )
        )
        ELSE CONCAT(
            FLOOR(ordenproduccion.piezas_solicitadas * 
                ((HOUR(modelo.tiempo_ensamble) * 3600 + 
                  MINUTE(modelo.tiempo_ensamble) * 60 + 
                  SECOND(modelo.tiempo_ensamble)) +
                 (HOUR(modelo.tiempo_preparacion) * 3600 + 
                  MINUTE(modelo.tiempo_preparacion) * 60 + 
                  SECOND(modelo.tiempo_preparacion))) / 60),
            " minutos",
            IF(
                FLOOR((ordenproduccion.piezas_solicitadas * 
                    ((HOUR(modelo.tiempo_ensamble) * 3600 + 
                      MINUTE(modelo.tiempo_ensamble) * 60 + 
                      SECOND(modelo.tiempo_ensamble)) +
                     (HOUR(modelo.tiempo_preparacion) * 3600 + 
                      MINUTE(modelo.tiempo_preparacion) * 60 + 
                      SECOND(modelo.tiempo_preparacion))) % 60)) > 0,
                CONCAT("  ", FLOOR((ordenproduccion.piezas_solicitadas * 
                    ((HOUR(modelo.tiempo_ensamble) * 3600 + 
                      MINUTE(modelo.tiempo_ensamble) * 60 + 
                      SECOND(modelo.tiempo_ensamble)) +
                     (HOUR(modelo.tiempo_preparacion) * 3600 + 
                      MINUTE(modelo.tiempo_preparacion) * 60 + 
                      SECOND(modelo.tiempo_preparacion))) % 60)), " segundos"),
                ""
            )
        )
    END as valor_total
'),

                DB::raw('COALESCE(registros_produccion.piezas_realizadas, 0) as piezas_realizadas'),
                DB::raw('(COALESCE(registros_produccion.piezas_realizadas, 0) / ordenproduccion.piezas_solicitadas) * 100 AS productividad'),
                'linea_produccion.nombre as nombre_linea'
            )
            ->where('ordenproduccion.idorden', $idorden)
            ->where('ordenproduccion.status', '<>', 'Finalizado')

            ->first();
    
        if (!$registro) {
            return response()->json(['message' => 'Orden no encontrada'], 404);
        }
    
        // Obtener el último registro de piezas realizadas
        $ultimoRegistro = DB::table('registros_produccion')
            ->where('idorden', $idorden)
            ->where('piezas_realizadas', '>', 0)
            ->orderBy('idregistro', 'desc')
            ->first();
    
        // Si hay un último registro, actualizar el valor en el objeto de respuesta
        $registro->piezas_realizadas = $ultimoRegistro ? $ultimoRegistro->piezas_realizadas : 0;
    
        return response()->json($registro);
    }

    
    public function TiempoAcumuladoConPiezasRealizadas($idorden)
    {
        $registro = DB::table('registros_produccion')
            ->join('modelo', 'registros_produccion.idmodelo', '=', 'modelo.idmodelo')
            ->leftJoin('linea_produccion', 'registros_produccion.idlinea', '=', 'linea_produccion.id')
            ->select(
                'registros_produccion.idorden',
                'registros_produccion.piezas_realizadas',
                'modelo.tiempo_ensamble',
                'modelo.tiempo_preparacion',
                'modelo.nombre as nombre_modelo',
                DB::raw('
                    CASE 
                        WHEN registros_produccion.piezas_realizadas > 0
                        THEN CONCAT(
                            FLOOR(registros_produccion.piezas_realizadas * 
                                (HOUR(modelo.tiempo_ensamble) * 3600 + 
                                MINUTE(modelo.tiempo_ensamble) * 60 + 
                                SECOND(modelo.tiempo_ensamble)) / 3600), 
                            " horas ", 
                            FLOOR((registros_produccion.piezas_realizadas * 
                                (HOUR(modelo.tiempo_ensamble) * 3600 + 
                                MINUTE(modelo.tiempo_ensamble) * 60 + 
                                SECOND(modelo.tiempo_ensamble)) % 3600) / 60), 
                            " minutos ", 
                            FLOOR((registros_produccion.piezas_realizadas * 
                                (HOUR(modelo.tiempo_ensamble) * 3600 + 
                                MINUTE(modelo.tiempo_ensamble) * 60 + 
                                SECOND(modelo.tiempo_ensamble)) % 60)),
                            " segundos"
                        )
                        ELSE "0 horas 0 minutos 0 segundos"
                    END as tiempo_acumulado_ensamble
                '),
                DB::raw('
                    CASE 
                        WHEN registros_produccion.piezas_realizadas >= 1
                        THEN CONCAT(
                            "1 hora ", 
                            FLOOR((HOUR(modelo.tiempo_preparacion) * 3600 + 
                                   MINUTE(modelo.tiempo_preparacion) * 60 + 
                                   SECOND(modelo.tiempo_preparacion)) / 60), 
                            " minutos"
                        )
                        ELSE "0 horas 0 minutos"
                    END as tiempo_acumulado_preparacion
                '),
                DB::raw('
                    CASE 
                        WHEN registros_produccion.piezas_realizadas > 0
                        THEN CONCAT(
                            FLOOR((registros_produccion.piezas_realizadas - 1) * 
                                (HOUR(modelo.tiempo_ensamble) * 3600 + 
                                MINUTE(modelo.tiempo_ensamble) * 60 + 
                                SECOND(modelo.tiempo_ensamble)) + 
                                (HOUR(modelo.tiempo_preparacion) * 3600 + 
                                MINUTE(modelo.tiempo_preparacion) * 60 + 
                                SECOND(modelo.tiempo_preparacion)) / 3600),
                            " horas ",
                            FLOOR(((registros_produccion.piezas_realizadas - 1) * 
                                (HOUR(modelo.tiempo_ensamble) * 3600 + 
                                MINUTE(modelo.tiempo_ensamble) * 60 + 
                                SECOND(modelo.tiempo_ensamble)) + 
                                (HOUR(modelo.tiempo_preparacion) * 3600 + 
                                MINUTE(modelo.tiempo_preparacion) * 60 + 
                                SECOND(modelo.tiempo_preparacion))) % 3600 / 60),
                            " minutos ",
                            FLOOR(((registros_produccion.piezas_realizadas - 1) * 
                                (HOUR(modelo.tiempo_ensamble) * 3600 + 
                                MINUTE(modelo.tiempo_ensamble) * 60 + 
                                SECOND(modelo.tiempo_ensamble)) + 
                                (HOUR(modelo.tiempo_preparacion) * 3600 + 
                                MINUTE(modelo.tiempo_preparacion) * 60 + 
                                SECOND(modelo.tiempo_preparacion))) % 60),
                            " segundos"
                        )
                        ELSE "0 horas 0 minutos 0 segundos"
                    END as tiempo_acumulado_total
                ')
            )
            ->where('registros_produccion.idorden', '=', $idorden)
            ->first();
    
        return $registro;
    }
    











    


    // Función para iniciar el tiempo de producción
    public function iniciarTiempo(Request $request)
    {
        $idorden = $request->input('idorden');
        
        if (!$idorden) {
            return response()->json(['message' => 'Faltan datos'], 400);
        }
        
        // Verificar si ya existe un inicio registrado
        $registroExistente = DB::table('registros_produccion')
            ->where('idorden', $idorden)
            ->whereNotNull('inicio')
            ->first();
        
        if ($registroExistente) {
            return response()->json([
                'message' => 'El tiempo ya fue iniciado anteriormente',
                'fecha_guardada' => $registroExistente->inicio // Devolver la fecha existente
            ], 400);
        }
    
        // Registrar el inicio del tiempo de producción
        $fechaInicio = now();
        DB::table('registros_produccion')->insert([
            'idorden' => $idorden,
            'inicio' => $fechaInicio,
            'created_at' => $fechaInicio
        ]);
    
        // Actualizar el estado de la orden a "iniciado"
        DB::table('ordenproduccion')
            ->where('idorden', $idorden)
            ->update(['status' => 'iniciado']);
    
        return response()->json([
            'message' => 'Tiempo de producción iniciado',
            'fecha_guardada' => $fechaInicio->toISOString() // Devolver la fecha en formato ISO 8601
        ]);
    }
    

    // Registrar piezas y calcular minutos
// Registrar piezas y calcular minutos
public function registrarPieza(Request $request)
{
    $validated = $request->validate([
        'idorden' => 'required|exists:ordenproduccion,idorden',
        'idlinea' => 'nullable|exists:linea_produccion,id',
    ]);

    $idlinea = $validated['idlinea'] ?? null;
    $idorden = $validated['idorden'];

    try {
        // 🔹 Obtener el último registro (la pieza anterior)
        $ultimoRegistro = DB::table('registros_produccion')
            ->where('idorden', $idorden)
            ->orderByDesc('registro_tiempo')
            ->first();

        // 🔹 Si hay un registro previo, sumar 1 al último valor. Si no hay, empezar en 1.
        $nuevoTotal = $ultimoRegistro ? $ultimoRegistro->piezas_realizadas + 1 : 1;

        // 🔹 Obtener el segundo registro de la orden para tomar el valor de 'inicio' (cuando el usuario presiona "inicio")
        $segundoRegistro = DB::table('registros_produccion')
            ->where('idorden', $idorden)
            ->skip(1) // Salta el primer registro (que es nulo)
            ->first();

        // 🔹 Verificamos si hay un segundo registro con el valor 'inicio'
        if ($nuevoTotal == 1 && !$segundoRegistro) {
            return response()->json([
                'message' => 'No se puede calcular el tiempo, falta el inicio de la orden',
                'piezas_realizadas' => $nuevoTotal,
                'minutos_transcurridos' => 'N/A'
            ], 400);
        }

        // 🔹 Insertar el nuevo registro de la pieza actual
        $nuevoRegistroId = DB::table('registros_produccion')->insertGetId([
            'idorden' => $idorden,
            'idlinea' => $idlinea,
            'registro_tiempo' => now(),
            'piezas_realizadas' => $nuevoTotal,
            'created_at' => now(),
        ]);

        // 🔹 Actualizar el estado de la orden a "en proceso"
        DB::table('ordenproduccion')
            ->where('idorden', $idorden)
            ->update(['status' => 'en proceso']); // Aquí cambias el estado



        $tiempoTranscurrido = null;
        $tiempoFormateado = 'N/A';

        // 🔹 Si es la primera pieza, usar el valor de 'inicio' del segundo registro
        if ($nuevoTotal == 1 && $segundoRegistro) {
            $inicioOrden = Carbon::parse($segundoRegistro->inicio); // Tomamos 'inicio' del segundo registro
            $tiempoTranscurrido = Carbon::now()->diffInSeconds($inicioOrden); // Diferencia en segundos
        } elseif ($ultimoRegistro) {
            // 🔹 Si es una pieza posterior, calcular la diferencia con el registro anterior
            $tiempoAnterior = Carbon::parse($ultimoRegistro->registro_tiempo);
            $tiempoTranscurrido = Carbon::now()->diffInSeconds($tiempoAnterior); // Diferencia en segundos
        }

        // 🔹 Si se calculó el tiempo, convertirlo a formato HH:MM:SS o MM:SS
        if ($tiempoTranscurrido !== null) {
            $horas = floor($tiempoTranscurrido / 3600);
            $minutos = floor(($tiempoTranscurrido % 3600) / 60);
            $segundos = $tiempoTranscurrido % 60;

            $tiempoFormateado = ($horas > 0)
                ? sprintf("%d:%02d:%02d", $horas, $minutos, $segundos)
                : sprintf("%02d:%02d", $minutos, $segundos);

            // 🔹 Guardar la diferencia de tiempo en la columna 'minutos'
            DB::table('registros_produccion')->where('idregistro', $nuevoRegistroId)
                ->update(['minutos' => $tiempoFormateado]);
        }

        // 🔹 Respuesta exitosa con el registro guardado
        return response()->json([
            'message' => 'Registro guardado exitosamente',
            'piezas_realizadas' => $nuevoTotal,
            'minutos_transcurridos' => $tiempoFormateado
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error al registrar la pieza',
            'error' => $e->getMessage(),
        ], 500);
    }
}









    public function obtenerTiempoPreparacion($ordenId)
{
    $orden = Orden::find($ordenId); // Suponiendo que "Orden" es tu modelo

    if (!$orden) {
        return response()->json(['error' => 'Orden no encontrada'], 404);
    }

    // Cálculo del tiempo de preparación
    // Ejemplo: supongamos que tiempo_preparacion es 5 minutos por cada orden.
    $tiempoPreparacion = 5 * $orden->cantidad; // Suponiendo que 'cantidad' es un atributo de la orden

    // Convierte el tiempo de minutos a horas y minutos
    $horas = floor($tiempoPreparacion / 60);
    $minutos = $tiempoPreparacion % 60;

    return response()->json([
        'tiempo_preparacion' => "{$horas} horas {$minutos} minutos"
    ]);
}

    
    
    

    // Detener el tiempo de producción
    public function detenerTiempo(Request $request)
    {
        $idorden = $request->input('idorden');
    
        if (!$idorden) {
            return response()->json(['message' => 'Faltan datos'], 400);
        }
    
        $ultimoRegistro = DB::table('registros_produccion')
            ->where('idorden', $idorden)
            ->whereNotNull('registro_tiempo')
            ->latest('registro_tiempo')
            ->first();
    
        if (!$ultimoRegistro) {
            return response()->json(['message' => 'No hay registros de piezas para detener'], 404);
        }
    
        DB::table('registros_produccion')
            ->where('idregistro', $ultimoRegistro->idregistro)
            ->update(['fin' => now()]);
    
        // Actualizar el estado de la orden a "finalizado"
        DB::table('ordenproduccion')
            ->where('idorden', $idorden)
            ->update(['status' => 'finalizado']);
    
        return response()->json(['message' => 'Tiempo detenido', 'fin' => now()]);
    }
    
}
