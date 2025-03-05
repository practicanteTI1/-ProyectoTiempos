<template>
    <div>
      <h1>Hola, este es un componente de prueba</h1>
      <button
        class="lg:hidden flex flex-col justify-center items-center space-y-1"
        @click="toggleSidebar"
      >
        <span class="block w-6 h-1 bg-white"></span> <!-- Barra superior -->
        <span class="block w-6 h-1 bg-white"></span> <!-- Barra del medio -->
        <span class="block w-6 h-1 bg-white"></span> <!-- Barra inferior -->
      </button>
    </div>
  </template>
  
  <script>
  export default {
    name: 'Prueba'
  };
  </script>
  
  <style scoped>
  div {
    text-align: center;
    font-family: Arial, sans-serif;
  }
  </style>
  