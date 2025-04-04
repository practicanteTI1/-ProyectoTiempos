<template>
  <div class="container mt-5">
    <h1 class="text-center mb-4 text-primary">Modelos de Producción</h1>

    <!-- Botón para agregar un nuevo modelo -->
    <div class="text-center mb-4">
      <button class="btn btn-lg btn-success" @click="abrirModalAgregar">
        <i class="fa-solid fa-plus"></i> Agregar
      </button>
    </div>

    <!-- Mensaje cuando no hay modelos -->
    <div v-if="modelos.length === 0" class="alert alert-warning text-center" role="alert">
      <strong>No hay modelos disponibles.</strong>
    </div>

    <!-- Tabla de modelos -->
    <div v-if="modelos.length > 0" class="table-responsive">
      <table class="table table-hover table-bordered table-striped">
        <thead class="table-dark">
          <tr>
            <th>ID Modelo</th>
            <th>Nombre</th>
            <th>Tiempo de Ensamble</th>
            <th>Tiempo de Preparación</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="modelo in modelos" :key="modelo.idmodelo">
            <td>{{ modelo.idmodelo }}</td>
            <td>{{ modelo.nombre }}</td>
            <td>{{ modelo.tiempo_ensamble }}</td>
            <td>{{ modelo.tiempo_preparacion }}</td>
            <td>
              <button class="btn btn-warning btn-sm me-2" @click="abrirModalEditar(modelo)">
                <i class="fa-solid fa-pen"></i> Editar
              </button>
              <button class="btn btn-danger btn-sm" @click="eliminarModelo(modelo.idmodelo)">
                <i class="fa-solid fa-trash"></i> Eliminar
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal de Agregar/Editar Modelo -->
    <div v-if="modalVisible" class="modal fade show" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: block;">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">
              {{ editando ? "Editar Modelo" : "Agregar Nuevo Modelo" }}
            </h5>
            <button type="button" class="btn-close" @click="cerrarModal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="editando ? actualizarModelo() : submitFormulario()">
              <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" v-model="modeloForm.nombre" required>
              </div>

              <div class="mb-3">
  <label for="tiempo" class="form-label">Tiempo de Ensamble (MM:SS)</label>
  <input
    type="text"
    class="form-control"
    id="tiempo"
    v-model="modeloForm.tiempo_ensamble"
    placeholder="Ej: 02:30"
    required
  >
</div>

<div class="mb-3">
  <label for="tiempo" class="form-label">Tiempo de Preparación (MM:SS)</label>
  <input
    type="text"
    class="form-control"
    id="tiempo"
    v-model="modeloForm.tiempo_preparacion"
    placeholder="Ej: 02:30"
    required
  >
</div>



              <div class="text-center">
                <button type="submit" class="btn btn-primary">
                  {{ editando ? "Actualizar Modelo" : "Agregar Modelo" }}
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
import axios from 'axios';

export default {
  data() {
    return {
      modelos: [],
      modeloForm: {
        idmodelo: null,
        nombre: '',
        minutosxequipo: 0
      },
      modalVisible: false,
      editando: false // Controla si el modal está en modo edición o creación
    };
  },
  created() {
    this.fetchModelos();
  },
  methods: {

    validarFormatoTiempo(time) {
    const regex = /^([0-5]?[0-9]):([0-5]?[0-9])$/;  // Acepta el formato MM:SS
    return regex.test(time);
  },
    // Obtener todos los modelos
    async fetchModelos() {
      try {
        const response = await axios.get('api/modelos');
        this.modelos = response.data;
      } catch (error) {
        console.error('Error al obtener los modelos:', error);
      }
    },

    // Abrir el modal para agregar un nuevo modelo
    abrirModalAgregar() {
      this.modeloForm = { idmodelo: null, nombre: '', minutosxequipo: 0 };
      this.editando = false;
      this.modalVisible = true;
    },

    // Abrir el modal para editar un modelo existente
    abrirModalEditar(modelo) {
      this.modeloForm = { ...modelo }; // Cargar los datos en el formulario
      this.editando = true;
      this.modalVisible = true;
    },

    // Cerrar el modal
    cerrarModal() {
      this.modalVisible = false;
    },

    // Agregar un nuevo modelo
    async submitFormulario() {
      try {
        await axios.post('api/CrearModelo', this.modeloForm);
        Swal.fire('Éxito', 'Modelo agregado correctamente', 'success');  // SweetAlert2
        this.fetchModelos();
        this.cerrarModal();
      } catch (error) {
        console.error('Error al agregar el modelo:', error);
        Swal.fire('Error', 'Hubo un error al agregar el modelo', 'error');  // SweetAlert2
      }
    },

    // Actualizar un modelo existente
    async actualizarModelo() {
      try {
        await axios.put(`api/modelos/${this.modeloForm.idmodelo}`, this.modeloForm);
        Swal.fire('Éxito', 'Modelo actualizado correctamente', 'success');  // SweetAlert2
        this.fetchModelos();
        this.cerrarModal();
      } catch (error) {
        console.error('Error al actualizar el modelo:', error);
        Swal.fire('Error', 'No se pudo actualizar el modelo', 'error');  // SweetAlert2
      }
    },

    // Eliminar un modelo
    async eliminarModelo(id) {
      const result = await Swal.fire({
        title: '¿Estás seguro?',
        text: 'Este modelo será eliminado permanentemente.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
      });

      if (result.isConfirmed) {
        try {
          await axios.delete(`api/modelos/${id}`);
          Swal.fire('Eliminado', 'Modelo eliminado exitosamente', 'success');  // SweetAlert2
          this.fetchModelos();
        } catch (error) {
          console.error('Error al eliminar el modelo:', error);
          Swal.fire('Error', 'No se pudo eliminar el modelo', 'error');  // SweetAlert2
        }
      }
    }
  }
};
</script>

<style scoped>
.container {
  background-color: #f8f9fa;
  border-radius: 10px;
  padding: 20px;
}

h1 {
  color: #007bff;
  font-weight: 600;
}

.table {
  background-color: #ffffff;
}

.table-hover tbody tr:hover {
  background-color: #e9ecef;
}

.table-dark th {
  background-color: #343a40;
  color: white;
}

.btn-warning {
  background-color: #ffc107;
  border-color: #ffc107;
}

.btn-danger {
  background-color: #dc3545;
  border-color: #dc3545;
}

.btn-lg {
  padding: 0.5rem 2rem;
  font-size: 1.25rem;
}

.text-center {
  text-align: center;
}

.modal-backdrop {
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 1040;
}

.modal {
  display: block;
  z-index: 1050;
}
</style>
