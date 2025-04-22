<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';

const props = defineProps({
    type: {
        type: String,
        default: 'success',
        validator: (value) => ['success', 'error', 'info', 'warning'].includes(value)
    },
    message: {
        type: String,
        required: true
    },
    duration: {
        type: Number,
        default: 3000
    }
});

const emit = defineEmits(['close']);
const isVisible = ref(true);
let timeout = null;

onMounted(() => {
    if (props.duration > 0) {
        timeout = setTimeout(() => {
            isVisible.value = false;
            emit('close');
        }, props.duration);
    }
});

onBeforeUnmount(() => {
    if (timeout) {
        clearTimeout(timeout);
    }
});

const getTypeClasses = () => {
    switch (props.type) {
        case 'success':
            return 'bg-green-50 border-green-200 text-green-700';
        case 'error':
            return 'bg-red-50 border-red-200 text-red-700';
        case 'info':
            return 'bg-blue-50 border-blue-200 text-blue-700';
        case 'warning':
            return 'bg-yellow-50 border-yellow-200 text-yellow-700';
        default:
            return 'bg-gray-50 border-gray-200 text-gray-700';
    }
};

const getIconPath = () => {
    switch (props.type) {
        case 'success':
            return 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z';
        case 'error':
            return 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
        case 'info':
            return 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
        case 'warning':
            return 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z';
    }
};
</script>

<template>
    <Transition
        enter-active-class="transform ease-out duration-300 transition"
        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-active-class="transition ease-in duration-100"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-show="isVisible"
             class="fixed bottom-4 right-4 max-w-sm w-full shadow-lg rounded-lg pointer-events-auto overflow-hidden z-50"
        >
            <div :class="['border px-4 py-3 rounded-lg', getTypeClasses()]">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getIconPath()" />
                        </svg>
                    </div>
                    <div class="ml-3 flex-1 pt-0.5">
                        <p class="text-sm font-medium">
                            {{ message }}
                        </p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button
                            @click="isVisible = false; emit('close')"
                            class="inline-flex text-current hover:opacity-75 focus:outline-none"
                        >
                            <span class="sr-only">Close</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>