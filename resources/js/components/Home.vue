<template>
  <div class="container-fluid p-4 text-white d-flex flex-column justify-content-center align-items-center">
    <!-- Cabecera -->
    <header class="text-center mb-4">
      <h1 class="text-light fw-bold display-5">Ordenes de Producción</h1>
    </header>

    <!-- Contenido principal con enlaces -->
    <div class="menu-container">
      <div class="d-flex justify-content-center flex-wrap gap-4">
        <div class="menu-card">
          <router-link to="/agregar" class="btn-custom blue">
            <i class="fa-solid fa-file-circle-plus fa-3x"></i>
            <span>Agregar Orden</span>
          </router-link>
        </div>
        <div class="menu-card">
          <router-link to="/monitoreo" class="btn-custom purple">
            <i class="fas fa-chart-line fa-3x"></i>
            <span>Monitoreo de Producción</span>
          </router-link>
        </div>
        <div class="menu-card">
          <router-link to="/index" class="btn-custom green">
            <i class="fas fa-list fa-3x"></i>
            <span>Historial de Órdenes</span>
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "Home",
};
</script>

<style scoped>
/* Fondo general */
.container-fluid {
  min-height: 100vh;
  text-align: center;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  padding: 20px;
  font-family: 'Poppins', sans-serif;
}

/* Títulos y textos */
h1 {
  color: #ffffff;
  font-size: 2.2rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 2px;
  margin-top: -130px; /* Subí el título */
}

/* Contenedor de los enlaces */
.menu-container {
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(8px);
  border-radius: 15px;
  padding: 40px;
  box-shadow: 0 5px 25px rgba(0, 0, 0, 0.4);
  max-width: 1000px;
  text-align: center;
  display: flex;
  justify-content: center;
  align-items: center;
  flex-wrap: wrap;
  gap: 40px;
  margin-top: -50px; /* Subí el contenido de los botones */
}

/* Diseño de cada enlace */
.menu-card {
  width: 220px;
  height: 220px;
  background: white;
  border-radius: 15px;
  display: flex;
  justify-content: center;
  align-items: center;
  text-align: center;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  padding: 10px;
  box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
}

.menu-card:hover {
  transform: translateY(-8px);
  box-shadow: 0px 10px 25px rgba(0, 0, 0, 0.2);
}

/* Botones de colores */
.btn-custom {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-decoration: none;
  color: white;
  font-size: 1.2rem;
  font-weight: 600;
  padding: 20px;
  border-radius: 15px;
  width: 100%;
  height: 100%;
}

.btn-custom i {
  margin-bottom: 10px;
}

/* Colores de los botones */
.green {
  background: #34c759;
}

.blue {
  background: #007aff;
}

.purple {
  background: #9b59b6;
}

/* Hover en los botones */
.btn-custom:hover {
  transform: scale(1.05);
  filter: brightness(1.1);
}

/* Responsivo */
@media (max-width: 768px) {
  .menu-container {
    padding: 20px;
  }

  .menu-card {
    width: 180px;
    height: 180px;
  }

  h1 {
    font-size: 1.8rem;
  }
}
</style>
