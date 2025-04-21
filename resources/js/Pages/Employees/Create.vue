<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Card from '@/Components/ui/card.vue';

// Add props definition
defineProps({
    departments: {
        type: Array,
        required: true
    },
    employmentStatuses: {
        type: Array,
        required: true
    }
});

const form = useForm({
    first_name: '',
    middle_name: '',
    last_name: '',
    suffix: '',
    birth_date: '',
    gender: '',
    civil_status: '',
    nationality: 'Filipino',
    address: '',
    contact_number: '',
    emergency_contact_name: '',
    emergency_contact_number: '',
    tin_number: '',
    sss_number: '',
    philhealth_number: '',
    pagibig_number: '',
    employment_status: '',
    department: '',
    position: '',
    date_hired: '',
    basic_salary: '',
});

const submitForm = () => {
    form.post(route('employees.store'), {
        onSuccess: () => form.reset()
    });
};
</script>

<template>
    <Head title="Add New Employee" />

    <AuthenticatedLayout>
        <div class="min-h-screen bg-background p-6">
            <Card class="max-w-4xl mx-auto">
                <form @submit.prevent="submitForm" class="space-y-6 p-6">
                    <h2 class="text-2xl font-bold">Add New Employee</h2>

                    <!-- Personal Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium mb-1">First Name</label>
                            <input 
                                v-model="form.first_name"
                                type="text"
                                class="w-full px-3 py-2 border border-border rounded-lg"
                                required
                            />
                            <div v-if="form.errors.first_name" class="text-red-500 text-sm mt-1">
                                {{ form.errors.first_name }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Last Name</label>
                            <input 
                                v-model="form.last_name"
                                type="text"
                                class="w-full px-3 py-2 border border-border rounded-lg"
                                required
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Middle Name</label>
                            <input 
                                v-model="form.middle_name"
                                type="text"
                                class="w-full px-3 py-2 border border-border rounded-lg"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Birth Date</label>
                            <input 
                                v-model="form.birth_date"
                                type="date"
                                class="w-full px-3 py-2 border border-border rounded-lg"
                                required
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Gender</label>
                            <select 
                                v-model="form.gender"
                                class="w-full px-3 py-2 border border-border rounded-lg"
                                required
                            >
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Civil Status</label>
                            <select 
                                v-model="form.civil_status"
                                class="w-full px-3 py-2 border border-border rounded-lg"
                                required
                            >
                                <option value="">Select Status</option>
                                <option value="single">Single</option>
                                <option value="married">Married</option>
                                <option value="widowed">Widowed</option>
                                <option value="separated">Separated</option>
                            </select>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium mb-1">Contact Number</label>
                            <input 
                                v-model="form.contact_number"
                                type="text"
                                class="w-full px-3 py-2 border border-border rounded-lg"
                                required
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Emergency Contact Name</label>
                            <input 
                                v-model="form.emergency_contact_name"
                                type="text"
                                class="w-full px-3 py-2 border border-border rounded-lg"
                                required
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Emergency Contact Number</label>
                            <input 
                                v-model="form.emergency_contact_number"
                                type="text"
                                class="w-full px-3 py-2 border border-border rounded-lg"
                                required
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Address</label>
                            <input 
                                v-model="form.address"
                                type="text"
                                class="w-full px-3 py-2 border border-border rounded-lg"
                                required
                            />
                        </div>
                    </div>

                    <!-- Employment Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium mb-1">Department</label>
                            <select 
                                v-model="form.department"
                                class="w-full px-3 py-2 border border-border rounded-lg"
                                required
                            >
                                <option value="">Select Department</option>
                                <option 
                                    v-for="department in departments" 
                                    :key="department" 
                                    :value="department"
                                >
                                    {{ department }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Position</label>
                            <input 
                                v-model="form.position"
                                type="text"
                                class="w-full px-3 py-2 border border-border rounded-lg"
                                required
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Employment Status</label>
                            <select 
                                v-model="form.employment_status"
                                class="w-full px-3 py-2 border border-border rounded-lg"
                                required
                            >
                                <option value="">Select Status</option>
                                <option 
                                    v-for="status in employmentStatuses" 
                                    :key="status" 
                                    :value="status"
                                >
                                    {{ status }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Date Hired</label>
                            <input 
                                v-model="form.date_hired"
                                type="date"
                                class="w-full px-3 py-2 border border-border rounded-lg"
                                required
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Basic Salary</label>
                            <input 
                                v-model="form.basic_salary"
                                type="number"
                                step="0.01"
                                class="w-full px-3 py-2 border border-border rounded-lg"
                                required
                            />
                        </div>
                    </div>

                    <!-- Government IDs -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium mb-1">SSS Number</label>
                            <input 
                                v-model="form.sss_number"
                                type="text"
                                class="w-full px-3 py-2 border border-border rounded-lg"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">TIN Number</label>
                            <input 
                                v-model="form.tin_number"
                                type="text"
                                class="w-full px-3 py-2 border border-border rounded-lg"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">PhilHealth Number</label>
                            <input 
                                v-model="form.philhealth_number"
                                type="text"
                                class="w-full px-3 py-2 border border-border rounded-lg"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Pag-IBIG Number</label>
                            <input 
                                v-model="form.pagibig_number"
                                type="text"
                                class="w-full px-3 py-2 border border-border rounded-lg"
                            />
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <button 
                            type="button"
                            class="px-4 py-2 border border-border rounded-lg hover:bg-accent"
                            @click="$router.go(-1)"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit"
                            class="px-4 py-2 bg-primary text-primary-foreground rounded-lg hover:opacity-90"
                            :disabled="form.processing"
                        >
                            Save Employee
                        </button>
                    </div>
                </form>
            </Card>
        </div>
    </AuthenticatedLayout>
</template>