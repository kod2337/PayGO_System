<script setup>
const gradientId = `gradient-${Math.random().toString(36).substr(2, 9)}`;
</script>

<template>
    <div class="flex items-center">
        <div class="relative h-10 w-10">
            <svg 
                viewBox="0 0 24 24" 
                fill="none" 
                xmlns="http://www.w3.org/2000/svg"
                class="h-full w-full"
            >
                <defs>
                    <linearGradient :id="gradientId" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" style="stop-color:#4F46E5" />
                        <stop offset="100%" style="stop-color:#06B6D4" />
                    </linearGradient>
                </defs>

                <!-- Decorative Background Elements -->
                <circle 
                    cx="12" 
                    cy="12" 
                    r="11" 
                    class="fill-primary/5"
                />
                <circle 
                    cx="12" 
                    cy="12" 
                    r="10" 
                    :fill="`url(#${gradientId})`"
                    class="opacity-90"
                />

                <!-- Main Icon Elements -->
                <g class="stroke-white">
                    <!-- P Symbol -->
                    <path 
                        d="M8 8h4a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2h-4v4M8 8v8"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                    
                    <!-- Dollar Sign -->
                    <path 
                        d="M16 8v8M14 12h4"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                </g>

                <!-- Decorative Accents -->
                <circle 
                    cx="12" 
                    cy="12" 
                    r="6" 
                    class="stroke-white/20"
                    fill="none"
                    stroke-width="1"
                />
            </svg>
        </div>

        <!-- Optional: Add company name with gradient text -->
        <div class="ml-2 flex flex-col">
            <span class="text-xl font-bold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-cyan-600">
                PayGo
            </span>
            <span class="text-xs text-muted-foreground">
                Enterprise Payroll
            </span>
        </div>
    </div>
</template>
