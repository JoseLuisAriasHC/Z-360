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
        <FloatLabel>
            <label :for="label">{{label}}</label>
            <slot :hasError="hasError"></slot>
        </FloatLabel>

        <small v-if="hint && !error" class="block mt-1 ml-2 text-base text-surface-600 dark:text-surface-400">
            {{ hint }}
        </small>

        <small v-if="error" class="block mt-1 ml-2 text-base text-red-500 dark:text-red-400">
            <i class="pi pi-exclamation-circle mr-1"></i>
            {{ error }}
        </small>
    </div>
</template>

<style scoped>
    .form-field {
        margin-bottom: 1rem;
    }
    .p-floatlabel label {
        font-size: 1.3rem;
    }

    .p-floatlabel:has(input:focus) label,
    .p-floatlabel:has(input.p-filled) label,
    .p-floatlabel:has(input:-webkit-autofill) label,
    .p-floatlabel:has(textarea:focus) label,
    .p-floatlabel:has(textarea.p-filled) label,
    .p-floatlabel:has(.p-inputwrapper-focus) label,
    .p-floatlabel:has(.p-inputwrapper-filled) label,
    .p-floatlabel:has(input[placeholder]) label,
    .p-floatlabel:has(textarea[placeholder]) label {
        font-size: 1.2rem;
    }

</style>
