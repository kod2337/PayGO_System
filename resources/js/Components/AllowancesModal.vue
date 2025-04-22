<script setup>
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

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

// Watch for employee changes to update form
watch(() => props.employee, (newEmployee) => {
    if (newEmployee?.salary?.allowances) {
        form.transportation_allowance = Number(newEmployee.salary.allowances.transportation || 0);
        form.meal_allowance = Number(newEmployee.salary.allowances.meal || 0);
        form.cola = Number(newEmployee.salary.allowances.cola || 0);
    }
}, { immediate: true });

const handleInput = (e, field) => {
    const value = e.target.value.replace(/[^0-9.]/g, '');
    if (value === '' || !isNaN(value)) {
        form[field] = value === '' ? 0 : Number(value);
    }
};

const updateAllowances = () => {
    form.put(route('employees.update-allowances', props.employee.id), {
        onSuccess: () => {
            emit('updated');
            emit('close');
        },
    });
};
</script>

<template>
    <Modal :show="show" @close="$emit('close')" max-width="xl">
        <div class="p-6">
            <div class="mb-6">
                <h2 class="text-lg font-bold text-foreground">Update Allowances</h2>
                <p class="text-sm text-muted-foreground">Employee: {{ employee?.name }}</p>
            </div>

            <form @submit.prevent="updateAllowances" class="space-y-6">
                <div>
                    <InputLabel for="transportation" value="Transportation Allowance" />
                    <input
                        id="transportation"
                        type="text"
                        inputmode="decimal"
                        pattern="[0-9]*\.?[0-9]*"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        :value="form.transportation_allowance"
                        @input="e => handleInput(e, 'transportation_allowance')"
                    />
                </div>

                <div>
                    <InputLabel for="meal" value="Meal Allowance" />
                    <input
                        id="meal"
                        type="text"
                        inputmode="decimal"
                        pattern="[0-9]*\.?[0-9]*"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        :value="form.meal_allowance"
                        @input="e => handleInput(e, 'meal_allowance')"
                    />
                </div>

                <div>
                    <InputLabel for="cola" value="Cost of Living Allowance (COLA)" />
                    <input
                        id="cola"
                        type="text"
                        inputmode="decimal"
                        pattern="[0-9]*\.?[0-9]*"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        :value="form.cola"
                        @input="e => handleInput(e, 'cola')"
                    />
                </div>

                <div class="flex justify-end space-x-2">
                    <SecondaryButton type="button" @click="$emit('close')">Cancel</SecondaryButton>
                    <PrimaryButton type="submit" :disabled="form.processing">Update Allowances</PrimaryButton>
                </div>
            </form>
        </div>
    </Modal>
</template>