<script setup>
defineProps({
    shortcuts: {
        type: Array,
        required: true
    }
});
</script>

<template>
    <div class="text-xs text-muted-foreground mt-2 flex items-center gap-3">
        <span>Keyboard shortcuts:</span>
        <div class="flex items-center gap-3">
            <span v-for="(shortcut, index) in shortcuts" :key="index" class="flex items-center">
                <template v-for="(key, keyIndex) in shortcut.keys" :key="keyIndex">
                    <kbd class="px-2 py-1 bg-muted rounded text-muted-foreground font-mono text-[10px] border border-border">
                        {{ key }}
                    </kbd>
                    <span v-if="keyIndex < shortcut.keys.length - 1" class="mx-1">+</span>
                </template>
                <span class="ml-1">{{ shortcut.action }}</span>
            </span>
        </div>
    </div>
</template>