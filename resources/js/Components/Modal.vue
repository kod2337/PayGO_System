<script setup>
import { computed, onMounted, onUnmounted, ref, nextTick, watch } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    maxWidth: {
        type: String,
        default: '2xl',
    },
    closeable: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['close']);
const modalRef = ref(null);
let previousActiveElement = null;

const close = () => {
    if (!props.closeable) return;
    emit('close');
};

const closeOnEscape = (e) => {
    if (e.key === 'Escape' && props.show) {
        close();
    }
};

const maxWidthClass = computed(() => {
    return {
        sm: 'sm:max-w-sm',
        md: 'sm:max-w-md',
        lg: 'sm:max-w-lg',
        xl: 'sm:max-w-xl',
        '2xl': 'sm:max-w-2xl',
        '3xl': 'sm:max-w-3xl',
        '4xl': 'sm:max-w-4xl',
        '5xl': 'sm:max-w-5xl',
        '6xl': 'sm:max-w-6xl',
        '7xl': 'sm:max-w-7xl',
    }[props.maxWidth];
});

const focusFirstInput = () => {
    nextTick(() => {
        const focusable = modalRef.value?.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
        if (focusable) {
            focusable.focus();
        }
    });
};

const handleTabbing = (e) => {
    if (!modalRef.value || e.key !== 'Tab') return;

    const focusable = modalRef.value.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
    const firstFocusable = focusable[0];
    const lastFocusable = focusable[focusable.length - 1];

    if (e.shiftKey) {
        if (document.activeElement === firstFocusable) {
            lastFocusable.focus();
            e.preventDefault();
        }
    } else {
        if (document.activeElement === lastFocusable) {
            firstFocusable.focus();
            e.preventDefault();
        }
    }
};

watch(() => props.show, (show) => {
    if (show) {
        previousActiveElement = document.activeElement;
        document.body.style.overflow = 'hidden';
        focusFirstInput();
    } else {
        document.body.style.overflow = '';
        if (previousActiveElement) {
            previousActiveElement.focus();
        }
    }
});

onMounted(() => {
    document.addEventListener('keydown', closeOnEscape);
    document.addEventListener('keydown', handleTabbing);
});

onUnmounted(() => {
    document.removeEventListener('keydown', closeOnEscape);
    document.removeEventListener('keydown', handleTabbing);
    document.body.style.overflow = '';
});
</script>

<template>
    <Teleport to="body">
        <div v-if="show" class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50">
            <div class="fixed inset-0 transform transition-all" @click="close">
                <div class="absolute inset-0 bg-gray-500/75 dark:bg-gray-900/90"></div>
            </div>

            <div
                ref="modalRef"
                class="mb-6 bg-background rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:mx-auto"
                :class="maxWidthClass"
                role="dialog"
                aria-modal="true"
            >
                <slot />
            </div>
        </div>
    </Teleport>
</template>
