<script setup>
import { ref, onMounted, computed } from "vue";
import axios from "axios";

const ordenId = ref(null);
const ordenes = ref([]);
const registro = ref(null);
const mensaje = ref("");
const nuevasPiezas = ref(null);
const nuevoTiempoMinutos = ref(null); // Nuevo campo para ingresar tiempo en minutos
const tiempoActual = ref(""); // Aquí se almacenará el tiempo calculado en horas y minutos
const tiempoEstimado = ref(""); // Aquí se almacenará el tiempo estimado calculado
const productividad = ref(0); // Aquí guardaremos el porcentaje de productividad
const cargando = ref(false);

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

const buscarOrden = async () => {
  if (!ordenId.value) {
    mensaje.value = "Selecciona una orden.";
    return;
  }
  mensaje.value = "";
  try {
    const res = await axios.get(`/api/registros/${ordenId.value}`);
    registro.value = res.data;

    // Calculando el tiempo estimado
    if (registro.value.piezas_solicitadas && registro.value.minutosxequipo) {
      const totalMinutos = registro.value.piezas_solicitadas * registro.value.minutosxequipo; // Total en minutos
      const horas = Math.floor(totalMinutos / 60); // Calculando las horas
      const minutos = totalMinutos % 60; // Calculando los minutos restantes
      tiempoEstimado.value = `${horas} horas ${minutos} minutos`;
    }
  } catch (error) {
    registro.value = null;
    mensaje.value = "Orden no encontrada.";
  }
};

const actualizarPiezas = async () => {
  if (!nuevasPiezas.value || nuevasPiezas.value <= 0 || !Number.isInteger(Number(nuevasPiezas.value))) {
    mensaje.value = "Ingresa un número válido de piezas.";
    return;
  }

  if (!nuevoTiempoMinutos.value || nuevoTiempoMinutos.value <= 0) {
    mensaje.value = "Ingresa un tiempo válido en minutos.";
    return;
  }

  // Convertir el tiempo en minutos a horas y minutos
  const horas = Math.floor(nuevoTiempoMinutos.value / 60);
  const minutos = nuevoTiempoMinutos.value % 60;
  tiempoActual.value = `${horas} horas ${minutos} minutos`;

  try {
    await axios.post("/api/actualizar-piezas", {
      idorden: ordenId.value,
      piezas_realizadas: nuevasPiezas.value,
      tiempo_actual: tiempoActual.value,
    });

    // Recargar la orden para obtener datos actualizados
    await buscarOrden();

    // Validar que registro tenga valores correctos
    if (!registro.value || !registro.value.piezas_solicitadas || !registro.value.minutosxequipo) {
      mensaje.value = "Error: Datos de la orden no válidos.";
      return;
    }

    // Calcular tiempo estimado en minutos
    const tiempoEstimadoMinutos = registro.value.piezas_solicitadas * registro.value.minutosxequipo;
    const tiempoActualMinutos = nuevoTiempoMinutos.value; // Usamos el nuevo tiempo ingresado

    if (tiempoEstimadoMinutos > 0 && tiempoActualMinutos > 0) {
      if (tiempoActualMinutos < tiempoEstimadoMinutos) {
        productividad.value = ((tiempoActualMinutos / tiempoEstimadoMinutos) * 100).toFixed(2);
      } else {
        productividad.value = ((tiempoEstimadoMinutos / tiempoActualMinutos) * 100).toFixed(2);
      }
    } else {
      productividad.value = "0.00"; // Evitar NaN si hay algún error
    }

    nuevasPiezas.value = null;
    nuevoTiempoMinutos.value = null;
  } catch (error) {
    mensaje.value = "Error al actualizar las piezas.";
  }
};


// Convertir horas y minutos a minutos
const convertirAHorasYMinutos = (horas, minutos) => {
  return horas * 60 + minutos; // Devuelve el total en minutos
};

onMounted(() => {
  obtenerOrdenes();
});
</script>

<template>
  <div class="min-h-screen bg-blue-to-r from-blue-200 via-purple-300 to-indigo-400 flex flex-col items-center justify-start p-6 space-y-8">
    <!-- Contenedor para la selección de orden -->
    <div class="w-full max-w-lg mb-6">
      <label class="block text-lg font-semibold text-white">Selecciona una Orden:</label>
      <select v-model="ordenId" class="w-full border border-white rounded-xl p-3 mt-2 focus:ring-2 focus:ring-blue-500 bg-white text-white" @change="buscarOrden">
        <option value="" disabled selected>Selecciona una orden</option>
        <option v-for="orden in ordenes" :key="orden.idorden" :value="orden.idorden" class="text-black">
          Orden #{{ orden.idorden }}
        </option>
      </select>
    </div>

    <!-- Mensajes -->
    <p v-if="cargando" class="text-gray-500 text-center">Cargando órdenes...</p>
    <p v-if="mensaje" class="text-red-500 text-center">{{ mensaje }}</p>

    <!-- Información de la orden -->
    <div v-if="registro" class="w-full max-w-7xl grid md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div class="p-6 border rounded-xl shadow-xl bg-gray-50 hover:shadow-2xl transition duration-300">
        <p><strong class="font-semibold">Orden ID:</strong> {{ registro.idorden }}</p>
      </div>
      <div class="p-6 border rounded-xl shadow-xl bg-gray-50 hover:shadow-2xl transition duration-300">
        <p><strong class="font-semibold">Piezas Solicitadas:</strong> {{ registro.piezas_solicitadas }}</p>
      </div>
      <div class="p-6 border rounded-xl shadow-xl bg-gray-50 hover:shadow-2xl transition duration-300">
        <p><strong class="font-semibold">Piezas Realizadas:</strong> {{ registro.piezas_realizadas }}</p>
      </div>
      <div class="p-6 border rounded-xl shadow-xl bg-gray-50 hover:shadow-2xl transition duration-300">
        <p><strong class="font-semibold">Minutos por pieza:</strong> {{ registro.minutosxequipo }}</p>
      </div>
      <div class="p-6 border rounded-xl shadow-xl bg-gray-50 hover:shadow-2xl transition duration-300">
        <p><strong class="font-semibold">Tiempo Estimado:</strong> {{ tiempoEstimado }}</p>
      </div>

      <div class="p-6 border rounded-xl shadow-xl bg-gray-50 hover:shadow-2xl transition duration-300">
        <p><strong class="font-semibold">Productividad:</strong> <span class="text-blue-600 font-bold">{{ productividad }}%</span></p>
      </div>
    </div>

    <!-- Tiempo Actual -->
    <div class="p-6 border rounded-xl shadow-xl bg-gray-50 hover:shadow-2xl transition duration-300">
      <p><strong class="font-semibold">Tiempo Actual:</strong> <span class="text-blue-600 font-bold">{{ tiempoActual }}</span></p>
    </div>

    <!-- Actualización de Piezas y Tiempo -->
    <div class="w-full max-w-lg">
      <label class="block text-lg font-semibold text-white">Actualizar Piezas Realizadas:</label>
      <input v-model="nuevasPiezas" type="number" class="w-full border border-gray-300 rounded-xl p-3 mt-2 focus:ring-2 focus:ring-blue-500 text-white bg-gray-800" />
      <label class="block text-lg font-semibold text-white mt-4">Tiempo en Minutos:</label>
      <input v-model="nuevoTiempoMinutos" type="number" class="w-full border border-gray-300 rounded-xl p-3 mt-2 focus:ring-2 focus:ring-blue-500 text-white bg-gray-800" />
      <button @click="actualizarPiezas" class="mt-4 w-full bg-blue-600 text-white p-3 rounded-xl hover:bg-blue-700 transition duration-300">
        Guardar
      </button>
    </div>
  </div>
</template>
