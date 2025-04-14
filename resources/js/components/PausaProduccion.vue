<template>
  <div v-if="mostrar" class="modal">
    <div class="contenido-modal">
      <h2 class="text-lg font-bold mb-4">Motivo de la Pausa</h2>
      
      <select v-model="motivo" class="w-full p-2 border rounded-md mb-4">
        <option disabled value="">Selecciona un motivo</option>
        <option>Comida</option>
        <option>Falta de materia</option>
        <option>Paro de Maquina</option>
      </select>

      <div class="flex justify-end gap-x-4 mt-4">
  <button @click="enviar" class="bg-green-500 text-white px-4 py-2 rounded">Enviar</button>
  <button @click="cerrar" class="bg-red-500 text-white px-4 py-2 rounded">Cancelar</button>
</div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import emitter from '@/eventsBus';

const mostrar = ref(false);
const motivo = ref('');

const abrir = () => {
  mostrar.value = true;
};

const cerrar = () => {
  mostrar.value = false;
  motivo.value = ''; // Reinicia selección
};

const enviar = () => {
  if (!motivo.value) {
    alert('Por favor selecciona un motivo');
    return;
  }

  // Aquí puedes emitir un evento o hacer un fetch al backend
  console.log('Motivo seleccionado:', motivo.value);
  emitter.emit('pausa-seleccionada', motivo.value); // opcional

  cerrar();
};

onMounted(() => {
  emitter.on('abrir-modal-pausa', abrir);
});

onUnmounted(() => {
  emitter.off('abrir-modal-pausa', abrir);
});
</script>

<style scoped>
.modal {
  position: fixed;
  top: 0;
  left: 0;
  background-color: rgba(0,0,0,0.6);
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 999;
}
.contenido-modal {
  background: white;
  padding: 2rem;
  border-radius: 1rem;
  min-width: 300px;
}
</style>
