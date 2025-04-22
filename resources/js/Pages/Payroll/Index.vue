<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Card from '@/Components/ui/card.vue';
import PayrollDetailsModal from '@/Components/PayrollDetailsModal.vue';
import AllowancesModal from '@/Components/AllowancesModal.vue';
import { ref, watch, computed } from 'vue';

const props = defineProps({
    employees: {
        type: Array,
        default: () => []
    },
    currentPeriod: {
        type: Object,
        required: true
    },
    frequencies: {
        type: Object,
        required: true
    },
    availablePeriods: {
        type: Array,
        default: () => []
    }
});

const selectedEmployee = ref(null);
const showDetailsModal = ref(false);
const showSettings = ref(false);
const selectedPeriod = ref(props.currentPeriod);
const search = ref('');

const settingsForm = useForm({
    frequency: props.currentPeriod.frequency || 'monthly',
    is_automated: props.currentPeriod.is_automated || false
});

const openPayrollDetails = (employee) => {
    selectedEmployee.value = employee;
    showDetailsModal.value = true;
};

const closePayrollDetails = () => {
    showDetailsModal.value = false;
    selectedEmployee.value = null;
};

const handleAllowancesUpdated = () => {
    window.location.reload();
};

const processPayroll = () => {
    if (!confirm('Are you sure you want to process the payroll for this period?')) return;
    
    useForm({
        period_id: selectedPeriod.value.id
    }).post(route('payroll.process'), {
        onSuccess: () => {
            window.location.reload();
        }
    });
};

const updateSettings = () => {
    settingsForm.post(route('payroll.settings'), {
        onSuccess: () => {
            showSettings.value = false;
        }
    });
};

watch(selectedEmployee, (newVal) => {
    if (newVal) {
        console.log('Selected employee data:', newVal);
    }
});

// Add computed property for total net pay
const totalNetPay = computed(() => {
    return props.employees.reduce((total, employee) => {
        const netPay = employee?.salary?.monthly?.net ?? 0;
        return total + netPay;
    }, 0);
});

const totalPayroll = computed(() => {
    return props.employees.reduce((total, employee) => {
        // Safely access nested properties
        const netPay = employee?.salary?.monthly?.net ?? 0;
        return total + netPay;
    }, 0);
});

const filteredEmployees = computed(() => {
    if (!search.value) return props.employees;
    return props.employees.filter(employee =>
        employee.name.toLowerCase().includes(search.value.toLowerCase())
    );
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(value);
};

const getTotalAllowances = (employee) => {
    try {
        if (!employee?.salary?.allowances) return 0;
        const allowances = employee.salary.allowances;
        if (typeof allowances !== 'object') return 0;
        return Object.values(allowances).reduce((total, amount) => total + (Number(amount) || 0), 0);
    } catch (error) {
        console.error('Error calculating allowances:', error);
        return 0;
    }
};

const getTotalDeductions = (employee) => {
    try {
        if (!employee?.salary?.deductions) return 0;
        const deductions = employee.salary.deductions;
        if (typeof deductions !== 'object') return 0;
        return Object.values(deductions).reduce((total, amount) => total + (Number(amount) || 0), 0);
    } catch (error) {
        console.error('Error calculating deductions:', error);
        return 0;
    }
};
</script>

<template>
    <Head title="Payroll Management" />

    <AuthenticatedLayout>
        <div class="min-h-screen bg-background p-6">
            <!-- Header with actions -->
            <div class="mb-8">
                <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-foreground">Payroll Management</h1>
                        <p class="mt-1 text-muted-foreground">Process and manage employee payroll records</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <select
                            v-model="selectedPeriod"
                            class="px-4 py-2 text-sm font-medium bg-white border border-border rounded-lg shadow-sm hover:bg-accent/5 focus:outline-none focus:ring-2 focus:ring-primary/50"
                        >
                            <option
                                v-for="period in availablePeriods"
                                :key="period.id"
                                :value="period"
                                :disabled="!period.is_unlocked"
                            >
                                {{ period.name }}
                            </option>
                        </select>
                        <button
                            @click="processPayroll"
                            class="px-4 py-2 bg-primary text-primary-foreground rounded-lg hover:opacity-90 transition-colors font-medium"
                        >
                            Process Payroll
                        </button>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <Card class="p-4 bg-primary/5">
                        <h3 class="text-sm font-medium text-muted-foreground">Total Employees</h3>
                        <p class="text-2xl font-bold mt-1">{{ filteredEmployees.length }}</p>
                    </Card>
                    <Card class="p-4 bg-green-50">
                        <h3 class="text-sm font-medium text-muted-foreground">Total Net Payroll</h3>
                        <p class="text-2xl font-bold mt-1 text-green-600">{{ formatCurrency(totalPayroll) }}</p>
                    </Card>
                    <Card class="p-4 bg-blue-50">
                        <h3 class="text-sm font-medium text-muted-foreground">Average Salary</h3>
                        <p class="text-2xl font-bold mt-1 text-blue-600">
                            {{ formatCurrency(totalPayroll / filteredEmployees.length || 0) }}
                        </p>
                    </Card>
                    <Card class="p-4 bg-accent/10">
                        <h3 class="text-sm font-medium text-muted-foreground">Pay Period</h3>
                        <p class="text-2xl font-bold mt-1">{{ selectedPeriod?.frequency || 'Monthly' }}</p>
                    </Card>
                </div>
            </div>

            <!-- Main Content -->
            <Card class="overflow-hidden">
                <!-- Search and filters -->
                <div class="p-4 border-b border-border">
                    <div class="flex items-center gap-4">
                        <div class="flex-1">
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Search employees..."
                                class="w-full px-4 py-2 text-sm border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/50"
                            />
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-accent/5">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Employee</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Position</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-muted-foreground uppercase tracking-wider">Basic</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-muted-foreground uppercase tracking-wider">Allowances</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-muted-foreground uppercase tracking-wider">Deductions</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-muted-foreground uppercase tracking-wider">Net Pay</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-muted-foreground uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            <tr v-for="employee in filteredEmployees" :key="employee.id" class="hover:bg-accent/5">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="font-medium text-foreground">{{ employee.name }}</div>
                                            <div class="text-sm text-muted-foreground">ID: {{ employee.employeeId }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-foreground">{{ employee.position }}</div>
                                    <div class="text-xs text-muted-foreground">{{ employee.department }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    {{ formatCurrency(employee.salary.basic) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <span class="font-medium text-green-600">+{{ formatCurrency(getTotalAllowances(employee)) }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <span class="font-medium text-red-600">-{{ formatCurrency(getTotalDeductions(employee)) }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <span class="font-bold text-primary">{{ formatCurrency(employee.salary.monthly.net) }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <button
                                        @click="openPayrollDetails(employee)"
                                        class="text-primary hover:text-primary/80 font-medium text-sm"
                                    >
                                        View Details
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot v-if="filteredEmployees.length > 0" class="bg-accent/5 border-t border-border">
                            <tr>
                                <td colspan="2" class="px-6 py-4 text-sm font-medium">Totals</td>
                                <td class="px-6 py-4 text-right text-sm font-medium">
                                    {{ formatCurrency(filteredEmployees.reduce((sum, emp) => sum + emp.salary.basic, 0)) }}
                                </td>
                                <td class="px-6 py-4 text-right text-sm">
                                    <span class="font-medium text-green-600">
                                        +{{ formatCurrency(filteredEmployees.reduce((sum, emp) => sum + getTotalAllowances(emp), 0)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right text-sm">
                                    <span class="font-medium text-red-600">
                                        -{{ formatCurrency(filteredEmployees.reduce((sum, emp) => sum + getTotalDeductions(emp), 0)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right text-sm">
                                    <span class="font-bold text-primary">{{ formatCurrency(totalPayroll) }}</span>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </Card>
        </div>

        <!-- Modals -->
        <PayrollDetailsModal
            :show="showDetailsModal"
            :employee="selectedEmployee"
            @close="closePayrollDetails"
            @allowances-updated="handleAllowancesUpdated"
        />
    </AuthenticatedLayout>
</template>

<style>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>