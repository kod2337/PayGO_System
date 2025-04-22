<script setup>
defineProps({
    className: {
        type: String,
        default: ''
    }
});
</script>

<template>
    <div 
        :class="['animate-pulse bg-muted rounded', className]"
        aria-hidden="true"
    ></div>
</template>