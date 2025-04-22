<script setup>
import { ref, nextTick } from 'vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import AllowancesModal from '@/Components/AllowancesModal.vue';
import Tooltip from '@/Components/Tooltip.vue';
import KeyboardShortcuts from '@/Components/KeyboardShortcuts.vue';
import SkeletonLoader from '@/Components/SkeletonLoader.vue';

const props = defineProps({
    show: Boolean,
    employee: Object
});

const emit = defineEmits(['close', 'allowances-updated']);
const showAllowancesModal = ref(false);
const isLoading = ref(false);

const openAllowancesModal = () => {
    showAllowancesModal.value = true;
};

const closeAllowancesModal = () => {
    showAllowancesModal.value = false;
};

const handleAllowancesUpdated = async () => {
    isLoading.value = true;
    closeAllowancesModal();
    await nextTick();
    emit('allowances-updated');
    setTimeout(() => {
        isLoading.value = false;
    }, 500);
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(value || 0);
};

const getDeductionDescription = (type) => {
    const descriptions = {
        tax: `Withholding tax based on annual income:
• ₱0 - ₱250,000: 0%
• ₱250,001 - ₱400,000: 20% of excess over ₱250,000
• ₱400,001 - ₱800,000: ₱30,000 + 25% of excess over ₱400,000
• ₱800,001 - ₱2M: ₱130,000 + 30% of excess over ₱800,000
• ₱2M - ₱8M: ₱490,000 + 32% of excess over ₱2M
• Over ₱8M: ₱2.41M + 35% of excess over ₱8M`,
        sss: 'Social Security System (SSS) contribution: 4.5% of basic salary, capped at ₱900 monthly',
        philhealth: 'PhilHealth Premium: 2.5% of basic salary (minimum contribution based on ₱10,000, maximum on ₱100,000)',
        pagibig: 'Pag-IBIG Fund contribution: 2% of basic salary, capped at ₱100 monthly',
    };
    return descriptions[type.toLowerCase()] || 'Other deduction';
};

const getAllowanceDescription = (type) => {
    const descriptions = {
        transportation: 'Non-taxable transportation assistance for commuting expenses',
        meal: 'Food and meal subsidy to cover work-related meal expenses',
        cola: 'Cost of Living Adjustment (COLA) to help offset inflation and living expenses',
    };
    return descriptions[type.toLowerCase()] || 'Additional allowance';
};

const getTaxCalculation = (employee) => {
    const basic = employee?.salary?.basic || 0;
    const mandatoryDeductions = Object.values(employee?.salary?.deductions || {})
        .reduce((sum, amount) => sum + Number(amount), 0);
    const taxableIncome = basic - mandatoryDeductions;
    const annual = taxableIncome * 12;

    if (annual <= 250000) {
        return 'Tax exempt - Annual taxable income below ₱250,000';
    }
    
    const calculateAnnualTax = () => {
        if (annual <= 400000) {
            return `20% of excess over ₱250,000\n(${formatCurrency(annual - 250000)} × 20% = ${formatCurrency((annual - 250000) * 0.20)} annually)`;
        } else if (annual <= 800000) {
            return `₱30,000 + 25% of excess over ₱400,000\n(₱30,000 + ${formatCurrency(annual - 400000)} × 25% = ${formatCurrency(30000 + (annual - 400000) * 0.25)} annually)`;
        } else if (annual <= 2000000) {
            return `₱130,000 + 30% of excess over ₱800,000\n(₱130,000 + ${formatCurrency(annual - 800000)} × 30% = ${formatCurrency(130000 + (annual - 800000) * 0.30)} annually)`;
        } else if (annual <= 8000000) {
            return `₱490,000 + 32% of excess over ₱2,000,000\n(₱490,000 + ${formatCurrency(annual - 2000000)} × 32% = ${formatCurrency(490000 + (annual - 2000000) * 0.32)} annually)`;
        } else {
            return `₱2,410,000 + 35% of excess over ₱8,000,000\n(₱2,410,000 + ${formatCurrency(annual - 8000000)} × 35% = ${formatCurrency(2410000 + (annual - 8000000) * 0.35)} annually)`;
        }
    };

    return `Monthly taxable income: ${formatCurrency(taxableIncome)}\nAnnual taxable income: ${formatCurrency(annual)}\n${calculateAnnualTax()}\nMonthly withholding: ${formatCurrency(employee?.salary?.deductions?.tax || 0)}`;
};

const handlePrint = () => {
    window.print();
};
</script>

<template>
    <Modal :show="show" @close="$emit('close')" maxWidth="4xl">
        <div class="p-6">
            <!-- Print Header (only visible when printing) -->
            <div class="hidden print:block mb-8 text-center">
                <h1 class="text-2xl font-bold mb-2">PayGO System</h1>
                <p class="text-sm mb-1">Payroll Management System</p>
                <p class="text-sm mb-4">Monthly Payroll Details</p>
                <div class="border-t border-border pt-4 text-left">
                    <p class="text-sm">Generated on: {{ new Date().toLocaleString() }}</p>
                </div>
            </div>

            <!-- Header (hidden when printing) -->
            <div class="print:hidden mb-6 border-b border-border pb-4">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-lg font-bold text-foreground">Payroll Details</h2>
                        <div class="flex items-center gap-2 mt-1">
                            <p class="text-sm text-muted-foreground">Employee: <span class="font-medium text-foreground">{{ employee?.name }}</span></p>
                            <span class="text-muted-foreground">•</span>
                            <p class="text-sm text-muted-foreground">ID: <span class="font-medium text-foreground">{{ employee?.employeeId }}</span></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <SecondaryButton @click="handlePrint" class="gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                <path fill-rule="evenodd" d="M5 2.75C5 1.784 5.784 1 6.75 1h6.5c.966 0 1.75.784 1.75 1.75v3.552c.377.046.752.097 1.126.153A2.212 2.212 0 0118 8.653v4.097A2.25 2.25 0 0115.75 15h-.241l.305 1.984A1.75 1.75 0 0114.084 19H5.915a1.75 1.75 0 01-1.73-2.016L4.492 15H4.25A2.25 2.25 0 012 12.75V8.653c0-1.082.775-2.034 1.874-2.198.374-.056.75-.107 1.127-.153L5 6.25v-3.5zm8.5 3.397a41.533 41.533 0 00-7 0V2.75a.25.25 0 01.25-.25h6.5a.25.25 0 01.25.25v3.397zM6.608 12.5a.25.25 0 00-.247.212l-.693 4.5a.25.25 0 00.247.288h8.17a.25.25 0 00.246-.288l-.692-4.5a.25.25 0 00-.247-.212H6.608z" clip-rule="evenodd" />
                            </svg>
                            Print
                        </SecondaryButton>
                        <PrimaryButton @click="openAllowancesModal" class="gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                            </svg>
                            Update Allowances
                        </PrimaryButton>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-8">
                <!-- Left column -->
                <div class="space-y-4">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-accent/5">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Type</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-muted-foreground uppercase tracking-wider">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                <template v-if="isLoading">
                                    <tr v-for="n in 4" :key="n">
                                        <td class="px-4 py-3">
                                            <div>
                                                <SkeletonLoader class="h-5 w-32 mb-2" />
                                                <SkeletonLoader class="h-4 w-24" />
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <SkeletonLoader class="h-5 w-24 ml-auto" />
                                        </td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div>
                                                <div class="font-medium">Basic Pay</div>
                                                <div class="text-sm text-muted-foreground">Monthly base salary</div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <span class="font-semibold">{{ formatCurrency(employee?.salary?.basic) }}</span>
                                        </td>
                                    </tr>
                                    <tr v-for="(amount, type) in employee?.salary?.allowances" :key="type">
                                        <td class="px-4 py-3">
                                            <div>
                                                <div class="font-medium capitalize">{{ type.replace('_', ' ') }}</div>
                                                <div class="text-sm text-muted-foreground">{{ getAllowanceDescription(type) }}</div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <span class="font-semibold text-[hsl(var(--success-text))]">+{{ formatCurrency(amount) }}</span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot class="bg-accent/5">
                                <tr>
                                    <td class="px-4 py-3 font-medium">Gross Pay</td>
                                    <td class="px-4 py-3 text-right font-bold">{{ formatCurrency(employee?.salary?.monthly?.gross) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Right column -->
                <div class="space-y-4">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-accent/5">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Deductions</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-muted-foreground uppercase tracking-wider">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                <template v-if="isLoading">
                                    <tr v-for="n in 4" :key="n">
                                        <td class="px-4 py-3">
                                            <div>
                                                <SkeletonLoader class="h-5 w-32 mb-2" />
                                                <SkeletonLoader class="h-4 w-48" />
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <SkeletonLoader class="h-5 w-24 ml-auto" />
                                        </td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <tr v-for="(amount, type) in employee?.salary?.deductions" :key="type">
                                        <td class="px-4 py-3">
                                            <div>
                                                <div class="font-medium uppercase">{{ type }}</div>
                                                <div class="text-sm text-muted-foreground whitespace-pre-line">
                                                    {{ type.toLowerCase() === 'tax' ? getTaxCalculation(employee) : getDeductionDescription(type) }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <span class="font-semibold text-[hsl(var(--error-text))]">-{{ formatCurrency(amount) }}</span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot class="bg-accent/5">
                                <tr>
                                    <td class="px-4 py-3 font-medium">Total Deductions</td>
                                    <td class="px-4 py-3 text-right font-bold text-[hsl(var(--error-text))]">
                                        -{{ formatCurrency(employee?.salary?.monthly?.deductions) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Summary -->
            <div class="mt-6 bg-primary/5 rounded-lg p-4 border-2 border-primary">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div>
                        <h3 class="text-lg font-semibold">Net Pay</h3>
                        <p class="text-sm text-muted-foreground">Final take-home amount</p>
                    </div>
                    <div class="text-right" v-if="isLoading">
                        <SkeletonLoader class="h-4 w-32 mb-2" />
                        <SkeletonLoader class="h-8 w-40" />
                    </div>
                    <div class="text-right" v-else>
                        <div class="text-sm text-muted-foreground mb-1">Monthly Net Salary</div>
                        <div class="text-2xl font-bold text-primary">
                            {{ formatCurrency(employee?.salary?.monthly?.net) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Print footer with signatures (only visible when printing) -->
            <div class="hidden print:block mt-12">
                <div class="grid grid-cols-2 gap-12">
                    <div class="text-center">
                        <div class="border-t border-border w-48 mx-auto mt-12"></div>
                        <p class="mt-2 text-sm font-medium">Employee Signature</p>
                        <p class="text-xs text-muted-foreground">Date: _________________</p>
                    </div>
                    <div class="text-center">
                        <div class="border-t border-border w-48 mx-auto mt-12"></div>
                        <p class="mt-2 text-sm font-medium">HR Manager Signature</p>
                        <p class="text-xs text-muted-foreground">Date: _________________</p>
                    </div>
                </div>
                <div class="mt-8 pt-4 border-t border-border text-xs text-muted-foreground">
                    <p>This is a computer-generated document. No signature is required.</p>
                    <p>For questions about this payroll, please contact HR department.</p>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-6 flex flex-col space-y-2 pt-4 border-t border-border">
                <div class="flex justify-end">
                    <SecondaryButton @click="$emit('close')">Close</SecondaryButton>
                </div>
                <KeyboardShortcuts :shortcuts="[
                    { keys: ['Esc'], action: 'Close dialog' }
                ]" />
            </div>
        </div>

        <!-- Nested Allowances Modal -->
        <AllowancesModal
            :show="showAllowancesModal"
            :employee="employee"
            @close="closeAllowancesModal"
            @updated="handleAllowancesUpdated"
        />
    </Modal>
</template>