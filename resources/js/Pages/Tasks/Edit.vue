<script lang="ts" setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import {useForm} from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import TextAreaInput from "@/Components/TextAreaInput.vue";
import SelectInput from "@/Components/SelectInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";

const props = defineProps<{
    task: Task,
    status?: String,
    statuses: Array<Status>
}>()

const form = useForm({
    name: props.task.name,
    due_date: props.task.due_date,
    status_id: props.task.status_id,
    description: props.task.description,
});

const submit = () => {
    form.put(route('tasks.update', props.task.id), <Partial<VisitOptions>>{
        onSuccess: () => form.reset(),
        onBefore: () => form.clearErrors(),
    });
}
</script>

<template>
    <AppLayout title="Todos">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Edit Task
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
                <div v-if="status" class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                    {{ status }}
                </div>

                <form @submit.prevent="submit">
                    <div>
                        <InputLabel for="name" value="Name"/>
                        <TextInput id="name"
                                   v-model="form.name"
                                   autofocus
                                   class="mt-1 block w-full"
                                   required
                                   type="text"/>
                        <InputError :message="form.errors.name" class="mt-2"/>
                    </div>

                    <div class="mt-4">
                        <InputLabel for="description" value="Description"/>
                        <TextAreaInput id="description"
                                       v-model="form.description"
                                       class="mt-1 block w-full"
                                       required
                                       rows="3"/>
                        <InputError :message="form.errors.description" class="mt-2"/>
                    </div>

                    <div class="mt-4">
                        <InputLabel for="status" value="Status"/>

                        <SelectInput id="status"
                                     v-model="form.status_id"
                                     class="mt-1 block w-full"
                                     required>
                            <option>-</option>

                            <option v-for="status in statuses" :key="`status-${status.id}`" :value="status.id">
                                {{ status.name }}
                            </option>
                        </SelectInput>

                        <InputError :message="form.errors.status_id" class="mt-2"/>
                    </div>

                    <div class="mt-4">
                        <InputLabel for="due_date" value="DueDate"/>

                        <TextInput id="due_date"
                                   v-model="form.due_date"
                                   class="mt-1 block w-full"
                                   required
                                   type="date"/>

                        <InputError :message="form.errors.due_date" class="mt-2"/>
                    </div>

                    <div class="flex items-center justify-end mt-8">
                        <PrimaryButton :class="{'opacity-25': form.processing}" :disabled="form.processing"
                                       class="ml-4">
                            Update Task
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
