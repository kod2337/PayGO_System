<script setup>
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

defineProps({
    show: {
        type: Boolean,
        required: true
    },
    employee: {
        type: Object,
        required: true
    }
});

defineEmits(['close']);
</script>

<template>
    <Modal :show="show" @close="$emit('close')" max-width="3xl">
        <div class="p-8 space-y-10 bg-white dark:bg-gray-900 rounded-lg shadow-lg">
            <h2 class="text-2xl font-extrabold text-primary mb-6 tracking-tight border-b pb-2">Employee Details</h2>
            
            <!-- Personal Information -->
            <section>
                <h3 class="text-lg font-semibold text-primary mb-3">Personal Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                    <div>
                        <span class="block text-xs text-muted-foreground">Full Name</span>
                        <span class="font-medium text-base">{{ employee.name }}</span>
                    </div>
                    <div>
                        <span class="block text-xs text-muted-foreground">Birthday</span>
                        <span class="font-medium text-base">{{ employee.birthDate }}</span>
                    </div>
                    <div>
                        <span class="block text-xs text-muted-foreground">Nationality</span>
                        <span class="font-medium text-base">{{ employee.nationality }}</span>
                    </div>
                    <div>
                        <span class="block text-xs text-muted-foreground">Civil Status</span>
                        <span class="font-medium text-base">{{ employee.civilStatus }}</span>
                    </div>
                    <div class="md:col-span-2">
                        <span class="block text-xs text-muted-foreground">Address</span>
                        <span class="font-medium text-base">{{ employee.address }}</span>
                    </div>
                    <div>
                        <span class="block text-xs text-muted-foreground">Contact No.</span>
                        <span class="font-medium text-base">{{ employee.contactNumber }}</span>
                    </div>
                    <div>
                        <span class="block text-xs text-muted-foreground">Emergency Contact</span>
                        <span class="font-medium text-base">{{ employee.emergencyContactName }}</span>
                    </div>
                    <div>
                        <span class="block text-xs text-muted-foreground">Emergency Contact No.</span>
                        <span class="font-medium text-base">{{ employee.emergencyContactNumber }}</span>
                    </div>
                </div>
            </section>

            <!-- Employment Information -->
            <section>
                <h3 class="text-lg font-semibold text-primary mb-3">Employment Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                    <div>
                        <span class="block text-xs text-muted-foreground">Employee ID</span>
                        <span class="font-medium text-base">{{ employee.employeeId }}</span>
                    </div>
                    <div>
                        <span class="block text-xs text-muted-foreground">Date Hired</span>
                        <span class="font-medium text-base">{{ employee.dateHired }}</span>
                    </div>
                    <div>
                        <span class="block text-xs text-muted-foreground">Position</span>
                        <span class="font-medium text-base">{{ employee.position }}</span>
                    </div>
                    <div>
                        <span class="block text-xs text-muted-foreground">Department</span>
                        <span class="font-medium text-base">{{ employee.department }}</span>
                    </div>
                    <div>
                        <span class="block text-xs text-muted-foreground">Status</span>
                        <span class="font-medium text-base">{{ employee.status }}</span>
                    </div>
                </div>
            </section>

            <!-- Government Numbers -->
            <section>
                <h3 class="text-lg font-semibold text-primary mb-3">Government Numbers</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                    <div>
                        <span class="block text-xs text-muted-foreground">SSS No.</span>
                        <span class="font-medium text-base">{{ employee.SSS }}</span>
                    </div>
                    <div>
                        <span class="block text-xs text-muted-foreground">PhilHealth No.</span>
                        <span class="font-medium text-base">{{ employee.PhilHealth }}</span>
                    </div>
                    <div>
                        <span class="block text-xs text-muted-foreground">Pag-Ibig No.</span>
                        <span class="font-medium text-base">{{ employee.PagIbig }}</span>
                    </div>
                    <div>
                        <span class="block text-xs text-muted-foreground">TIN No.</span>
                        <span class="font-medium text-base">{{ employee.TIN }}</span>
                    </div>
                </div>
            </section>

            <div class="flex justify-end pt-4">
                <SecondaryButton @click="$emit('close')" class="px-6 py-2">Close</SecondaryButton>
            </div>
        </div>
    </Modal>
</template>