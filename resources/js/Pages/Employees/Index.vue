<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Card from '@/Components/ui/card.vue';
import EmployeeDetailsModal from '@/Components/EmployeeDetailsModal.vue';
import { ref } from 'vue';

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

const selectedEmployee = ref(null);
const showDetailsModal = ref(false);

const openDetailsModal = (employee) => {
    selectedEmployee.value = employee;
    showDetailsModal.value = true;
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
            <div class="grid grid-cols-1 md:grid-cols-1 gap-4 mb-6">
                <Card class="p-4 bg-primary/5">
                    <h3 class="text-sm font-medium text-muted-foreground">Total Employees</h3>
                    <p class="text-2xl font-bold">{{ employees.length }}</p>
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
                                <th class="text-left p-4">Department</th>
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
                                <td class="p-4">{{ employee.position }}</td>
                                <td class="p-4">{{ employee.department }}</td>
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
            @close="closeDetailsModal"
        />
    </AuthenticatedLayout>
</template>