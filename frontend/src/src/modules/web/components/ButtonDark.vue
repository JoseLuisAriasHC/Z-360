<script setup lang="ts">
import { computed } from 'vue';

type ButtonVariant = 'primary' | 'secondary' | 'outline' | 'text';
type ButtonSize = 'sm' | 'md' | 'lg' | 'xl';

interface Props {
    variant?: ButtonVariant;
    size?: ButtonSize;
    icon?: string;
    iconPosition?: 'left' | 'right';
    fullWidth?: boolean;
    disabled?: boolean;
    loading?: boolean;
    type?: 'button' | 'submit' | 'reset';
}

const props = withDefaults(defineProps<Props>(), {
    variant: 'primary',
    size: 'lg',
    iconPosition: 'left',
    fullWidth: false,
    disabled: false,
    loading: false,
    type: 'button',
});

const emit = defineEmits<{
    (e: 'click', event: MouseEvent): void;
}>();

const buttonClasses = computed(() => {
    const classes: string[] = [
        'btn-base',
        `btn-${props.variant}`,
        `btn-${props.size}`,
    ];

    if (props.fullWidth) {
        classes.push('w-full');
    }

    if (props.disabled || props.loading) {
        classes.push('btn-disabled');
    }

    return classes.join(' ');
});

const iconClasses = computed(() => {
    const size = {
        sm: 'text-sm',
        md: 'text-base',
        lg: 'text-lg',
        xl: 'text-xl',
    }[props.size];

    const position = props.iconPosition === 'left' ? 'mr-2' : 'ml-2';

    return `${size} ${position}`;
});

const handleClick = (event: MouseEvent) => {
    if (!props.disabled && !props.loading) {
        emit('click', event);
    }
};
</script>

<template>
    <button
        :type="type"
        :class="buttonClasses"
        :disabled="disabled || loading"
        @click="handleClick">
        <!-- Loading spinner -->
        <i v-if="loading" class="pi pi-spinner pi-spin" :class="iconClasses"></i>

        <!-- Icon left -->
        <i
            v-else-if="icon && iconPosition === 'left'"
            :class="[icon, iconClasses]"></i>

        <!-- Slot content -->
        <span class="btn-content">
            <slot></slot>
        </span>

        <!-- Icon right -->
        <i
            v-if="!loading && icon && iconPosition === 'right'"
            :class="[icon, iconClasses]"></i>
    </button>
</template>

<style scoped>
/* Base styles */
.btn-base {
    @apply font-rubik font-semibold rounded-lg;
    @apply transition-all duration-300;
    @apply inline-flex items-center justify-center;
    @apply cursor-pointer;
}

/* Sizes */
.btn-sm {
    @apply text-sm px-4 py-2;
}

.btn-md {
    @apply text-base px-6 py-3;
}

.btn-lg {
    @apply text-lg px-8 py-3;
}

.btn-xl {
    @apply text-xl px-8 py-4;
}

/* Variants */
.btn-primary {
    @apply bg-background-dark border border-background-dark text-background-light;
    @apply hover:bg-white hover:text-black hover:border-black;
    @apply focus:ring-gray-700;
}

.btn-secondary {
    @apply bg-background-light border border-muted-light text-gray-900;
    @apply hover:bg-gray-50 hover:border-gray-400;
    @apply focus:ring-gray-400;
}

.btn-outline {
    @apply bg-transparent border-2 border-gray-900 text-gray-900;
    @apply hover:bg-gray-900 hover:text-white;
    @apply focus:ring-gray-700;
}

.btn-text {
    @apply bg-transparent border-none text-gray-900;
    @apply hover:bg-gray-100;
    @apply focus:ring-gray-400;
}

/* Disabled state */
.btn-disabled {
    @apply opacity-50 cursor-not-allowed;
    @apply hover:bg-background-dark hover:text-background-light;
}

.btn-secondary.btn-disabled {
    @apply hover:bg-background-light hover:text-gray-900;
}

.btn-outline.btn-disabled {
    @apply hover:bg-transparent hover:text-gray-900;
}

.btn-text.btn-disabled {
    @apply hover:bg-transparent;
}

/* Content */
.btn-content {
    @apply inline-flex items-center;
}
</style>