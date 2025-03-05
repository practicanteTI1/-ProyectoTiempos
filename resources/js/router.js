import { createRouter, createWebHistory } from 'vue-router';
import prueba from './components/prueba.vue';
import index from './components/ordenes/index.vue';
import Modelo from './components/Modelo.vue';
import LineaProduccion from './components/LineaProduccion.vue';
import Home from './components/Home.vue';
import MonitoreoProduccion from './components/MonitoreoProduccion.vue';
import AgregarOrden from './components/ordenes/AgregarOrden.vue';

const routes = [
 
  { path: '/prueba', component: prueba },
  { path: '/index', component: index },
  { path: '/modelo', component: Modelo },
  { path: '/linea', component: LineaProduccion },
  { path: '/home', component: Home },
  { path: '/monitoreo', component: MonitoreoProduccion },
  { path: '/agregar', component: AgregarOrden }

];

const router = createRouter({
  history: createWebHistory(),
  routes
});

export default router;
