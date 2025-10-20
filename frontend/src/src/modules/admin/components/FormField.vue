<template>
    <label :for="id">
        {{ label }}
    </label>

    <slot></slot>

    <small v-if="error" class="p-error block mt-1 text-sm text-red-500 dark:text-red-400">
        {{ error }}
    </small>
</template>

<script setup lang="ts">
    defineProps<{
        id: string;
        label: string;
        error?: string;
    }>();
</script>
