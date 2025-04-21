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
    },
    calculateTotalCompensation: {
        type: Function,
        required: true
    }
});

defineEmits(['close']);
</script>

<template>
    <Modal :show="show" @close="$emit('close')" max-width="3xl">
        <div class="p-6">
            <h2 class="text-lg font-bold text-foreground mb-4">Employee Details</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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

                <!-- Salary Information -->
                <div class="space-y-4">
                    <h3 class="font-medium text-primary">Salary Information</h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-muted-foreground">Basic Salary</p>
                            <p class="font-medium">₱{{ employee.salary.basic.toLocaleString() }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-muted-foreground mb-2">Allowances</p>
                            <div class="bg-muted/10 rounded-lg p-3 space-y-1">
                                <div class="flex justify-between">
                                    <p class="text-sm">Transportation</p>
                                    <p class="text-sm">₱{{ employee.salary.allowances.transportation.toLocaleString() }}</p>
                                </div>
                                <div class="flex justify-between">
                                    <p class="text-sm">Meal</p>
                                    <p class="text-sm">₱{{ employee.salary.allowances.meal.toLocaleString() }}</p>
                                </div>
                                <div class="flex justify-between">
                                    <p class="text-sm">COLA</p>
                                    <p class="text-sm">₱{{ employee.salary.allowances.cola.toLocaleString() }}</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <p class="text-sm text-muted-foreground mb-2">Government Deductions</p>
                            <div class="bg-muted/10 rounded-lg p-3 space-y-1">
                                <div class="flex justify-between">
                                    <p class="text-sm">SSS (4.5%)</p>
                                    <p class="text-sm">₱{{ employee.salary.deductions.sss.toLocaleString() }}</p>
                                </div>
                                <div class="flex justify-between">
                                    <p class="text-sm">PhilHealth (2.5%)</p>
                                    <p class="text-sm">₱{{ employee.salary.deductions.philhealth.toLocaleString() }}</p>
                                </div>
                                <div class="flex justify-between">
                                    <p class="text-sm">Pag-IBIG (2%)</p>
                                    <p class="text-sm">₱{{ employee.salary.deductions.pagibig.toLocaleString() }}</p>
                                </div>
                                <div class="flex justify-between font-medium pt-2 border-t">
                                    <p class="text-sm">Total Deductions</p>
                                    <p class="text-sm">₱{{ Object.values(employee.salary.deductions).reduce((a, b) => a + b, 0).toLocaleString() }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-primary/5 rounded-lg p-3">
                            <div class="flex justify-between font-medium">
                                <p>Taxable Income</p>
                                <p>₱{{ (employee.salary.basic - Object.values(employee.salary.deductions).reduce((a, b) => a + b, 0)).toLocaleString() }}</p>
                            </div>
                        </div>

                        <div>
                            <p class="text-sm text-muted-foreground mb-2">Deductions</p>
                            <div class="bg-muted/10 rounded-lg p-3 space-y-1">
                                <div class="flex justify-between">
                                    <p class="text-sm">SSS</p>
                                    <p class="text-sm">₱{{ employee.salary.deductions.sss.toLocaleString() }}</p>
                                </div>
                                <div class="flex justify-between">
                                    <p class="text-sm">PhilHealth</p>
                                    <p class="text-sm">₱{{ employee.salary.deductions.philhealth.toLocaleString() }}</p>
                                </div>
                                <div class="flex justify-between">
                                    <p class="text-sm">Pag-IBIG</p>
                                    <p class="text-sm">₱{{ employee.salary.deductions.pagibig.toLocaleString() }}</p>
                                </div>
                                <div class="flex justify-between">
                                    <p class="text-sm">Tax</p>
                                    <p class="text-sm">₱{{ employee.salary.deductions.withholding.toLocaleString() }}</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <p class="text-sm text-muted-foreground mb-2">Benefits</p>
                            <div class="bg-muted/10 rounded-lg p-3 space-y-1">
                                <div class="flex justify-between">
                                    <p class="text-sm">13th Month</p>
                                    <p class="text-sm">₱{{ employee.salary.benefits.thirteenthMonth.toLocaleString() }}</p>
                                </div>
                                <div class="flex justify-between">
                                    <p class="text-sm">Rice Subsidy</p>
                                    <p class="text-sm">₱{{ employee.salary.benefits.riceBenefit.toLocaleString() }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t">
                            <div class="flex justify-between font-medium">
                                <p>Net Salary</p>
                                <p class="text-primary">₱{{ calculateTotalCompensation(employee.salary).net.toLocaleString() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Monthly Salary Section -->
                <div class="space-y-4">
                    <h3 class="font-medium text-primary">Monthly Salary</h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-muted-foreground">Basic Salary</p>
                            <p class="font-medium">₱{{ employee.salary.basic.toLocaleString() }}</p>
                        </div>
                        
                        <!-- Total Allowances -->
                        <div class="bg-muted/10 rounded-lg p-3">
                            <div class="flex justify-between font-medium">
                                <p>Total Allowances</p>
                                <p>₱{{ Object.values(employee.salary.allowances).reduce((a, b) => a + b, 0).toLocaleString() }}</p>
                            </div>
                        </div>

                        <!-- Total Deductions -->
                        <div class="bg-muted/10 rounded-lg p-3">
                            <div class="flex justify-between font-medium">
                                <p>Total Deductions</p>
                                <p>₱{{ Object.values(employee.salary.deductions).reduce((a, b) => a + b, 0).toLocaleString() }}</p>
                            </div>
                        </div>
                        
                        <div class="pt-4 border-t">
                            <div class="flex justify-between font-medium">
                                <p>Monthly Net Salary</p>
                                <p class="text-primary">₱{{ calculateTotalCompensation(employee.salary).net.toLocaleString() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Benefits Section -->
                <div class="space-y-4 mt-6">
                    <h3 class="font-medium text-primary">Annual Benefits</h3>
                    <div class="bg-muted/10 rounded-lg p-3 space-y-1">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm">13th Month Pay</p>
                                <p class="text-xs text-muted-foreground">(Released on December 24)</p>
                            </div>
                            <p class="text-sm">₱{{ employee.salary.basic.toLocaleString() }}</p>
                        </div>
                        <div class="flex justify-between">
                            <p class="text-sm">Rice Subsidy</p>
                            <p class="text-sm">₱{{ employee.salary.benefits.riceBenefit.toLocaleString() }}</p>
                        </div>
                        <div class="flex justify-between pt-2 border-t">
                            <p class="text-sm font-medium">Total Annual Benefits</p>
                            <p class="text-sm font-medium">₱{{ (employee.salary.basic + (employee.salary.benefits.riceBenefit * 12)).toLocaleString() }}</p>
                        </div>
                    </div>
                    <p class="text-xs text-muted-foreground italic">* 13th month pay is a mandatory benefit paid in full by December 24</p>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <SecondaryButton @click="$emit('close')">Close</SecondaryButton>
            </div>
        </div>
    </Modal>
</template>