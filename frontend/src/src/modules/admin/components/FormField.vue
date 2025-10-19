<template>
    <div class="mb-8">
        <label :for="id" class="block text-surface-900 dark:text-surface-0 text-xl font-medium mb-2">
            {{ label }}
        </label>

        <slot></slot>

        <small v-if="error" class="p-error block mt-1 text-sm text-red-500 dark:text-red-400">
            {{ error }}
        </small>
    </div>
</template>

<script setup lang="ts">
    defineProps<{
        id: string;
        label: string;
        error?: string;
    }>();
</script>
