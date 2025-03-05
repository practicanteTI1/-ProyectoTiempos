<template>
  <div class="flex flex-col h-screen bg-[#0F142A]"> <!-- Fondo Magenta -->
    <!-- Header -->
    <header class="bg-[#16223F] text-white p-4 flex items-center justify-between shadow-md"> <!-- Encabezado Azul -->
      <h1 class="text-2xl font-semibold text-center mx-auto">Monitoreo de Producción</h1>
    </header>

    <div class="flex flex-1 relative"> <!-- Contenedor principal con relative -->
      <!-- Sidebar -->
      <div
        :class="{
          'w-64 p-4': sidebarOpen,
          'w-20 p-4': !sidebarOpen,  /* Ancho ajustado cuando el sidebar está cerrado */
        }"
        class="transition-all duration-300 ease-in-out bg-[#16223F] text-white min-h-screen shadow-lg flex flex-col items-center"
      > 
        <!-- Título del menú -->
        <h2 v-if="sidebarOpen" class="text-xl font-semibold mb-6 text-center">Menú</h2>
        
        <!-- Lista de enlaces -->
        <ul class="space-y-2 w-full flex flex-col items-center">
          <li v-for="(item, index) in menuItems" :key="index" class="w-full">
            <router-link
              :to="item.route"
              class="flex items-center py-3 rounded hover:bg-gray-700 text-white transition-all w-full"
              :class="{
                'justify-center px-0': !sidebarOpen, 
                'justify-start px-4': sidebarOpen, 
                'mt-4': !sidebarOpen && item.label !== 'Home' , /* Mantiene un espaciado normal para los demás íconos */
                'mt-12': !sidebarOpen && item.label === 'Home' /* Más margen solo para el ícono de Home */
              }"
            >
              <i :class="[item.icon, 'text-xl w-6 text-center']"></i>
              <span v-if="sidebarOpen" class="whitespace-nowrap ml-3">{{ item.label }}</span>
            </router-link>
          </li>
        </ul>
      </div>

      <!-- Botón hamburguesa -->
      <button
        class="absolute top-6 left-6 z-50 bg-[#16223F] text-white p-2 rounded-md shadow-md hover:bg-gray-800 transition-all sm:left-8 lg:left-8"
        @click="toggleSidebar"
      >
        <i :class="sidebarOpen ? 'fas fa-times' : 'fas fa-bars'"></i>
      </button>

      <!-- Main Content -->
      <div class="flex-1 p-6 overflow-y-auto bg-[#0F142A] ml-16 sm:ml-0"> <!-- Contenedor de contenido principal -->
        <router-view></router-view>
      </div>
    </div>

    <!-- Footer -->
    <footer class="bg-[#16223F] text-white p-4 mt-auto text-center shadow-md"> <!-- Pie de página Azul -->
      <p class="text-sm">© 2025 Monitoreo de Producción. Todos los derechos reservados.</p>
    </footer>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const sidebarOpen = ref(true);

function toggleSidebar() {
  sidebarOpen.value = !sidebarOpen.value;
}

// Definir los elementos del menú
const menuItems = ref([
  { label: "Home", route: "/home", icon: "fas fa-home" },
  { label: "Prueba", route: "/prueba", icon: "fas fa-flask" },
  { label: "Ordenes", route: "/index", icon: "fas fa-list" },
  { label: "Modelos", route: "/modelo", icon: "fas fa-cubes" },
  { label: "Líneas", route: "/linea", icon: "fas fa-stream" },
  { label: "Monitoreo", route: "/monitoreo", icon: "fas fa-chart-line" },
  { label: "Agregar Orden", route: "/agregar", icon: "fa-solid fa-file-circle-plus"},
]);
</script>

<style scoped>
/* Asegurar que no haya subrayado en los enlaces */
a {
  text-decoration: none !important;
}

/* Estilo del Sidebar */
.sidebar {
  width: 250px;
  height: 100%;
  transition: width 0.3s ease;
}

/* Mejora de iconos */
i {
  transition: transform 0.3s ease;
}

i:hover {
  transform: scale(1.2);
}

/* Estilo del menú cuando está cerrado */
.sidebar-closed {
  width: 80px; /* Asegura que los iconos sean visibles sin solaparse */
}

/* Responsividad */
@media (max-width: 1024px) {
  .sidebar {
    width: 200px;
  }

  .sidebar-closed {
    width: 60px; /* Asegura que los iconos se vean bien en pantallas pequeñas */
  }

  /* Ajuste de la posición del botón en pantallas pequeñas */
  .sm:left-8 {
    left: 20px; /* Ajustar el botón hamburguesa a la izquierda */
  }

  .lg:left-8 {
    left: 20px; /* Ajustar el botón hamburguesa a la izquierda */
  }

  /* Ajuste del contenedor principal cuando el sidebar está cerrado */
  .ml-16 {
    margin-left: 4rem; /* Asegura que el contenido no se solape con el sidebar */
  }
}
</style>
