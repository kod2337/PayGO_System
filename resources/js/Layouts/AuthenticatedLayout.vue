<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
const showSidebar = ref(true);

const navigation = [
    { 
        name: 'Dashboard', 
        href: route('dashboard'), 
        icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' 
    },
    { 
        name: 'Employees', 
        href: route('employees.index'), 
        icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'
    },
    {
        name: 'Payroll',
        href: route('payroll.index'),
        icon: 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'
    },
    { 
        name: 'Attendance', 
        href: '#', 
        icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z' 
    },
    { 
        name: 'Reports', 
        href: '#', 
        icon: 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' 
    },
    { 
        name: 'Settings', 
        href: '#', 
        icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z' 
    },
];
</script>

<template>
    <div>
        <!-- Sidebar -->
        <div class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 transition-transform duration-200"
             :class="{ '-translate-x-full': !showSidebar }">
            <div class="flex h-16 items-center justify-between px-4 border-b border-gray-200">
                <Link :href="route('dashboard')" class="flex items-center">
                    <ApplicationLogo class="block h-9 w-auto text-primary" />
                    <div class="ml-2 flex flex-col">
                 
                    </div>
                </Link>
                <button @click="showSidebar = false" class="lg:hidden">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <nav class="mt-5 px-4 space-y-1">
                <Link v-for="item in navigation" 
                      :key="item.name"
                      :href="item.href"
                      :class="[
                          route().current(item.name.toLowerCase()) 
                              ? 'bg-primary text-primary-foreground' 
                              : 'text-gray-600 hover:bg-gray-50',
                          'group flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors'
                      ]">
                    <svg class="mr-3 h-5 w-5" 
                         :class="route().current(item.name.toLowerCase()) ? 'text-primary-foreground' : 'text-gray-400'"
                         fill="none" 
                         stroke="currentColor" 
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                    </svg>
                    {{ item.name }}
                </Link>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="lg:pl-64">
            <!-- Mobile header -->
            <div class="sticky top-0 z-40 lg:hidden">
                <div class="flex h-16 items-center gap-x-4 border-b border-gray-200 bg-white px-4">
                    <button @click="showSidebar = true">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <ApplicationLogo class="h-8 w-auto" />
                </div>
            </div>

            <!-- Main content -->
            <main class="py-4">
                <slot />
            </main>
        </div>
    </div>
</template>
