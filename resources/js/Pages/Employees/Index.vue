<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Card from '@/Components/ui/card.vue';
import EmployeeDetailsModal from '@/Components/EmployeeDetailsModal.vue';
import { ref } from 'vue';
import { calculateDeductions } from '@/Utils/payrollCalculations';

const props = defineProps({
    employees: {
        type: Array,
        default: () => []
    },
    error: {
        type: String,
        default: null
    }
});

// Calculate total compensation
const calculateTotalCompensation = (salary) => {
    if (!salary) return { gross: 0, net: 0, totalBenefits: 0, monthly: { net: 0 } };
    
    const totalAllowances = Object.values(salary.allowances || {}).reduce((a, b) => a + b, 0);
    const totalDeductions = Object.values(salary.deductions || {}).reduce((a, b) => a + b, 0);
    const totalBenefits = Object.values(salary.benefits || {}).reduce((a, b) => a + b, 0);
    
    // Monthly net calculation (excluding 13th month)
    const monthlyNet = salary.basic + totalAllowances - totalDeductions;
    
    return {
        gross: salary.basic + totalAllowances,
        net: monthlyNet,
        totalBenefits: totalBenefits,
        monthly: {
            net: monthlyNet
        }
    };
};

// Calculate totals for stats
const totalPayroll = (props.employees || []).reduce((total, emp) => {
    return total + calculateTotalCompensation(emp.salary).net;
}, 0);

const averageSalary = props.employees?.length 
    ? totalPayroll / props.employees.length 
    : 0;

const selectedEmployee = ref(null);
const showDetailsModal = ref(false);

const openDetailsModal = (employee) => {
    console.log('Opening modal for employee:', employee);
    selectedEmployee.value = employee;
    showDetailsModal.value = true;
    console.log('Modal state:', { selectedEmployee: selectedEmployee.value, showDetailsModal: showDetailsModal.value });
};

const closeDetailsModal = () => {
    showDetailsModal.value = false;
    selectedEmployee.value = null;
};
</script>

<template>
    <Head title="Employees Management" />

    <AuthenticatedLayout>
        <div class="min-h-screen bg-background p-6">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-foreground">Employees Management</h1>
                        <p class="text-muted-foreground">Manage employee information and salaries</p>
                    </div>
                    <Link
                        :href="route('employees.create')"
                        class="bg-primary text-primary-foreground px-4 py-2 rounded-lg hover:opacity-90 transition-colors"
                    >
                        Add New Employee
                    </Link>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <Card class="p-4 bg-primary/5">
                    <h3 class="text-sm font-medium text-muted-foreground">Total Employees</h3>
                    <p class="text-2xl font-bold">{{ employees.length }}</p>
                </Card>
                <Card class="p-4 bg-primary/5">
                    <h3 class="text-sm font-medium text-muted-foreground">Total Monthly Payroll</h3>
                    <p class="text-2xl font-bold">₱{{ totalPayroll.toLocaleString() }}</p>
                </Card>
                <Card class="p-4 bg-primary/5">
                    <h3 class="text-sm font-medium text-muted-foreground">Average Salary</h3>
                    <p class="text-2xl font-bold">₱{{ averageSalary.toLocaleString() }}</p>
                </Card>
            </div>

            <!-- Employees Table -->
            <Card class="p-6">
                <div v-if="error" class="text-red-500 p-4">
                    {{ error }}
                </div>
                
                <div v-else-if="!employees.length" class="text-center py-8 text-muted-foreground">
                    No employees found. Add your first employee to get started.
                </div>
                
                <div v-else class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="text-left p-4">Employee</th>
                                <th class="text-left p-4">Position</th>
                                <th class="text-left p-4">Basic Salary</th>
                                <th class="text-left p-4">Net Salary</th>
                                <th class="text-left p-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="employee in employees" :key="employee.id">
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
                                <td class="p-4">₱{{ employee.salary.basic.toLocaleString() }}</td>
                                <td class="p-4">₱{{ employee.salary.monthly.net.toLocaleString() }}</td>
                                <td class="p-4">
                                    <button 
                                        @click="openDetailsModal(employee)"
                                        class="text-primary hover:underline"
                                    >
                                        View Details
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </Card>
        </div>

        <!-- Modal should be at the root level of your component -->
        <EmployeeDetailsModal
            :show="showDetailsModal"
            :employee="selectedEmployee || {}"
            :calculateTotalCompensation="calculateTotalCompensation"
            @close="closeDetailsModal"
        />
    </AuthenticatedLayout>
</template>