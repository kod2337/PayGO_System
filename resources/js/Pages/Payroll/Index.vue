<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Card from '@/Components/ui/card.vue';
import PayrollDetailsModal from '@/Components/PayrollDetailsModal.vue';
import AllowancesModal from '@/Components/AllowancesModal.vue';
import { ref, watch, computed } from 'vue';

const props = defineProps({
    employees: Array,
    currentPeriod: Object,
    availablePeriods: Array,
    frequencies: Object
});

const selectedEmployee = ref(null);
const showPayrollDetails = ref(false);
const selectedPeriod = ref(props.currentPeriod);

const form = useForm({
    frequency: props.currentPeriod?.frequency || 'monthly',
    is_automated: props.currentPeriod?.is_automated || false,
    period_id: props.currentPeriod?.id
});

const filteredPeriods = computed(() => {
    if (!props.availablePeriods) return [];
    return props.availablePeriods.filter(period => {
        const periodDate = new Date(period.start_date);
        return periodDate.getFullYear() === new Date().getFullYear();
    });
});

const calculateNextPayrollDate = (frequency) => {
    const today = new Date();
    const currentDay = today.getDate();
    let nextDate = new Date();

    switch (frequency) {
        case 'weekly':
            // Set to next Friday
            nextDate.setDate(today.getDate() + (5 + 7 - today.getDay()) % 7);
            break;
        case 'bi_weekly':
            // Set to 15th or end of month
            if (currentDay < 15) {
                nextDate.setDate(15);
            } else {
                nextDate = new Date(today.getFullYear(), today.getMonth() + 1, 0); // Last day of current month
            }
            break;
        case 'monthly':
            // Set to end of month
            nextDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            break;
        default:
            nextDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
    }

    return nextDate;
};

const nextPayrollDate = computed(() => {
    const date = calculateNextPayrollDate(form.frequency);
    return new Intl.DateTimeFormat('en-PH', { 
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    }).format(date);
});

const daysUntilNextPayroll = computed(() => {
    const next = calculateNextPayrollDate(form.frequency);
    const today = new Date();
    const diffTime = Math.abs(next - today);
    return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
});

const updateSettings = () => {
    form.post(route('payroll.settings'), {
        preserveScroll: true,
        onSuccess: () => {
            // Handle success
        },
    });
};

const processPayroll = () => {
    if (!selectedPeriod.value) return;
    
    form.post(route('payroll.process'), {
        preserveScroll: true,
        data: {
            period_id: selectedPeriod.value.id
        },
        onSuccess: () => {
            // Handle success
        },
    });
};

watch(() => form.frequency, (newFrequency) => {
    updateSettings();
});

const showDetails = (employee) => {
    selectedEmployee.value = employee;
    showPayrollDetails.value = true;
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP'
    }).format(value || 0);
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Payroll Management" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                <!-- Page Header -->
                <div class="flex flex-col gap-2">
                    <h1 class="text-2xl font-bold tracking-tight">Payroll Management</h1>
                    <p class="text-muted-foreground">Manage and process payroll for all employees</p>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Payroll Settings Card -->
                    <Card class="md:col-span-1">
                        <div class="p-6">
                            <div class="space-y-4">
                                <div>
                                    <h2 class="text-lg font-semibold leading-none tracking-tight">Payroll Settings</h2>
                                    <p class="text-sm text-muted-foreground mt-1">Configure payroll frequency and automation</p>
                                </div>
                                <div class="space-y-4">
                                    <div class="space-y-2">
                                        <label class="text-sm font-medium">Frequency</label>
                                        <select
                                            v-model="form.frequency"
                                            class="w-full rounded-md border-input bg-background px-3 py-2 text-sm shadow-sm transition-colors"
                                        >
                                            <option v-for="(label, value) in frequencies" :key="value" :value="value">
                                                {{ label }}
                                            </option>
                                        </select>
                                    </div>
                                    
                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <input
                                            type="checkbox"
                                            v-model="form.is_automated"
                                            class="rounded border-input w-4 h-4"
                                            @change="updateSettings"
                                        />
                                        <span class="text-sm font-medium">Enable Automated Processing</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </Card>

                    <!-- Payroll Period Selection -->
                    <div class="space-y-6">
                        <Card class="md:col-span-1">
                            <div class="p-6">
                                <div class="space-y-4">
                                    <div>
                                        <h2 class="text-lg font-semibold leading-none tracking-tight">Payroll Period</h2>
                                        <p class="text-sm text-muted-foreground mt-1">Select and process payroll for a specific period</p>
                                    </div>
                                    <div class="space-y-4">
                                        <div class="space-y-2">
                                            <label class="text-sm font-medium">Select Period</label>
                                            <select
                                                v-model="selectedPeriod"
                                                class="w-full rounded-md border-input bg-background px-3 py-2 text-sm shadow-sm"
                                            >
                                                <option v-for="period in filteredPeriods" 
                                                        :key="period.id" 
                                                        :value="period"
                                                        :disabled="period.is_processed || !period.is_unlocked">
                                                    {{ period.name }}
                                                    <span class="text-muted-foreground">
                                                        {{ period.is_processed ? '(Processed)' : !period.is_unlocked ? '(Locked)' : '' }}
                                                    </span>
                                                </option>
                                            </select>
                                        </div>
                                        
                                        <button
                                            @click="processPayroll"
                                            class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-primary text-primary-foreground font-semibold text-sm tracking-wide transition ease-in-out duration-150 rounded-md hover:bg-primary/90 disabled:opacity-50"
                                            :disabled="!selectedPeriod || selectedPeriod.is_processed || !selectedPeriod.is_unlocked || form.processing"
                                        >
                                            <template v-if="form.processing">
                                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                Processing...
                                            </template>
                                            <template v-else>
                                                Process Payroll
                                            </template>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </Card>

                        <!-- Next Payroll Info -->
                        <Card class="p-6 bg-primary/5">
                            <div class="space-y-4">
                                <h3 class="font-semibold">Next Payroll Date</h3>
                                <div class="flex items-center space-x-2 text-primary">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="font-medium">{{ nextPayrollDate }}</span>
                                </div>
                                <div class="text-sm text-muted-foreground">
                                    <p>Processing starts in {{ daysUntilNextPayroll }} days</p>
                                    <p class="mt-1">{{ form.is_automated ? 'Automated processing enabled' : 'Manual processing required' }}</p>
                                </div>
                            </div>
                        </Card>
                    </div>
                </div>

                <!-- Employees List -->
                <Card>
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-semibold leading-none tracking-tight">Employee Payroll Summary</h3>
                                <p class="text-sm text-muted-foreground mt-1">Overview of employee compensation details</p>
                            </div>
                        </div>

                        <div class="overflow-x-auto rounded-lg border">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-muted/50 border-b">
                                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider">Employee</th>
                                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-muted-foreground uppercase tracking-wider">Basic Pay</th>
                                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-muted-foreground uppercase tracking-wider">Allowances</th>
                                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-muted-foreground uppercase tracking-wider">Deductions</th>
                                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-muted-foreground uppercase tracking-wider">Net Pay</th>
                                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-muted-foreground uppercase tracking-wider w-24"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    <tr v-for="employee in employees" 
                                        :key="employee.id"
                                        class="hover:bg-muted/50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex flex-col">
                                                <span class="font-medium">{{ employee.name }}</span>
                                                <span class="text-sm text-muted-foreground">{{ employee.employeeId }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-right whitespace-nowrap font-medium">
                                            {{ formatCurrency(employee.salary.basic) }}
                                        </td>
                                        <td class="px-6 py-4 text-right whitespace-nowrap font-medium">
                                            {{ formatCurrency(Object.values(employee.salary.allowances).reduce((a, b) => a + b, 0)) }}
                                        </td>
                                        <td class="px-6 py-4 text-right whitespace-nowrap font-medium text-red-500">
                                            -{{ formatCurrency(employee.salary.monthly.deductions) }}
                                        </td>
                                        <td class="px-6 py-4 text-right whitespace-nowrap font-bold">
                                            {{ formatCurrency(employee.salary.monthly.net) }}
                                        </td>
                                        <td class="px-6 py-4 text-right whitespace-nowrap">
                                            <button
                                                @click="showDetails(employee)"
                                                class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-9 px-3"
                                            >
                                                View Details
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </Card>
            </div>
        </div>

        <PayrollDetailsModal
            :show="showPayrollDetails"
            :employee="selectedEmployee"
            @close="showPayrollDetails = false"
            @allowances-updated="$page.props.employees = employees"
        />
    </AuthenticatedLayout>
</template>