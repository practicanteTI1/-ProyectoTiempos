<template>
  <div class="container mt-5">
    <h1 class="text-center mb-4 text-primary">Órdenes de Producción</h1>

    <!-- Botón para agregar una nueva orden (Redirige a la página de agregar orden) -->
    <div class="text-center mb-4">
      <router-link to="/agregar" class="btn btn-lg btn-success">
        <i class="fa-solid fa-plus"></i> Agregar Orden
      </router-link>
    </div>

    <!-- Mensaje cuando no hay órdenes -->
    <div v-if="ordenes.length === 0" class="alert alert-warning text-center" role="alert">
      <strong>No hay órdenes disponibles.</strong>
    </div>

    <!-- Tabla de órdenes -->
    <div v-if="ordenes.length > 0">
      <table class="table table-bordered table-striped">
        <thead class="table-dark">
          <tr>
            <th>ID Orden</th>
            <th>Orden</th>
            <th>Línea</th>
            <th>Modelo</th>
            <th>Piezas Solicitadas</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="orden in ordenes" :key="orden.idorden">
            <td>{{ orden.idorden }}</td>
            <td>{{ orden.orden }}</td>
            <td>{{ orden.linea.nombre }}</td>
            <td>{{ orden.modelo.nombre }}</td>
            <td>{{ orden.piezas_solicitadas }}</td>
            <td>
              <button class="btn btn-warning btn-sm me-2" @click="abrirModalEditar(orden)">
                <i class="fa-solid fa-pen"></i>
              </button>
              <button class="btn btn-danger btn-sm" @click="confirmarEliminarOrden(orden.idorden)">
                <i class="fa-solid fa-trash"></i>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal de edición de Orden -->
    <div v-if="modalVisible" class="modal fade show" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true" style="display: block;">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editModalLabel">Editar Orden</h5>
            <button type="button" class="btn-close" @click="cerrarModal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="actualizarOrden">
              <!-- ID Orden -->
              <div class="mb-3">
                <label for="idorden" class="form-label">ID Orden</label>
                <input type="text" class="form-control" id="idorden" v-model="ordenForm.idorden" readonly>
              </div>

              <!-- Campo Orden -->
              <div class="mb-3">
                <label for="orden" class="form-label">Orden</label>
                <input type="text" class="form-control" id="orden" v-model="ordenForm.orden" required>
              </div>

              <!-- Línea -->
              <div class="mb-3">
                <label for="idlinea" class="form-label">Línea</label>
                <select class="form-control" id="idlinea" v-model="ordenForm.idlinea" required>
                  <option v-for="linea in lineas" :key="linea.id" :value="linea.id">{{ linea.nombre }}</option>
                </select>
              </div>

              <!-- Modelo -->
              <div class="mb-3">
                <label for="idmodelo" class="form-label">Modelo</label>
                <select class="form-control" id="idmodelo" v-model="ordenForm.idmodelo" required>
                  <option v-for="modelo in modelos" :key="modelo.idmodelo" :value="modelo.idmodelo">{{ modelo.nombre }}</option>
                </select>
              </div>

              <!-- Piezas Solicitadas -->
              <div class="mb-3">
                <label for="piezas_solicitadas" class="form-label">Piezas Solicitadas</label>
                <input type="number" class="form-control" id="piezas_solicitadas" v-model="ordenForm.piezas_solicitadas" required>
              </div>

              <div class="text-center">
                <button type="submit" class="btn btn-primary">Actualizar Orden</button>
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
      ordenes: [],
      lineas: [],
      modelos: [],
      ordenForm: {
        idorden: null, 
        orden: '',     
        idlinea: '',
        idmodelo: '',
        piezas_solicitadas: 0,
      },
      modalVisible: false,
    };
  },
  created() {
    this.fetchOrdenes();
    this.fetchLineas();
    this.fetchModelos();
  },
  methods: {
    async fetchOrdenes() {
      try {
        const response = await axios.get("/api/ordenes");
        this.ordenes = response.data;
      } catch (error) {
        console.error("Error al obtener las órdenes:", error);
      }
    },

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

    abrirModalEditar(orden) {
      this.ordenForm = { ...orden };
      this.modalVisible = true;
    },

    cerrarModal() {
      this.modalVisible = false;
    },

    async actualizarOrden() {
      try {
        await axios.put(`/api/ordenes/${this.ordenForm.idorden}`, this.ordenForm);
        
        // SweetAlert2 para mostrar mensaje de éxito
        Swal.fire({
          title: '¡Éxito!',
          text: 'Orden actualizada correctamente.',
          icon: 'success',
          confirmButtonText: 'Aceptar'
        }).then(() => {
          this.fetchOrdenes(); // Refrescar la lista de órdenes
          this.cerrarModal(); // Cerrar el modal
        });
      } catch (error) {
        console.error("Error al actualizar la orden:", error);
        Swal.fire({
          title: '¡Error!',
          text: 'No se pudo actualizar la orden.',
          icon: 'error',
          confirmButtonText: 'Aceptar'
        });
      }
    },

    async confirmarEliminarOrden(id) {
      // Confirmación con SweetAlert2 antes de eliminar
      const result = await Swal.fire({
        title: '¿Estás seguro?',
        text: "No podrás recuperar esta orden después de eliminarla.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
      });

      if (result.isConfirmed) {
        try {
          await axios.delete(`/api/ordenes/${id}`);
          
          // SweetAlert2 para mostrar mensaje de éxito
          Swal.fire({
            title: '¡Eliminada!',
            text: 'La orden ha sido eliminada exitosamente.',
            icon: 'success',
            confirmButtonText: 'Aceptar'
          }).then(() => {
            this.fetchOrdenes(); // Refrescar la lista de órdenes
          });
        } catch (error) {
          Swal.fire({
            title: '¡Error!',
            text: 'No se pudo eliminar la orden.',
            icon: 'error',
            confirmButtonText: 'Aceptar'
          });
        }
      }
    },
  },
};
</script>

<style scoped>
/* Puedes agregar estilos personalizados si lo necesitas */

/* Mejoramos el espaciado y los márgenes */
.table {
  margin-top: 20px;
  margin-bottom: 20px;
}

.btn-lg {
  padding: 10px 20px;
  font-size: 1.1rem;
}

.text-center {
  text-align: center;
}

.mb-4 {
  margin-bottom: 1.5rem;
}

.modal-dialog {
  max-width: 800px;
  margin: 1.75rem auto;
}

.modal-content {
  border-radius: 10px;
}

.modal-header {
  background-color: #16223f;
  color: white;
}
</style>
