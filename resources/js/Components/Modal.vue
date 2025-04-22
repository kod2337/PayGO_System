<script setup>
import { computed, onMounted, onUnmounted } from 'vue';

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

const close = (e) => {
    if (!props.closeable) return;
    
    if (e.type === 'keydown') {
        if (e.key === 'Escape') {
            e.preventDefault();
            emit('close');
        }
        return;
    }
    
    emit('close');
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

onMounted(() => {
    document.addEventListener('keydown', close);
});

onUnmounted(() => {
    document.removeEventListener('keydown', close);
});
</script>

<template>
    <Teleport to="body">
        <div v-if="show" class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50">
            <div class="fixed inset-0 transform transition-all" @click="close">
                <div class="absolute inset-0 bg-gray-500/75"></div>
            </div>

            <div
                class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:mx-auto"
                :class="maxWidthClass"
            >
                <slot />
            </div>
        </div>
    </Teleport>
</template>
