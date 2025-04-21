<script setup>
defineProps({
  class: {
    type: String,
    default: ''
  }
});
</script>

<template>
  <div 
    class="rounded-lg border border-border bg-card text-card-foreground shadow-sm" 
    :class="class"
  >
    <slot></slot>
  </div>
</template>