<script setup lang="ts">
    import { computed } from 'vue';

    interface Props {
        id: string;
        label: string;
        error?: string;
        required?: boolean;
        hint?: string;
        class?: string;
    }

    const props = withDefaults(defineProps<Props>(), {
        required: false,
    });

    const hasError = computed(() => !!props.error);
</script>

<template>
    <div :class="['form-field', props.class]">
        <label :for="id" class="block text-surface-900 dark:text-surface-0 font-medium mb-2">
            {{ label }}
        </label>

        <slot :hasError="hasError"></slot>

        <small v-if="hint && !error" class="block mt-1 text-sm text-surface-600 dark:text-surface-400">
            {{ hint }}
        </small>

        <small v-if="error" class="block mt-1 text-sm text-red-500 dark:text-red-400">
            <i class="pi pi-exclamation-circle mr-1"></i>
            {{ error }}
        </small>
    </div>
</template>

<style scoped>
    .form-field {
        margin-bottom: 1rem;
    }
</style>
