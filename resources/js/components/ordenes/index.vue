<template>
  <div class="container mt-5">
    <h1 class="text-center mb-4 text-primary">Órdenes de Producción</h1>

    
    <select id="filtroStatus" class="bg-white w-1/3 text-sm py-1 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" v-model="estadoFiltro" @change="filtrarPorEstado">
      <option value="">Todos</option>
      <option value="orden creada">Orden Creada</option>
      <option value="iniciado">Iniciado</option>
      <option value="en proceso">En Proceso</option>
      <option value="finalizado">Finalizado</option>
    </select>


    <!-- Botón para agregar una nueva orden -->
    <div class="text-center mb-4">
      <router-link to="/agregar" class="btn btn-lg btn-success">
        <i class="fa-solid fa-plus"></i> Agregar Orden
      </router-link>
    </div>

    <!-- Mensaje si no hay órdenes -->
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
            <th>Status</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
  <tr v-for="orden in paginacionOrdenes" :key="orden.idorden">
    <td>{{ orden.idorden }}</td>
    <td>{{ orden.orden }}</td>
    <td>{{ orden.linea.nombre }}</td>
    <td>{{ orden.modelo.nombre }}</td>
    <td>{{ orden.piezas_solicitadas }}</td>
    <td>{{ orden.status }}</td>
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

      <!-- Paginación -->
      <nav>
        <ul class="pagination justify-content-center">
          <!-- Ir a la primera página -->
          <li class="page-item" :class="{ disabled: paginaActual === 1 }">
            <button class="page-link" @click="cambiarPagina(1)">
              <i class="fa-solid fa-angles-left"></i> 
            </button>
          </li>

          <!-- Flecha Anterior -->
          <li class="page-item" :class="{ disabled: paginaActual <= 1 }">
            <button class="page-link" @click="cambiarPagina(paginaActual - 1)">
              Anterior
            </button>
          </li>

          <!-- Páginas visibles (rango de 10 páginas) -->
          <li v-for="num in paginasVisibles" :key="num" class="page-item" :class="{ active: paginaActual === num }">
            <button class="page-link" @click="cambiarPagina(num)">{{ num }}</button>
          </li>

          <!-- Flecha Siguiente -->
          <li class="page-item" :class="{ disabled: paginaActual >= totalPaginas }">
            <button class="page-link" @click="cambiarPagina(paginaActual + 1)">
              Siguiente
            </button>
          </li>

          <!-- Ir a la última página -->
          <li class="page-item" :class="{ disabled: paginaActual === totalPaginas }">
            <button class="page-link" @click="cambiarPagina(totalPaginas)">
               <i class="fa-solid fa-angles-right"></i>
            </button>
          </li>
        </ul>
      </nav>
    </div>
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
      paginaActual: 1,
      elementosPorPagina: 10, // Muestra 10 órdenes por página
      rangoPaginas: 10, // Muestra 10 páginas visibles a la vez
      ordenForm: {
        idorden: null,
        orden: "",
        idlinea: "",
        idmodelo: "",
        piezas_solicitadas: 0,
      },
      modalVisible: false,
      estadoFiltro: "", // Filtro por estado
    };
  },
  created() {
    this.fetchOrdenes();
    this.fetchLineas();
    this.fetchModelos();
  },
  computed: {
  // Filtra las órdenes según el estado seleccionado
  ordenesFiltradas() {
    if (this.estadoFiltro) {
      return this.ordenes.filter(orden => orden.status === this.estadoFiltro);
    }
    return this.ordenes;
  },
  // Total de páginas basado en las órdenes filtradas
  totalPaginas() {
    return Math.ceil(this.ordenesFiltradas.length / this.elementosPorPagina);
  },
  // Las órdenes que se deben mostrar en la página actual, usando las órdenes filtradas
  paginacionOrdenes() {
    const inicio = (this.paginaActual - 1) * this.elementosPorPagina;
    const fin = inicio + this.elementosPorPagina;
    return this.ordenesFiltradas.slice(inicio, fin); // Se filtran las órdenes
  },
  // Generar las páginas visibles
  paginasVisibles() {
    const inicio = Math.floor((this.paginaActual - 1) / this.rangoPaginas) * this.rangoPaginas + 1;
    const fin = Math.min(inicio + this.rangoPaginas - 1, this.totalPaginas);
    const paginas = [];
    for (let i = inicio; i <= fin; i++) {
      paginas.push(i);
    }
    return paginas;
  },
},

  methods: {

    filtrarPorEstado() {
    this.paginaActual = 1; // Volver a la primera página al cambiar el filtro
  },

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

    cambiarPagina(num) {
      if (num > 0 && num <= this.totalPaginas) {
        this.paginaActual = num;
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
          title: "¡Éxito!",
          text: "Orden actualizada correctamente.",
          icon: "success",
          confirmButtonText: "Aceptar",
        }).then(() => {
          this.fetchOrdenes(); // Refrescar la lista de órdenes
          this.cerrarModal(); // Cerrar el modal
        });
      } catch (error) {
        console.error("Error al actualizar la orden:", error);
        Swal.fire({
          title: "¡Error!",
          text: "No se pudo actualizar la orden.",
          icon: "error",
          confirmButtonText: "Aceptar",
        });
      }
    },

    async confirmarEliminarOrden(id) {
      // Confirmación con SweetAlert2 antes de eliminar
      const result = await Swal.fire({
        title: "¿Estás seguro?",
        text: "No podrás recuperar esta orden después de eliminarla.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
      });

      if (result.isConfirmed) {
        try {
          await axios.delete(`/api/ordenes/${id}`);

          // SweetAlert2 para mostrar mensaje de éxito
          Swal.fire({
            title: "¡Eliminada!",
            text: "La orden ha sido eliminada exitosamente.",
            icon: "success",
            confirmButtonText: "Aceptar",
          }).then(() => {
            this.fetchOrdenes(); // Refrescar la lista de órdenes
          });
        } catch (error) {
          Swal.fire({
            title: "¡Error!",
            text: "No se pudo eliminar la orden.",
            icon: "error",
            confirmButtonText: "Aceptar",
          });
        }
      }
    },
  },
};
</script>

<style scoped>
/* Estilos generales */
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

.pagination {
  margin-top: 20px;
}

.page-item.active .page-link {
  background-color: #007bff;
  color: white;
  border-color: #007bff;
}

.page-link {
  cursor: pointer;
}

/* Estilo del modal */
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
