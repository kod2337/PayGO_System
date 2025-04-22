<script setup>
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import Notification from '@/Components/Notification.vue';
import KeyboardShortcuts from '@/Components/KeyboardShortcuts.vue';
import { useForm } from '@inertiajs/vue3';
import { ref, watch, computed, onMounted, onBeforeUnmount } from 'vue';

const props = defineProps({
    show: Boolean,
    employee: Object,
});

const emit = defineEmits(['close', 'updated']);

const form = useForm({
    transportation_allowance: 0,
    meal_allowance: 0,
    cola: 0,
});

const isLoading = computed(() => form.processing);
const hasChanges = computed(() => {
    if (!props.employee?.salary?.allowances) return false;
    return form.transportation_allowance !== (props.employee.salary.allowances.transportation || 0) ||
           form.meal_allowance !== (props.employee.salary.allowances.meal || 0) ||
           form.cola !== (props.employee.salary.allowances.cola || 0);
});

watch(() => props.employee?.salary?.allowances, (newAllowances) => {
    if (newAllowances) {
        form.transportation_allowance = Number(newAllowances.transportation || 0);
        form.meal_allowance = Number(newAllowances.meal || 0);
        form.cola = Number(newAllowances.cola || 0);
    }
}, { immediate: true });

const handleInput = (e, field) => {
    const value = e.target.value.replace(/[^0-9.]/g, '');
    form[field] = value === '' ? 0 : Number(value);
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP'
    }).format(value || 0);
};

const showSuccess = ref(false);

const updateAllowances = () => {
    form.put(route('employees.update-allowances', props.employee.id), {
        preserveScroll: true,
        onSuccess: () => {
            showSuccess.value = true;
            setTimeout(() => {
                emit('updated');
                emit('close');
            }, 1000);
        },
    });
};

const handleKeydown = (e) => {
    // Submit form on Ctrl+Enter or Cmd+Enter
    if ((e.ctrlKey || e.metaKey) && e.key === 'Enter' && !isLoading.value && hasChanges.value) {
        e.preventDefault();
        updateAllowances();
    }
};

onMounted(() => {
    document.addEventListener('keydown', handleKeydown);
});

onBeforeUnmount(() => {
    document.removeEventListener('keydown', handleKeydown);
});
</script>

<template>
    <Modal :show="show" @close="$emit('close')" maxWidth="2xl">
        <div class="p-6">
            <div class="mb-6 border-b border-border pb-4">
                <h2 class="text-lg font-bold text-foreground">Update Allowances</h2>
                <div class="flex items-center gap-2 mt-1">
                    <p class="text-sm text-muted-foreground">Employee: <span class="font-medium text-foreground">{{ employee?.name }}</span></p>
                    <span class="text-muted-foreground">•</span>
                    <p class="text-sm text-muted-foreground">ID: <span class="font-medium text-foreground">{{ employee?.employeeId }}</span></p>
                </div>
            </div>

            <form @submit.prevent="updateAllowances" class="space-y-4 lg:space-y-6">
                <div class="space-y-4">
                    <!-- Transportation Allowance -->
                    <div class="bg-[hsl(var(--success-bg))] rounded-lg p-4">
                        <label class="block font-medium text-[hsl(var(--success-text))] mb-1">
                            Transportation Allowance
                        </label>
                        <p class="text-sm text-muted-foreground mb-3">
                            Non-taxable allowance for transportation expenses. Helps cover costs of commuting, fuel, or public transport.
                        </p>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground">₱</span>
                            <input
                                type="text"
                                class="block w-full rounded-md pl-8 bg-background border-[hsl(var(--success-border))] focus:border-primary text-lg font-medium"
                                :value="form.transportation_allowance"
                                @input="e => handleInput(e, 'transportation_allowance')"
                            />
                        </div>
                        <InputError :message="form.errors.transportation_allowance" class="mt-1" />
                    </div>

                    <!-- Meal Allowance -->
                    <div class="bg-[hsl(var(--success-bg))] rounded-lg p-4">
                        <label class="block font-medium text-[hsl(var(--success-text))] mb-1">
                            Meal Allowance
                        </label>
                        <p class="text-sm text-muted-foreground mb-3">
                            Food subsidy to ensure proper nutrition during work hours. Covers lunch and other work-related meal expenses.
                        </p>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground">₱</span>
                            <input
                                type="text"
                                class="block w-full rounded-md pl-8 bg-background border-[hsl(var(--success-border))] focus:border-primary text-lg font-medium"
                                :value="form.meal_allowance"
                                @input="e => handleInput(e, 'meal_allowance')"
                            />
                        </div>
                        <InputError :message="form.errors.meal_allowance" class="mt-1" />
                    </div>

                    <!-- COLA -->
                    <div class="bg-[hsl(var(--success-bg))] rounded-lg p-4">
                        <label class="block font-medium text-[hsl(var(--success-text))] mb-1">
                            Cost of Living Allowance (COLA)
                        </label>
                        <p class="text-sm text-muted-foreground mb-3">
                            Supplementary allowance to help maintain purchasing power amid rising costs. Adjusts compensation for economic changes.
                        </p>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground">₱</span>
                            <input
                                type="text"
                                class="block w-full rounded-md pl-8 bg-background border-[hsl(var(--success-border))] focus:border-primary text-lg font-medium"
                                :value="form.cola"
                                @input="e => handleInput(e, 'cola')"
                            />
                        </div>
                        <InputError :message="form.errors.cola" class="mt-1" />
                    </div>

                    <!-- Total -->
                    <div class="bg-[hsl(var(--success-bg))] rounded-lg p-4 mt-6 border border-[hsl(var(--success-border))]">
                        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center">
                            <div class="mb-2 lg:mb-0">
                                <span class="font-medium text-[hsl(var(--success-text))]">Total Monthly Allowances</span>
                                <p class="text-sm text-muted-foreground">Combined non-taxable benefits</p>
                            </div>
                            <span class="text-lg font-bold text-[hsl(var(--success-text))]">
                                {{ formatCurrency(Number(form.transportation_allowance) + Number(form.meal_allowance) + Number(form.cola)) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex flex-col space-y-2 pt-4 border-t border-border">
                    <div class="flex justify-end space-x-2">
                        <SecondaryButton 
                            type="button" 
                            @click="$emit('close')"
                            :disabled="isLoading"
                        >
                            Cancel
                        </SecondaryButton>
                        <PrimaryButton 
                            type="submit" 
                            :disabled="isLoading || !hasChanges"
                            class="relative"
                        >
                            <template v-if="isLoading">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Updating...</span>
                            </template>
                            <template v-else>
                                Update Allowances
                            </template>
                        </PrimaryButton>
                    </div>
                    <KeyboardShortcuts :shortcuts="[
                        { keys: ['Esc'], action: 'Close dialog' },
                        { keys: ['Ctrl/⌘', 'Enter'], action: 'Save changes' }
                    ]" />
                </div>
            </form>

            <Notification
                v-if="showSuccess"
                type="success"
                message="Allowances updated successfully!"
                @close="showSuccess = false"
            />
        </div>
    </Modal>
</template>