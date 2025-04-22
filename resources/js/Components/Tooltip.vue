<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';

const props = defineProps({
    text: {
        type: String,
        required: true
    },
    position: {
        type: String,
        default: 'top',
        validator: (value) => ['top', 'bottom', 'left', 'right'].includes(value)
    }
});

const tooltipRef = ref(null);
const showTooltip = ref(false);
const timeoutId = ref(null);

const handleMouseEnter = () => {
    timeoutId.value = setTimeout(() => {
        showTooltip.value = true;
    }, 200);
};

const handleMouseLeave = () => {
    if (timeoutId.value) {
        clearTimeout(timeoutId.value);
    }
    showTooltip.value = false;
};

onBeforeUnmount(() => {
    if (timeoutId.value) {
        clearTimeout(timeoutId.value);
    }
});
</script>

<template>
    <div class="relative inline-block" @mouseenter="handleMouseEnter" @mouseleave="handleMouseLeave">
        <slot></slot>
        <div v-show="showTooltip" ref="tooltipRef"
            class="absolute z-50 px-3 py-2 text-xs text-white bg-gray-900 rounded shadow-lg whitespace-pre-line max-w-xs"
            :class="{
                'bottom-full left-1/2 -translate-x-1/2 mb-1': position === 'top',
                'top-full left-1/2 -translate-x-1/2 mt-1': position === 'bottom',
                'right-full top-1/2 -translate-y-1/2 mr-1': position === 'left',
                'left-full top-1/2 -translate-y-1/2 ml-1': position === 'right'
            }"
        >
            {{ text }}
            <div class="absolute w-0 h-0"
                :class="{
                    'bottom-[-4px] left-1/2 -ml-1 border-l-4 border-r-4 border-t-4 border-l-transparent border-r-transparent border-t-gray-900': position === 'top',
                    'top-[-4px] left-1/2 -ml-1 border-l-4 border-r-4 border-b-4 border-l-transparent border-r-transparent border-b-gray-900': position === 'bottom',
                    'right-[-4px] top-1/2 -mt-1 border-t-4 border-b-4 border-l-4 border-t-transparent border-b-transparent border-l-gray-900': position === 'left',
                    'left-[-4px] top-1/2 -mt-1 border-t-4 border-b-4 border-r-4 border-t-transparent border-b-transparent border-r-gray-900': position === 'right'
                }"
            ></div>
        </div>
    </div>
</template>