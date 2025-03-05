<template>
  <div class="container mt-8 px-4">
    <h1 class="text-center text-3xl font-bold mb-6" style="color: #1D4ED8;">Agregar Nueva Orden</h1>


    <form @submit.prevent="submitFormulario" class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-lg">
      <!-- Campo Orden -->
      <div class="mb-4">
        <label for="orden" class="block text-sm font-medium text-gray-700">Orden</label>
        <input
          type="text"
          class="mt-2 w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none"
          id="orden"
          v-model="ordenForm.orden"
          required
        />
      </div>

      <!-- Línea -->
      <div class="mb-4">
        <label for="idlinea" class="block text-sm font-medium text-gray-700">Línea</label>
        <select
          class="mt-2 w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none"
          id="idlinea"
          v-model="ordenForm.idlinea"
          required
        >
          <option v-for="linea in lineas" :key="linea.id" :value="linea.id">{{ linea.nombre }}</option>
        </select>
      </div>

      <!-- Modelo -->
      <div class="mb-4">
        <label for="idmodelo" class="block text-sm font-medium text-gray-700">Modelo</label>
        <select
          class="mt-2 w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none"
          id="idmodelo"
          v-model="ordenForm.idmodelo"
          required
        >
          <option v-for="modelo in modelos" :key="modelo.idmodelo" :value="modelo.idmodelo">{{ modelo.nombre }}</option>
        </select>
      </div>

      <!-- Piezas Solicitadas -->
      <div class="mb-6">
        <label for="piezas_solicitadas" class="block text-sm font-medium text-gray-700">Piezas Solicitadas</label>
        <input
          type="number"
          class="mt-2 w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none"
          id="piezas_solicitadas"
          v-model="ordenForm.piezas_solicitadas"
          required
        />
      </div>

      <!-- Botón Enviar -->
      <div class="text-center">
        <button type="submit" class="w-full py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
          Agregar Orden
        </button>
      </div>
    </form>
  </div>
</template>

<script>
import axios from "axios";
export default {
  data() {
    return {
      ordenForm: {
        orden: '',
        idlinea: '',
        idmodelo: '',
        piezas_solicitadas: 0,
      },
      lineas: [],
      modelos: [],
    };
  },
  created() {
    this.fetchLineas();
    this.fetchModelos();
  },
  methods: {
    async fetchLineas() {
      try {
        const response = await axios.get("/api/lineas");
        this.lineas = response.data;
      } catch (error) {
        console.error("Error al obtener las líneas:", error);
      }
    },
    async fetchModelos() {
      try {
        const response = await axios.get("/api/modelos");
        this.modelos = response.data;
      } catch (error) {
        console.error("Error al obtener los modelos:", error);
      }
    },
    async submitFormulario() {
      try {
        const nuevaOrden = { ...this.ordenForm };
        await axios.post("/api/CrearOrden", nuevaOrden);

        // Mostrar el mensaje de éxito con SweetAlert2
        Swal.fire({
          title: 'Orden agregada!',
          text: 'La orden fue agregada exitosamente.',
          icon: 'success',
          confirmButtonText: 'Aceptar',
        }).then(() => {
          this.$router.push("/ordenes"); // Redirige a la lista de órdenes
          this.$router.go(0); // Refresca la página
        });
      } catch (error) {
        console.error("Error al agregar la orden:", error);
        Swal.fire({
          title: 'Error',
          text: 'Hubo un problema al agregar la orden.',
          icon: 'error',
          confirmButtonText: 'Aceptar',
        });
      }
    },
  },
};
</script>

<style scoped>
/* Estilo general: mejorar la apariencia de los campos de entrada y botones */
input, select {
  font-size: 16px;
}
button {
  font-size: 16px;
}
</style>
