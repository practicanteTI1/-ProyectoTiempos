<script setup>
import { ref, onMounted, computed } from "vue";
import axios from "axios";

const ordenId = ref(null);
const ordenes = ref([]);
const registro = ref(null);
const mensaje = ref("");
const nuevasPiezas = ref(null);
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
  try {
    await axios.post("/api/actualizar-piezas", {
      idorden: ordenId.value,
      piezas_realizadas: nuevasPiezas.value,
    });
    nuevasPiezas.value = null;
    await buscarOrden();
  } catch (error) {
    mensaje.value = "Error al actualizar las piezas.";
  }
};

const productividad = computed(() => {
  if (!registro.value || !registro.value.piezas_solicitadas) {
    return 0;
  }
  return ((registro.value.piezas_realizadas / registro.value.piezas_solicitadas) * 100).toFixed(2);
});

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
        <p><strong class="font-semibold">Tiempo Estimado:</strong> {{ registro.tiempo_estimado_horas }} horas</p>
      </div>
      <div class="p-6 border rounded-xl shadow-xl bg-gray-50 hover:shadow-2xl transition duration-300">
        <p><strong class="font-semibold">Productividad:</strong> <span class="text-blue-600 font-bold">{{ productividad }}%</span></p>
      </div>
    </div>

 <!-- Actualización de Piezas -->
<div class="w-full max-w-lg">
  <label class="block text-lg font-semibold text-white">Actualizar Piezas Realizadas:</label>
  <input v-model="nuevasPiezas" type="number" class="w-full border border-gray-300 rounded-xl p-3 mt-2 focus:ring-2 focus:ring-blue-500 text-white bg-gray-800" />
  <button @click="actualizarPiezas" class="mt-4 w-full bg-blue-600 text-white p-3 rounded-xl hover:bg-blue-700 transition duration-300">
    Guardar
  </button>
</div>

    
  </div>
</template>

<style scoped>
/* Estilos adicionales si es necesario */
</style>
