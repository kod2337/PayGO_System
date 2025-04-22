<script setup>
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import AllowancesModal from '@/Components/AllowancesModal.vue';
import { computed, ref } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        required: true
    },
    employee: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['close', 'allowances-updated']);

const showAllowancesModal = ref(false);

const openAllowancesModal = () => {
    showAllowancesModal.value = true;
};

const closeAllowancesModal = () => {
    showAllowancesModal.value = false;
};

const handleAllowancesUpdated = () => {
    emit('allowances-updated');
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP'
    }).format(amount || 0);
};

const computationSteps = computed(() => {
    const totalAllowances = Object.values(props.employee?.salary?.allowances || {}).reduce((a, b) => a + b, 0);
    
    return [
        { label: 'Basic Monthly Salary', value: props.employee?.salary?.basic || 0 },
        { label: 'Total Monthly Allowances', value: totalAllowances },
        { label: 'Gross Monthly Pay', value: props.employee?.salary?.grossPay || 0, isBold: true },
        { label: 'Less: Mandatory Deductions', isHeader: true },
        { label: 'SSS Contribution', value: props.employee?.salary?.deductions?.sss || 0, isDeduction: true },
        { label: 'PhilHealth Contribution', value: props.employee?.salary?.deductions?.philhealth || 0, isDeduction: true },
        { label: 'Pag-IBIG Contribution', value: props.employee?.salary?.deductions?.pagibig || 0, isDeduction: true },
        { label: 'Total Mandatory Deductions', value: props.employee?.salary?.computation?.mandatoryDeductions || 0, isDeduction: true, isBold: true },
        { label: 'Taxable Income', value: props.employee?.salary?.computation?.taxableIncome || 0, isBold: true },
        { label: 'Withholding Tax', help: props.employee?.salary?.computation?.taxBracket, value: props.employee?.salary?.deductions?.tax || 0, isDeduction: true },
        { label: 'Net Take Home Pay', value: props.employee?.salary?.netPay || 0, isTotal: true }
    ];
});
</script>

<template>
    <Modal :show="show" @close="$emit('close')" max-width="4xl">
        <div class="p-6">
            <!-- Header -->
            <div class="flex justify-between items-start mb-8 pb-4 border-b">
                <div>
                    <h2 class="text-xl font-bold text-foreground">Payroll Computation Worksheet</h2>
                    <div class="mt-1 flex items-center space-x-4">
                        <p class="text-sm text-muted-foreground">Employee: <span class="font-medium text-foreground">{{ employee.name }}</span></p>
                        <p class="text-sm text-muted-foreground">ID: <span class="font-medium text-foreground">{{ employee.employeeId }}</span></p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm text-muted-foreground">Position</p>
                    <p class="font-medium">{{ employee.position }}</p>
                    <p class="text-sm text-muted-foreground mt-1">Department</p>
                    <p class="font-medium">{{ employee.department }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
                <!-- Left Column - Computation Worksheet -->
                <div class="md:col-span-7 space-y-6">
                    <div class="bg-white rounded-lg border">
                        <div class="px-4 py-3 border-b bg-gray-50">
                            <h3 class="font-medium text-gray-900">Salary Computation Worksheet</h3>
                        </div>
                        <div class="p-4">
                            <div class="space-y-3">
                                <div v-for="(step, index) in computationSteps" :key="index"
                                     :class="[
                                         'flex justify-between items-center py-2',
                                         step.isHeader ? 'mt-4' : '',
                                         step.isDeduction ? 'text-red-600' : '',
                                         step.isTotal ? 'pt-4 border-t border-gray-200 text-lg font-bold text-primary' : '',
                                         step.isBold ? 'font-bold' : ''
                                     ]">
                                    <div class="flex items-center">
                                        <span :class="[
                                            step.isHeader ? 'font-medium' : 'text-sm',
                                            step.isDeduction ? 'text-red-600' : 'text-gray-600'
                                        ]">{{ step.label }}</span>
                                        <span v-if="step.help" 
                                              class="ml-2 text-xs text-gray-500 italic">
                                            ({{ step.help }})
                                        </span>
                                    </div>
                                    <span v-if="!step.isHeader" :class="[
                                        step.isDeduction ? 'text-red-600' : '',
                                        step.isBold ? 'font-bold' : '',
                                        step.isTotal ? 'text-lg font-bold text-primary' : ''
                                    ]">
                                        {{ step.isDeduction ? '-' : '' }}{{ formatCurrency(step.value) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Summary and Details -->
                <div class="md:col-span-5 space-y-6">
                    <!-- Basic Info Card -->
                    <div class="bg-primary/5 p-4 rounded-lg">
                        <h3 class="font-medium text-primary mb-3">Monthly Overview</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-muted-foreground">Basic Salary</p>
                                <p class="font-bold">{{ formatCurrency(employee?.salary?.basic || 0) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-muted-foreground">Gross Pay</p>
                                <p class="font-bold">{{ formatCurrency(employee?.salary?.grossPay || 0) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-muted-foreground">Total Deductions</p>
                                <p class="font-bold text-red-600">{{ formatCurrency(employee?.salary?.deductions?.total || 0) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-muted-foreground">Net Pay</p>
                                <p class="font-bold text-primary">{{ formatCurrency(employee?.salary?.netPay || 0) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Allowances -->
                    <div class="bg-white rounded-lg border">
                        <div class="px-4 py-3 border-b flex justify-between items-center">
                            <h3 class="font-medium text-gray-900">Monthly Allowances</h3>
                            <button 
                                @click="openAllowancesModal"
                                class="text-sm text-primary hover:text-primary/80 font-medium"
                            >
                                Edit Allowances
                            </button>
                        </div>
                        <div class="p-4">
                            <div class="space-y-3">
                                <div v-for="(amount, type) in (employee?.salary?.allowances || {})" 
                                     :key="type"
                                     class="flex justify-between items-center py-2 border-b border-gray-100 last:border-0">
                                    <span class="text-sm text-gray-600 capitalize">{{ type }}</span>
                                    <span class="font-medium">{{ formatCurrency(amount || 0) }}</span>
                                </div>
                                <div v-if="!Object.keys(employee?.salary?.allowances || {}).length" 
                                     class="text-sm text-gray-500 text-center py-2">
                                    No allowances
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tax Information -->
                    <div class="bg-white rounded-lg border">
                        <div class="px-4 py-3 border-b">
                            <h3 class="font-medium text-gray-900">Tax Information</h3>
                        </div>
                        <div class="p-4">
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm text-gray-600">Tax Bracket</p>
                                    <p class="font-medium">{{ employee?.salary?.computation?.taxBracket || 'Not applicable' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Monthly Taxable Income</p>
                                    <p class="font-medium">{{ formatCurrency(employee?.salary?.computation?.taxableIncome || 0) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Withholding Tax</p>
                                    <p class="font-medium text-red-600">{{ formatCurrency(employee?.salary?.deductions?.tax || 0) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-4 border-t flex justify-between items-center">
                <p class="text-sm text-muted-foreground">
                    * Tax computation based on TRAIN Law
                </p>
                <div class="flex gap-2">
                    <SecondaryButton @click="$emit('close')">Close</SecondaryButton>
                </div>
            </div>
        </div>
    </Modal>

    <AllowancesModal
        :show="showAllowancesModal"
        :employee="employee"
        @close="closeAllowancesModal"
        @updated="handleAllowancesUpdated"
    />
</template>