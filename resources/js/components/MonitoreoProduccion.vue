<script setup>
import { ref,computed, onMounted, watch } from "vue";
import axios from "axios";
import emitter from '@/eventsBus'; // Asegúrate que la ruta sea correcta
import PausaProduccion from '@/components/PausaProduccion.vue';

const ordenId = ref(null);
const ordenes = ref([]);
const estadoFiltro = ref("");
const ordenFinalizada = ref(false);

const registro = ref(null);
const mensaje = ref("");
const nuevasPiezas = ref(null);
const tiempoPreparacionRestante = ref(0); // Tiempo de preparación restante en minutos

const estadoPieza = ref(""); // "Atrasado", "A tiempo", "Adelantado"
const barraProgreso = ref(0); // Progreso de la barra en porcentaje

const nuevoTiempoMinutos = ref(null);
const tiempoActual = ref("");
const tiempoEstimado = ref("");
const tiempoTotal = ref("");
const tiempoPreparacion = ref('');
const productividad = ref(0);
const cargando = ref(false);

const tiempoInicio = ref(null);
const tiempoTranscurrido = ref(0);
const tiempoFormato = ref("00:00"); // Nuevo formato de tiempo
const contadorPiezas = ref(0);
let intervaloTiempo = null;
let primeraPiezaTardia = false;

const guardarTodo = () => {
  localStorage.setItem('estadoFiltro', estadoFiltro.value || '');
  localStorage.setItem('ordenId', ordenId.value || '');
  localStorage.setItem('cronometroInicio', tiempoInicio.value || '');
  localStorage.setItem('estadoTrabajo', estadoTrabajo.value || '');
  localStorage.setItem('barraProgreso', barraProgreso.value || '');
  localStorage.setItem('estadoPieza', estadoPieza.value || '');
  localStorage.setItem('piezaActual', piezaActual.value || '');
  localStorage.setItem('contadorPiezas', contadorPiezas.value); // ✅ GUARDAR CONTADOR

};


const resetearEstado = () => {
  // Reiniciar las variables del cronómetro, estado de la orden y parámetros relacionados
  ordenId.value = "";
  registro.value = null;
  tiempoInicio.value = null;
  tiempoTranscurrido.value = 0;
  tiempoFormato.value = "00:00";
  estadoTrabajo.value = "";
  estadoPieza.value = "";
  barraProgreso.value = 0;
  piezaActual.value = 1;
  contadorPiezas.value = 0;

  // Limpiar el localStorage para evitar mostrar datos anteriores
  localStorage.removeItem("estadoFiltro");
  localStorage.removeItem("ordenId");
  localStorage.removeItem("cronometroInicio");
  localStorage.removeItem("estadoTrabajo");
  localStorage.removeItem("barraProgreso");
  localStorage.removeItem("estadoPieza");
  localStorage.removeItem("piezaActual");
  localStorage.removeItem("contadorPiezas");

  mensaje.value = "Selecciona una orden.";
};



const abrirModal = () => {
  emitter.emit('abrir-modal-pausa');
};


const obtenerOrdenes = async () => {
  cargando.value = true;
  try {
    const res = await axios.get("/api/ordenes");
    ordenes.value = res.data;
  } catch (error) {
    mensaje.value = "Error al obtener las órdenes.";
  } finally {
    cargando.value = false;
  }
};

const restaurarTodo = () => {
  const filtroGuardado = localStorage.getItem('estadoFiltro');
  if (filtroGuardado) estadoFiltro.value = filtroGuardado;

  const ordenGuardada = localStorage.getItem('ordenId');
  if (ordenGuardada) {
    ordenId.value = ordenGuardada;
    buscarOrden(); // Actualiza el registro con la orden guardada
  }

  const cronometroGuardado = localStorage.getItem('cronometroInicio');
  if (cronometroGuardado) {
    tiempoInicio.value = parseInt(cronometroGuardado);
    iniciarIntervalo(); // Esta función debe reiniciar el intervalo del cronómetro
  }

  const estadoTrabajoGuardado = localStorage.getItem('estadoTrabajo');
  if (estadoTrabajoGuardado) estadoTrabajo.value = estadoTrabajoGuardado;

  const barraGuardada = localStorage.getItem('barraProgreso');
  if (barraGuardada) barraProgreso.value = parseFloat(barraGuardada);

  const estadoPiezaGuardado = localStorage.getItem('estadoPieza');
  if (estadoPiezaGuardado) estadoPieza.value = estadoPiezaGuardado;

  const piezaActualGuardada = localStorage.getItem('piezaActual');
  if (piezaActualGuardada) piezaActual.value = parseInt(piezaActualGuardada);

  const contadorGuardado = localStorage.getItem('contadorPiezas');
  if (contadorGuardado) contadorPiezas.value = parseInt(contadorGuardado);

};


const fetchData = async () => {
  try {
    const response = await axios.get(`/api/acumulado/${idOrden}`); // Asegúrate de pasar el ID de la orden
    registro.value = response.data;
  } catch (error) {
    console.error('Error al obtener los datos:', error);
  }
};



const buscarOrden = async () => {
  if (!ordenId.value) {
    mensaje.value = "Selecciona una orden.";
    registro.value = null; // Limpiar registro si no hay orden seleccionada
    return;
  }
  mensaje.value = "";
  try {
    const res = await axios.get(`/api/registros/${ordenId.value}`);
    registro.value = res.data;
    guardarTodo(); 

    // Verificar si el status es 'finalizado' antes de continuar
    if (registro.value.status === "finalizado") {
      mensaje.value = "Esta orden ya ha sido finalizada.";
      registro.value = null; // Limpiar para evitar que muestre datos antiguos
      return;
    }

    let totalMinutos = 0;
    let totalMinutosPreparacion = 0;

    if (registro.value.piezas_solicitadas && registro.value.tiempo_ensamble) {
      const [horas, minutos, segundos] = registro.value.tiempo_ensamble.split(":").map(Number);
      const tiempoEnMinutos = minutos + segundos / 60;
      totalMinutos = registro.value.piezas_solicitadas * tiempoEnMinutos;
    }

    if (registro.value.tiempo_preparacion) {
      const [horasPreparacion, minutosPreparacion, segundosPreparacion] =
        registro.value.tiempo_preparacion.split(":").map(Number);
      const tiempoPreparacionEnMinutos = minutosPreparacion + segundosPreparacion / 60;
      totalMinutosPreparacion = tiempoPreparacionEnMinutos;
    }

    const totalMinutosFinal = totalMinutos + totalMinutosPreparacion;
    const horasTotales = Math.floor(totalMinutosFinal / 60);
    const minutosTotales = totalMinutosFinal % 60;

    tiempoEstimado.value = formatearTiempo(totalMinutos);
    tiempoPreparacion.value = formatearTiempo(totalMinutosPreparacion);
    tiempoTotal.value = `${horasTotales} horas ${minutosTotales.toFixed(0)} minutos`;

  } catch (error) {
    registro.value = null;
    mensaje.value = "Orden no encontrada.";
  }
};

// Computed para filtrar órdenes que no estén finalizadas
const ordenesFiltradas = computed(() => {
  // Primero, filtramos las órdenes que no están finalizadas
  const noFinalizadas = ordenes.value.filter((orden) => orden.status !== "finalizado");

  // Si no se ha seleccionado ninguna línea (estadoFiltro vacío), retornamos todas
  if (!estadoFiltro.value) {
    return noFinalizadas;
  }

  // Si se seleccionó una línea, filtramos las órdenes que pertenezcan a esa línea
  // En tu API, la propiedad de la línea se llama "linea" y su nombre es "nombre"
  return noFinalizadas.filter((orden) => {
    return orden.linea && orden.linea.nombre === estadoFiltro.value;
  });
  
});


const iniciarTiempo = async () => {
  if (!ordenId.value) {
    mensaje.value = "Por favor selecciona una orden.";
    return;
  }

  try {
    const res = await axios.post("/api/iniciar-tiempo", {
      idorden: ordenId.value,
    });

    if (res.data.fecha_guardada) {
      tiempoInicio.value = new Date(res.data.fecha_guardada).getTime();

      // Guardar en localStorage
      localStorage.setItem('cronometroInicio', tiempoInicio.value);

      iniciarIntervalo(); // Separamos el intervalo
      mensaje.value = `Tiempo de producción iniciado.`;
    }

  } catch (error) {
    mensaje.value = error.response?.data?.message || "Error al registrar el inicio.";
  }
};

function iniciarIntervalo() {
  if (intervaloTiempo) clearInterval(intervaloTiempo);

  intervaloTiempo = setInterval(() => {
    const tiempoPasado = Math.floor((Date.now() - tiempoInicio.value) / 1000);
    const horas = Math.floor(tiempoPasado / 3600);
    const minutos = Math.floor((tiempoPasado % 3600) / 60);
    const segundos = tiempoPasado % 60;

    tiempoFormato.value = `${horas} horas ${minutos} minutos ${segundos} segundos`;

    actualizarEstadoPieza(tiempoPasado); // tu lógica
  }, 1000);
}




// Agregar estas variables
const estadoTrabajo = ref("Preparación"); // "Preparación", "A tiempo", "Atrasado", "Completado"
const piezaActual = ref(1); // Pieza que actualmente se está produciendo

// Esta función calcula el tiempo que debería transcurrir hasta la pieza N
const calcularTiempoEsperadoHasta = (numeroPieza) => {
  if (!registro.value) return 0;
  
  const tiempoPreparacionEnSegundos = convertirTiempoPreparacion(registro.value.tiempo_estimado_horas_preparacion);
  const [hEns, mEns, sEns] = registro.value.tiempo_ensamble.split(":").map(Number);
  const tiempoEnsambleEnSegundos = hEns * 3600 + mEns * 60 + sEns;
  
  return tiempoPreparacionEnSegundos + (tiempoEnsambleEnSegundos * numeroPieza);
};

// Función para actualizar el estado y la barra de progreso
const actualizarEstadoPieza = (tiempoTranscurridoTotal) => {
  if (!registro.value) return;

  // Calcular el tiempo de preparación y ensamble
  const prepTime = convertirTiempoPreparacion(registro.value.tiempo_estimado_horas_preparacion);
  const [hEns, mEns, sEns] = registro.value.tiempo_ensamble.split(":").map(Number);
  const ensambleTime = hEns * 3600 + mEns * 60 + sEns;

  // Si el tiempo transcurrido es menor al tiempo de preparación, seguimos mostrando "Preparación"
  if (tiempoTranscurridoTotal < prepTime) {
    estadoTrabajo.value = "Preparación";
    estadoPieza.value = "Preparación";
    barraProgreso.value = (tiempoTranscurridoTotal / prepTime) * 100;
    barraColor.value = "amarillo"; // Barra amarilla indicando que está en preparación
    return;
  }

  const productionElapsed = tiempoTranscurridoTotal - prepTime;
  let piezaEnProceso = Math.floor(productionElapsed / ensambleTime) + 1;
  const pieceElapsed = productionElapsed % ensambleTime;

  // Si ya se han completado todas las piezas, cambiamos el estado a "Completado"
  if (contadorPiezas.value >= registro.value.piezas_solicitadas) {
    estadoTrabajo.value = "Completado";
    estadoPieza.value = "Completado";
    barraProgreso.value = 100;
    barraColor.value = "amarillo"; // Barra amarilla cuando se ha completado todo
    return;  // Detenemos la ejecución aquí si ya se completaron todas las piezas
  }

  // Flujo de la pieza en proceso
  if (piezaEnProceso === 1) {
    // La primera pieza siempre se marca como "A tiempo"
    estadoTrabajo.value = "A tiempo";
    estadoPieza.value = `OP pieza 1 A tiempo (pieza 1 actual)`;
    guardarTodo()
    barraProgreso.value = (pieceElapsed / ensambleTime) * 100;
    barraColor.value = "verde"; // Barra verde indicando que está a tiempo
    
  } else if (piezaEnProceso === contadorPiezas.value + 1) {
    // Si la pieza en proceso es exactamente 1 más que las completadas, está "A tiempo"
    estadoTrabajo.value = "A tiempo";
    estadoPieza.value = `OP pieza ${contadorPiezas.value + 1} A tiempo (pieza ${piezaEnProceso} actual)`;
    guardarTodo()
    barraProgreso.value = (pieceElapsed / ensambleTime) * 100;
    barraColor.value = "verde"; // Barra verde indicando que está a tiempo
  } else if (piezaEnProceso > contadorPiezas.value + 1) {
    // Si la pieza en proceso es más de 1 adelante, está "Atrasado"
    estadoTrabajo.value = "Atrasado";
    estadoPieza.value = `OP pieza ${contadorPiezas.value} Atrasado (pieza ${piezaEnProceso} actual)`;
    guardarTodo()
    barraProgreso.value = (pieceElapsed / ensambleTime) * 100;
    barraColor.value = "rojo"; // Barra roja indicando que está atrasado
   
  } else {
    // Si la pieza en proceso es menor o igual al contador de piezas, se mantiene a tiempo
    estadoTrabajo.value = "A tiempo";
    estadoPieza.value = `OP pieza ${piezaEnProceso} A tiempo (pieza ${piezaEnProceso})`;
    guardarTodo()
    barraProgreso.value = (pieceElapsed / ensambleTime) * 100;
    barraColor.value = "verde"; // Barra verde indicando que está a tiempo
    
  }

  // Cuando capture la pieza atrasada, la barra vuelve a verde y se actualiza el estado
  if (estadoTrabajo.value === "Atrasado" && piezaEnProceso === contadorPiezas.value + 1) {
    estadoTrabajo.value = "A tiempo";
    estadoPieza.value = `OP pieza ${piezaEnProceso} A tiempo (pieza ${piezaEnProceso})`;
    guardarTodo()
    barraProgreso.value = (pieceElapsed / ensambleTime) * 100;
    barraColor.value = "verde"; // Barra verde cuando la pieza es registrada
   
  }
}






// Funcion para convertir el tiempo de preparacion del backend al front
const convertirTiempoPreparacion = (tiempoTexto) => {
  let totalSegundos = 0;

  // Extrae horas
  const horasMatch = tiempoTexto.match(/(\d+)\s*hora/);
  if (horasMatch) {
    totalSegundos += parseInt(horasMatch[1]) * 3600;
  }

  // Extrae minutos
  const minutosMatch = tiempoTexto.match(/(\d+)\s*minuto/);
  if (minutosMatch) {
    totalSegundos += parseInt(minutosMatch[1]) * 60;
  }

  // Extrae segundos
  const segundosMatch = tiempoTexto.match(/(\d+)\s*segundo/);
  if (segundosMatch) {
    totalSegundos += parseInt(segundosMatch[1]);
  }

  return totalSegundos;
};


const formatearTiempo = (totalMinutos) => {
  const horas = Math.floor(totalMinutos / 60);
  const minutos = totalMinutos % 60;
  return `${horas} horas ${minutos} minutos`;
};

const detenerTiempo = async () => {
  // Si la orden no está finalizada, detén el cronómetro y registra la finalización
  if (!ordenFinalizada.value) {
    if (intervaloTiempo) {
      clearInterval(intervaloTiempo);
      intervaloTiempo = null;
      tiempoTranscurrido.value = tiempoFormato.value; // Actualizamos el tiempo transcurrido

      try {
        // Notificar al backend que se finaliza la orden
        await axios.post("/api/detener-tiempo", {
          idorden: ordenId.value,
          fin: new Date().toISOString(),
        });
        mensaje.value = "Orden finalizada.";
        // Marcamos la orden como finalizada para la siguiente acción del botón "Fin"
        ordenFinalizada.value = true;
      } catch (error) {
        mensaje.value = 'Error al registrar el fin.';
      }
    }
  } else {
    // Si la orden ya está finalizada, limpiar todos los parámetros y reiniciar el estado
    resetearEstado();
    // Reiniciar el flag para que se pueda seleccionar otra orden
    ordenFinalizada.value = false;
  }
};


// 💥 Función para registrar la pieza
// Modificar la función registrarPieza
// Función para registrar una pieza
const registrarPieza = async () => {
  try {
    // Verificar si ya hemos alcanzado el número de piezas solicitadas
    if (registro.value.piezas_realizadas >= registro.value.piezas_solicitadas) {
      // Si ya no hay más piezas por registrar
      mensaje.value = "✅ Has llegado al límite de piezas. No puedes agregar más.";
      return; // Salir de la función y no hacer nada
    }

    // Realizar la petición al backend para registrar la pieza
    const response = await axios.post("/api/registrar-pieza", {
      idorden: ordenId.value,
      piezas_realizadas: 1,
    });

    if (!response.data || typeof response.data.piezas_realizadas !== "number") {
      throw new Error("Respuesta no válida del servidor");
    }

    // Actualizar el contador de piezas realizadas
    registro.value.piezas_realizadas = response.data.piezas_realizadas;
    contadorPiezas.value = response.data.piezas_realizadas;
    guardarTodo()

    // Calcular el tiempo transcurrido
    const tiempoTranscurrido = Math.floor((Date.now() - tiempoInicio.value) / 1000);
    const tiempoEsperadoParaPiezaRegistrada = calcularTiempoEsperadoHasta(contadorPiezas.value);
    const piezaRegistradaATiempo = tiempoTranscurrido <= tiempoEsperadoParaPiezaRegistrada;

    if (piezaRegistradaATiempo) {
      estadoTrabajo.value = "A tiempo";
    }

    // Actualizar estado y barra de progreso
    actualizarEstadoPieza(tiempoTranscurrido);

    // Verificar si ya hemos alcanzado todas las piezas solicitadas
    if (contadorPiezas.value >= registro.value.piezas_solicitadas) {
      estadoTrabajo.value = "Completado";
      estadoPieza.value = "Completado";
      barraProgreso.value = 100;
    }

    // Contador y estado de la pieza
    contadorPiezas.value++;
    piezaActual.value = contadorPiezas.value + 1;
    

    // Si la pieza estaba atrasada, cambiamos el estado
    if (estadoTrabajo.value === "Atrasado") {
      estadoTrabajo.value = "A tiempo";
      barraColor.value = "verde";
      estadoPieza.value = `OP pieza ${contadorPiezas.value} A tiempo (pieza ${piezaActual.value})`;
    }

    // Si ya hemos completado todas las piezas solicitadas, cambiamos el estado y el fondo
    if (estadoTrabajo.value === "Completado") {
      barraColor.value = "yellow-500"; // Para mostrar el fondo amarillo cuando se complete
    }
  } catch (error) {
    mensaje.value = `❌ Error al registrar la pieza: ${error.message}`;
  }
};



// Tiempo acumulado de ensamblaje
const tiempoAcumuladoEnsamble = computed(() => {
  if (!registro.value || !registro.value.tiempo_ensamble || registro.value.piezas_realizadas === undefined) {
    return "0 horas 0 minutos 0 segundos";
  }
  const piezas = registro.value.piezas_realizadas;
  const [h, m, s] = registro.value.tiempo_ensamble.split(":").map(Number);
  const totalSegundos = piezas * (h * 3600 + m * 60 + s);
  const horas = Math.floor(totalSegundos / 3600);
  const minutos = Math.floor((totalSegundos % 3600) / 60);
  const segundos = totalSegundos % 60;
  return `${horas} horas ${minutos} minutos ${segundos} segundos`;
});


// Tiempo acumulado de preparación (se cuenta solo para la primera pieza)
const tiempoAcumuladoPreparacion = computed(() => {
  if (!registro.value || !registro.value.tiempo_preparacion || registro.value.piezas_realizadas === undefined) {
    return "0 horas 0 minutos 0 segundos";
  }
  const piezas = registro.value.piezas_realizadas;
  const [h, m, s] = registro.value.tiempo_preparacion.split(":").map(Number);
  const totalSegundos = piezas * (h * 3600 + m * 60 + s);
  const horas = Math.floor(totalSegundos / 3600);
  const minutos = Math.floor((totalSegundos % 3600) / 60);
  const segundos = totalSegundos % 60;
  return `${horas} horas ${minutos} minutos ${segundos} segundos`;
});




// Tiempo acumulado de preparación (se cuenta solo para la primera pieza)
// Tiempo acumulado total: suma el tiempo de preparación (una vez) y el tiempo de ensamble para las piezas restantes
const tiempoAcumuladoTotal = computed(() => {
  if (
    !registro.value ||
    !registro.value.tiempo_ensamble ||
    !registro.value.tiempo_preparacion ||
    registro.value.piezas_realizadas === undefined
  ) {
    return "0 horas 0 minutos 0 segundos";
  }

  const piezas = registro.value.piezas_realizadas;
  const [hEns, mEns, sEns] = registro.value.tiempo_ensamble.split(":").map(Number);
  const [hPrep, mPrep, sPrep] = registro.value.tiempo_preparacion.split(":").map(Number);

  const tiempoEnsambleSeg = hEns * 3600 + mEns * 60 + sEns;
  const tiempoPreparacionSeg = hPrep * 3600 + mPrep * 60 + sPrep;

  // Modo B: ambos tiempos se aplican por cada pieza
  const totalSegundos = piezas * (tiempoEnsambleSeg + tiempoPreparacionSeg);

  const horas = Math.floor(totalSegundos / 3600);
  const minutos = Math.floor((totalSegundos % 3600) / 60);
  const segundos = totalSegundos % 60;

  return `${horas} horas ${minutos} minutos ${segundos} segundos`;
});



// Función para convertir una cadena de tiempo al formato "X horas Y minutos Z segundos" a segundos
const convertirATiempoSegundos = (tiempoStr) => {
  const regex = /(\d+)\s+horas\s+(\d+)\s+minutos\s+(\d+)\s+segundos/;
  const match = tiempoStr.match(regex);
  if (!match) return 0;
  const [, horas, minutos, segundos] = match.map(Number);
  return horas * 3600 + minutos * 60 + segundos;
};

// Computed property para la productividad
const Eficiencia = computed(() => {
  const acumuladoSegundos = convertirATiempoSegundos(tiempoAcumuladoTotal.value);
  const transcurridoSegundos = convertirATiempoSegundos(tiempoFormato.value);
  
  if (transcurridoSegundos === 0) return "0.00"; // Para evitar división por cero
  
  const prod = (acumuladoSegundos / transcurridoSegundos) * 100;
  return prod.toFixed(2);
});


onMounted(() => {
  restaurarTodo();
  obtenerOrdenes();
  fetchData();
});

</script>


<template>

<select id="filtroStatus" class="bg-white w-1/3 text-sm py-1 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500"
        v-model="estadoFiltro">
  <option value="">Todas las Lineas</option>
  <option value="Linea Ensamble 1">Linea Ensamble 1</option>
  <option value="Linea Ensamble 2">Linea Ensamble 2</option>
  <option value="Linea Ensamble 3">Linea Ensamble 3</option>
</select>



  <div class="min-h-screen bg-blue-to-r from-blue-200 via-purple-300 to-indigo-400 flex flex-col items-center justify-start p-6 space-y-8">
    <!-- Sección para seleccionar la orden -->
    <div class="w-full max-w-lg mb-6">
      <label class="block text-lg font-semibold text-white">Selecciona una Orden:</label>
<select
  v-model="ordenId"
  class="w-full border border-white rounded-xl p-3 mt-2 focus:ring-2 focus:ring-blue-500 bg-white text-black"
  @change="buscarOrden"
>
  <option value="" disabled selected>Selecciona una orden</option>
  <option
    v-for="orden in ordenesFiltradas"
    :key="orden.idorden"
    :value="orden.idorden"
  >
    Orden #{{ orden.idorden }}
  </option>
</select>

    </div>

    <p v-if="cargando" class="text-gray-500 text-center">Cargando órdenes...</p>
    <p v-if="mensaje" class="text-red-500 text-center">{{ mensaje }}</p>

    <!-- Detalles de la orden seleccionada -->
    <div v-if="registro" class="w-full max-w-7xl grid md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div class="p-6 border rounded-xl shadow-xl bg-gray-50">
        <p><strong class="font-semibold">Orden ID:</strong> {{ registro.idorden }}</p>
      </div>

      <div class="p-6 border rounded-xl shadow-xl bg-gray-50">
    <!-- Después: -->
<p><strong class="font-semibold">Línea de Producción:</strong> {{ registro.nombre }}</p>
      </div>

      <div class="p-6 border rounded-xl shadow-xl bg-gray-50">
        <p><strong class="font-semibold">Piezas Solicitadas:</strong> {{ registro.piezas_solicitadas }}</p>
      </div>
      <div class="p-6 border rounded-xl shadow-xl bg-gray-50">
        <p><strong class="font-semibold">Piezas Realizadas: </strong>{{ registro.piezas_realizadas }}</p>
        <p><strong class="font-semibold">Tiempo Acumulado Ensamble:</strong>  {{ tiempoAcumuladoEnsamble }}</p>
    <p><strong class="font-semibold">Tiempo Acumulado Preparación:</strong>  {{ tiempoAcumuladoPreparacion }}</p>
    <p><strong class="font-semibold">Tiempo Acumulado Total:</strong> {{ tiempoAcumuladoTotal }}</p>
      </div>
      <div class="p-6 border rounded-xl shadow-xl bg-gray-50">
        <p><strong class="font-semibold">Tiempo Ensamble:</strong> {{ registro.tiempo_ensamble }}</p>
        <p><strong class="font-semibold">Tiempo preparación:</strong> {{ registro.tiempo_preparacion }}</p>
        <p><strong class="font-semibold">Modelo:</strong> {{ registro.nombre_modelo }}</p>
      </div>
      <div class="p-6 border rounded-xl shadow-xl bg-gray-50">
        <p><strong class="font-semibold">Tiempo de emsamblaje:</strong> {{ registro.tiempo_estimado_horas_ensamble }}</p>
        <p><strong class="font-semibold">Tiempo de Preparación:</strong> {{ registro.tiempo_estimado_horas_preparacion }}</p>
          <p><strong class="font-semibold">Tiempo Total:</strong> {{ registro.valor_total }}</p>
        <!-- Muestra el tiempo estimado -->
      </div>
      <div class="p-6 border rounded-xl shadow-xl bg-gray-50">
        <p><strong class="font-semibold">Productividad:</strong> <span class="text-blue-600 font-bold"> {{ Eficiencia }}%</span></p>
      </div>
    </div>

    <!-- Cronómetro y control de tiempo -->
    <div class="p-6 border rounded-xl shadow-xl bg-gray-50">
      <p><strong class="font-semibold">Tiempo Transcurrido:</strong> <span class="text-green-600 font-bold">{{ tiempoFormato }}</span></p>
    </div>
    <div class="w-full max-w-lg bg-gray-200 h-4 rounded-lg overflow-hidden">
      <div :class="{
  'bg-blue-500': estadoTrabajo === 'Preparación', 
  'bg-red-500': estadoTrabajo === 'Atrasado', 
  'bg-green-500': estadoTrabajo === 'A tiempo', 
  'bg-yellow-500': estadoTrabajo === 'Completado'
}"
     class="h-full"
     :style="{ width: barraProgreso + '%' }"></div>


</div>
<p v-if="estadoPieza" class="mt-2 text-center text-white font-semibold">
  Estado: {{ estadoPieza }} 
  <span v-if="registro && piezaActual <= registro.piezas_solicitadas"></span>

</p>

<PausaProduccion />

    <!-- Sección para registrar piezas -->
    <div class="w-full max-w-lg">
      <!-- Botones de control -->
      <div class="mt-4 flex justify-between">
        <button @click="iniciarTiempo" class="bg-green-500 text-white p-3 rounded-xl">Inicio</button>
        <button @click="registrarPieza" class="bg-yellow-500 text-white p-3 rounded-xl">Registrar</button>
        <button @click="abrirModal" class="bg-yellow-500 text-white p-3 rounded-xl">Pausa</button>
        <button @click="detenerTiempo" class="bg-red-500 text-white p-3 rounded-xl">Fin</button>
      </div>
    </div>
  </div>
</template>
