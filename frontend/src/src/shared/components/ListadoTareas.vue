<template>
    <h1>Listado de Tareas</h1>
    <div>
        <input type="text" v-model="nuevaTarea" @keyup.enter="addTarea" />
        <ul v-for="(tarea, index) in tareas" :key="index">
            <li>{{ tarea }}</li>
        </ul>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
const tareas = ref<string[]>([]);
const nuevaTarea = ref<string>('');

function addTarea() {
    if (nuevaTarea.value.trim() !== '') {
        tareas.value.push(nuevaTarea.value.trim());
        nuevaTarea.value = '';
    }
}
</script>

<style scoped></style>
