<template>
    <div class="container mt-5">
      <h1 class="text-center mb-4 text-primary">Órdenes de Producción</h1>
  
      <!-- Mensaje cuando no hay órdenes -->
      <div v-if="ordenes.length === 0" class="alert alert-warning text-center" role="alert">
        <strong>No hay órdenes de producción disponibles.</strong>
      </div>
  
      <!-- Tabla de órdenes de producción -->
      <div v-if="ordenes.length > 0">
        <table class="table table-hover table-bordered table-striped">
          <thead class="table-dark">
            <tr>
              <th>ID Orden</th>
              <th>Fecha</th>
              <th>Línea</th>
              <th>Modelo</th>
              <th>Piezas Solicitadas</th>
              <th>minutos por pieza</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="orden in ordenes" :key="orden.idorden">
              <td>{{ orden.idorden }}</td>
              <td>{{ orden.created_at }}</td>
              <td>{{ orden.linea.nombre }}</td>
              <td>{{ orden.modelo.nombre }}</td>
              <td>{{ orden.piezas_solicitadas }}</td>
              <td>{{ orden.minutosxpieza }}</td>
              <td>
                <button class="btn btn-warning btn-sm me-2" @click="abrirModalEditar(orden)">
                  <i class="fa-solid fa-pen"></i> Editar
                </button>
                <button class="btn btn-danger btn-sm" @click="eliminarOrden(orden.idorden)">
                  <i class="fa-solid fa-trash"></i> Eliminar
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
  
      <!-- Botón para agregar una nueva orden -->
      <div class="text-center mt-4">
        <button class="btn btn-lg btn-success" @click="abrirModalAgregar">
          <i class="fa-solid fa-plus"></i> Agregar Nueva Orden
        </button>
      </div>
  
      <!-- Modal de Agregar/Editar Orden -->
      <div v-if="modalVisible" class="modal fade show" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: block;">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">
                {{ editando ? "Editar Orden" : "Agregar Nueva Orden" }}
              </h5>
              <button type="button" class="btn-close" @click="cerrarModal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form @submit.prevent="editando ? actualizarOrden() : submitFormulario()">
                <div class="mb-3">
                  <label for="linea_id" class="form-label">Línea de Producción</label>
                  <select id="linea_id" class="form-control" v-model="ordenForm.linea_id" required>
                    <option value="">Seleccione una línea</option>
                    <option v-for="linea in lineas" :key="linea.id" :value="linea.id">
                      {{ linea.nombre }}
                    </option>
                  </select>
                </div>
  
                <div class="mb-3">
                  <label for="modelo_id" class="form-label">Modelo</label>
                  <select id="modelo_id" class="form-control" v-model="ordenForm.modelo_id" required>
                    <option value="">Seleccione un modelo</option>
                    <option v-for="modelo in modelos" :key="modelo.id" :value="modelo.id">
                      {{ modelo.nombre }}
                    </option>
                  </select>
                </div>
  
                <div class="mb-3">
                  <label for="piezas_solicitadas" class="form-label">Piezas Solicitadas</label>
                  <input type="number" class="form-control" id="piezas_solicitadas" v-model="ordenForm.piezas_solicitadas" required>
                </div>

                <div class="mb-3">
                  <label for="minutosxpieza" class="form-label">minutosxpieza</label>
                  <input type="number" class="form-control" id="minutosxpieza" v-model="ordenForm.minutosxpieza" required>
                </div>
  
                <div class="text-center">
                  <button type="submit" class="btn btn-primary">
                    {{ editando ? "Actualizar Orden" : "Agregar Orden" }}
                  </button>
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button class="btn btn-secondary" @click="cerrarModal">Cerrar</button>
            </div>
          </div>
        </div>
      </div>
  
      <!-- Fondo del modal -->
      <div v-if="modalVisible" class="modal-backdrop fade show"></div>
    </div>
  </template>
  
  <script>
  import axios from "axios";
  
  export default {
    data() {
      return {
        ordenes: [], // Lista de órdenes de producción
        lineas: [], // Lista de líneas de producción
        modelos: [], // Lista de modelos de producción
        ordenForm: {
          idorden: null,
          linea_id: null,
          modelo_id: null,
          piezas_solicitadas: null, // Agregado: campo piezas solicitadas
          created_at: new Date().toISOString().slice(0, 19).replace('T', ' '), // Fecha de creación, manejado automáticamente
        },
        modalVisible: false, // Controla la visibilidad del modal
        editando: false, // Controla si estamos en modo de edición o creación
      };
    },
    created() {
      this.fetchOrdenes();
      this.fetchLineas();
      this.fetchModelos(); // Obtener los modelos de producción
    },
    methods: {
      // Obtener todas las órdenes de producción
      async fetchOrdenes() {
        try {
          const response = await axios.get("api/ordenes");
          this.ordenes = response.data;
        } catch (error) {
          console.error("Error al obtener las órdenes de producción:", error);
        }
      },
  
      // Obtener todas las líneas de producción
      async fetchLineas() {
        try {
          const response = await axios.get("api/lineas");
          this.lineas = response.data;
        } catch (error) {
          console.error("Error al obtener las líneas de producción:", error);
        }
      },
  
      // Obtener todos los modelos de producción
      async fetchModelos() {
        try {
          const response = await axios.get("api/modelos");
          this.modelos = response.data;
        } catch (error) {
          console.error("Error al obtener los modelos de producción:", error);
        }
      },
  
      // Abrir el modal para agregar una nueva orden
      abrirModalAgregar() {
        this.ordenForm = { idorden: null, linea_id: null, modelo_id: null, piezas_solicitadas: null, created_at: new Date().toISOString().slice(0, 19).replace('T', ' ') };
        this.editando = false;
        this.modalVisible = true;
      },
  
      // Abrir el modal para editar una orden de producción
      abrirModalEditar(orden) {
        this.ordenForm = { ...orden }; // Cargar los datos de la orden en el formulario
        this.editando = true;
        this.modalVisible = true;
      },
  
      // Cerrar el modal
      cerrarModal() {
        this.modalVisible = false;
      },
  
      // Agregar una nueva orden de producción
      async submitFormulario() {
        try {
          await axios.post("api/CrearOrden", this.ordenForm);
          alert("Orden de producción agregada correctamente");
          this.fetchOrdenes();
          this.cerrarModal();
        } catch (error) {
          console.error("Error al agregar la orden de producción:", error);
          alert("Hubo un error al agregar la orden");
        }
      },
  
      // Actualizar una orden de producción existente
      async actualizarOrden() {
        try {
          await axios.put(`api/ordenes/${this.ordenForm.idorden}`, this.ordenForm);
          alert("Orden de producción actualizada correctamente");
          this.fetchOrdenes();
          this.cerrarModal();
        } catch (error) {
          console.error("Error al actualizar la orden de producción:", error);
          alert("No se pudo actualizar la orden");
        }
      },
  
      // Eliminar una orden de producción
      async eliminarOrden(idorden) {
        if (confirm("¿Estás seguro de que deseas eliminar esta orden de producción?")) {
          try {
            await axios.delete(`api/ordenes/${idorden}`);
            this.fetchOrdenes();
            alert("Orden de producción eliminada correctamente");
          } catch (error) {
            console.error("Error al eliminar la orden de producción:", error);
            alert("No se pudo eliminar la orden");
          }
        }
      },
    },
  };
  </script>
  
  <style scoped>
  /* Tu estilo para el componente */
  </style>
  