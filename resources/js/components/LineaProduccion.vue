<template>
  <div class="container mt-5">
    <h1 class="text-center mb-4 text-primary">Líneas de Producción</h1>

    <!-- Mensaje cuando no hay líneas -->
    <div v-if="lineas.length === 0" class="alert alert-warning text-center" role="alert">
      <strong>No hay líneas de producción disponibles.</strong>
    </div>

    <!-- Botón para agregar una nueva línea (ahora arriba de la tabla) -->
    <div class="text-center mb-4">
      <button class="btn btn-lg btn-success" @click="abrirModalAgregar">
        <i class="fa-solid fa-plus"></i> Agregar Nueva Línea
      </button>
    </div>

    <!-- Tabla de líneas de producción -->
    <div v-if="lineas.length > 0" class="table-responsive">
      <table class="table table-auto table-sm table-hover table-bordered table-striped">
        <thead class="table-dark">
          <tr>
            <th>ID Línea</th>
            <th>Nombre</th>
            <th class="text-nowrap" style="width: 150px;">Acciones</th> <!-- Ajusta el ancho aquí -->
          </tr>
        </thead>
        <tbody>
          <tr v-for="linea in lineas" :key="linea.id">
            <td>{{ linea.id }}</td>
            <td>{{ linea.nombre }}</td>
            <td class="text-nowrap">
              <button class="btn btn-warning btn-sm me-2" @click="abrirModalEditar(linea)">
                <i class="fa-solid fa-pen"></i> 
              </button>
              <button class="btn btn-danger btn-sm" @click="eliminarLinea(linea.id)">
                <i class="fa-solid fa-trash"></i> 
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal de Agregar/Editar Línea -->
    <div v-if="modalVisible" class="modal fade show" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: block;">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">
              {{ editando ? "Editar Línea" : "Agregar Nueva Línea" }}
            </h5>
            <button type="button" class="btn-close" @click="cerrarModal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="editando ? actualizarLinea() : submitFormulario()">
              <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" v-model="lineaForm.nombre" required>
              </div>

              <div class="text-center">
                <button type="submit" class="btn btn-primary">
                  {{ editando ? "Actualizar Línea" : "Agregar Línea" }}
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
      lineas: [], // Lista de líneas de producción
      lineaForm: {
        id: null,
        nombre: "",
      },
      modalVisible: false, // Controla la visibilidad del modal
      editando: false, // Controla si estamos en modo de edición o creación
    };
  },
  created() {
    this.fetchLineas();
  },
  methods: {
    // Obtener todas las líneas de producción
    async fetchLineas() {
      try {
        const response = await axios.get("api/lineas");
        this.lineas = response.data;
      } catch (error) {
        console.error("Error al obtener las líneas de producción:", error);
        Swal.fire({
          title: '¡Error!',
          text: 'Hubo un problema al obtener las líneas de producción.',
          icon: 'error',
          confirmButtonText: 'Aceptar',
        });
      }
    },

    // Abrir el modal para agregar una nueva línea
    abrirModalAgregar() {
      this.lineaForm = { id: null, nombre: "" };
      this.editando = false;
      this.modalVisible = true;
    },

    // Abrir el modal para editar una línea de producción
    abrirModalEditar(linea) {
      this.lineaForm = { ...linea }; // Cargar los datos de la línea en el formulario
      this.editando = true;
      this.modalVisible = true;
    },

    // Cerrar el modal
    cerrarModal() {
      this.modalVisible = false;
    },

    // Agregar una nueva línea de producción
    async submitFormulario() {
      try {
        await axios.post("api/CrearLinea", this.lineaForm);
        Swal.fire({
          title: '¡Éxito!',
          text: 'Línea de producción agregada correctamente.',
          icon: 'success',
          confirmButtonText: 'Aceptar',
        });
        this.fetchLineas();
        this.cerrarModal();
      } catch (error) {
        console.error("Error al agregar la línea de producción:", error);
        Swal.fire({
          title: '¡Error!',
          text: 'Hubo un error al agregar la línea.',
          icon: 'error',
          confirmButtonText: 'Aceptar',
        });
      }
    },

    // Actualizar una línea de producción existente
    async actualizarLinea() {
      try {
        await axios.put(`api/lineas/${this.lineaForm.id}`, this.lineaForm);
        Swal.fire({
          title: '¡Éxito!',
          text: 'Línea de producción actualizada correctamente.',
          icon: 'success',
          confirmButtonText: 'Aceptar',
        });
        this.fetchLineas();
        this.cerrarModal();
      } catch (error) {
        console.error("Error al actualizar la línea de producción:", error);
        Swal.fire({
          title: '¡Error!',
          text: 'No se pudo actualizar la línea.',
          icon: 'error',
          confirmButtonText: 'Aceptar',
        });
      }
    },

    // Eliminar una línea de producción
    async eliminarLinea(id) {
      const result = await Swal.fire({
        title: '¿Estás seguro?',
        text: "No podrás recuperar esta línea después de eliminarla.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
      });

      if (result.isConfirmed) {
        try {
          await axios.delete(`api/lineas/${id}`);
          Swal.fire({
            title: '¡Eliminada!',
            text: 'Línea de producción eliminada correctamente.',
            icon: 'success',
            confirmButtonText: 'Aceptar',
          });
          this.fetchLineas();
        } catch (error) {
          console.error("Error al eliminar la línea de producción:", error);
          Swal.fire({
            title: '¡Error!',
            text: 'No se pudo eliminar la línea.',
            icon: 'error',
            confirmButtonText: 'Aceptar',
          });
        }
      }
    },
  },
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
