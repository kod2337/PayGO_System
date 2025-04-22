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
        <div class="p-6">
            <h2 class="text-lg font-bold text-foreground mb-4">Employee Details</h2>
            
            <div class="space-y-4">
                <!-- Personal Information -->
                <div class="space-y-4">
                    <h3 class="font-medium text-primary">Personal Information</h3>
                    <div class="space-y-2">
                        <div>
                            <p class="text-sm text-muted-foreground">Full Name</p>
                            <p class="font-medium">{{ employee.name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Employee ID</p>
                            <p class="font-medium">{{ employee.employeeId }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Position</p>
                            <p class="font-medium">{{ employee.position }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Department</p>
                            <p class="font-medium">{{ employee.department }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Status</p>
                            <p class="font-medium">{{ employee.status }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <SecondaryButton @click="$emit('close')">Close</SecondaryButton>
            </div>
        </div>
    </Modal>
</template>