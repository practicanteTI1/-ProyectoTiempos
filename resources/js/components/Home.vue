<template>
    <div class="container mt-5">
      <!-- Cabecera -->
      <header class="text-center mb-4">
        <h1 class="text-primary">Panel de Administración</h1>
        <p class="lead">Gestiona las órdenes, líneas y modelos de producción de forma sencilla.</p>
      </header>
  
      <!-- Enlaces a las vistas de las otras secciones -->
      <div class="text-center mb-4">
        <router-link to="/index" class="btn btn-lg btn-primary me-3">
          Ordenes de Producción
        </router-link>
        <router-link to="/linea" class="btn btn-lg btn-info me-3">
          Líneas de Producción
        </router-link>
        <router-link to="/modelo" class="btn btn-lg btn-success">
          Modelos de Producción
        </router-link>
      </div>
  
      <!-- Pie de página -->
      <footer class="text-center mt-5 pt-4 pb-2">
        <p class="text-muted">© 2025 Sistema de Producción. Todos los derechos reservados.</p>
        <p class="text-muted">
          <small>Desarrollado por <strong>Tu Empresa</strong></small>
        </p>
      </footer>
    </div>
  </template>
  
  <script>
  export default {
    name: "Home",
  };
  </script>
  
  <style scoped>
  .container {
    background-color: #f8f9fa;
    border-radius: 10px;
    padding: 30px;
  }
  
  h1 {
    color: #007bff;
    font-weight: bold;
  }
  
  .lead {
    font-size: 1.2rem;
    color: #6c757d;
  }
  
  .text-center {
    text-align: center;
  }
  
  .btn-lg {
    padding: 1rem 2rem;
    font-size: 1.25rem;
    transition: background-color 0.3s ease;
  }
  
  .btn-lg:hover {
    opacity: 0.9;
  }
  
  footer {
    background-color: #f1f1f1;
    border-top: 1px solid #ddd;
  }
  
  footer p {
    margin: 0;
  }
  
  footer small {
    font-size: 0.9rem;
  }
  </style>
  