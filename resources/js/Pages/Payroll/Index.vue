<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Card from '@/Components/ui/card.vue';
import PayrollDetailsModal from '@/Components/PayrollDetailsModal.vue';
import { ref, watch } from 'vue';

const props = defineProps({
    employees: {
        type: Array,
        default: () => []
    },
    currentPeriod: {
        type: Object,
        required: true
    }
});

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP'
    }).format(amount || 0);
};

const selectedEmployee = ref(null);
const showDetailsModal = ref(false);

const openPayrollDetails = (employee) => {
    console.log('Opening payroll details for:', employee); // For debugging
    selectedEmployee.value = { ...employee }; // Create a copy of the employee object
    showDetailsModal.value = true;
};

const closePayrollDetails = () => {
    showDetailsModal.value = false;
    selectedEmployee.value = null;
};

const handleAllowancesUpdated = () => {
    // Refresh the page to get updated data
    window.location.reload();
};

// Watch for any changes in the selected employee data
watch(selectedEmployee, (newVal) => {
    if (newVal) {
        console.log('Selected employee data:', newVal);
    }
});
</script>

<template>
    <Head title="Payroll Management" />

    <AuthenticatedLayout>
        <div class="min-h-screen bg-background p-6">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-foreground">Payroll Management</h1>
                        <p class="text-muted-foreground">
                            Current Period: {{ new Date(currentPeriod.start_date).toLocaleDateString() }} - 
                            {{ new Date(currentPeriod.end_date).toLocaleDateString() }}
                        </p>
                    </div>
                    <button
                        class="bg-primary text-primary-foreground px-4 py-2 rounded-lg hover:opacity-90 transition-colors"
                    >
                        Process Payroll
                    </button>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <Card class="p-4 bg-primary/5">
                    <h3 class="text-sm font-medium text-muted-foreground">Total Employees</h3>
                    <p class="text-2xl font-bold">{{ employees.length }}</p>
                </Card>
                <Card class="p-4 bg-primary/5">
                    <h3 class="text-sm font-medium text-muted-foreground">Total Gross Pay</h3>
                    <p class="text-2xl font-bold">{{ formatCurrency(employees.reduce((sum, emp) => sum + emp.salary.grossPay, 0)) }}</p>
                </Card>
                <Card class="p-4 bg-primary/5">
                    <h3 class="text-sm font-medium text-muted-foreground">Total Deductions</h3>
                    <p class="text-2xl font-bold text-red-600">{{ formatCurrency(employees.reduce((sum, emp) => 
                        sum + emp.salary.deductions.sss + emp.salary.deductions.philhealth + emp.salary.deductions.pagibig, 0)) }}</p>
                </Card>
                <Card class="p-4 bg-primary/5">
                    <h3 class="text-sm font-medium text-muted-foreground">Total Net Pay</h3>
                    <p class="text-2xl font-bold text-primary">{{ formatCurrency(employees.reduce((sum, emp) => sum + emp.salary.netPay, 0)) }}</p>
                </Card>
            </div>

            <!-- Payroll Table -->
            <Card class="p-6">
                <div v-if="!employees.length" class="text-center py-8 text-muted-foreground">
                    No employees found.
                </div>
                
                <div v-else class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left p-4">Employee</th>
                                <th class="text-left p-4">Position</th>
                                <th class="text-right p-4">Basic Salary</th>
                                <th class="text-right p-4">Gross Pay</th>
                                <th class="text-right p-4">Net Pay</th>
                                <th class="text-center p-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="employee in employees" :key="employee.id" 
                                class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="p-4">
                                    <div>
                                        <p class="font-medium">{{ employee.name }}</p>
                                        <p class="text-sm text-muted-foreground">{{ employee.employeeId }}</p>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <div>
                                        <p class="font-medium">{{ employee.position }}</p>
                                        <p class="text-sm text-muted-foreground">{{ employee.department }}</p>
                                    </div>
                                </td>
                                <td class="p-4 text-right">{{ formatCurrency(employee.salary.basic) }}</td>
                                <td class="p-4 text-right">{{ formatCurrency(employee.salary.grossPay) }}</td>
                                <td class="p-4 text-right font-medium text-primary">{{ formatCurrency(employee.salary.netPay) }}</td>
                                <td class="p-4 text-center">
                                    <button 
                                        @click="openPayrollDetails(employee)"
                                        class="inline-flex items-center px-3 py-1.5 bg-primary/10 text-primary rounded-md hover:bg-primary/20 transition-colors text-sm font-medium"
                                    >
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        View Details
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </Card>

            <!-- Payroll Details Modal -->
            <PayrollDetailsModal
                :show="showDetailsModal"
                :employee="selectedEmployee || {}"
                @close="closePayrollDetails"
                @allowances-updated="handleAllowancesUpdated"
            />
        </div>
    </AuthenticatedLayout>
</template>