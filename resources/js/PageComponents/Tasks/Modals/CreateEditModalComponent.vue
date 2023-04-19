<script lang="ts" setup>
import Modal from "@/Components/Modal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import {useForm} from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SelectInput from "@/Components/SelectInput.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import TextAreaInput from "@/Components/TextAreaInput.vue";
import {ref} from "vue";
import type {Status} from "@/Types/Status";
import type {Task} from "@/Types/Task";

const props = defineProps<{
    show: Boolean,
    statuses: Array<Status>,
}>()
const emit = defineEmits(['closeCreate']);
const taskToEdit = ref<number | null>(null)

const form = useForm({
    name: '',
    due_date: '',
    status_id: '',
    description: '',
})

const close = () => {
    taskToEdit.value = null
    form.reset()
    form.clearErrors()
    emit('closeCreate')
};

const editTask = (task: Task) => {
    taskToEdit.value = task.id
    form.name = task.name
    form.status_id = task.status_id
    form.description = task.description
    form.due_date = task.formatted_due_date
}

const submit = () => {
    const options = <Partial<VisitOptions>>{
        onBefore: () => form.clearErrors(),
        onSuccess: () => {
            form.reset()
            close()
        },
    }

    if (taskToEdit.value != null) {
        form.put(route('tasks.update', taskToEdit.value), options);
    } else {
        form.post(route('tasks.store'), options);
    }
}

defineExpose({editTask})
</script>

<template>
    <Modal :show="show" @close="close">
        <div class="px-6 py-4">
            <div class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Create Task
            </div>

            <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
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
                </form>
            </div>
        </div>

        <div class="flex flex-row justify-end px-6 py-4 bg-gray-100 dark:bg-gray-800 text-right">
            <SecondaryButton :class="{ 'opacity-25': form.processing }"
                             :disabled="form.processing"
                             @click="close">Cancel
            </SecondaryButton>

            <PrimaryButton :class="{ 'opacity-25': form.processing }"
                           :disabled="form.processing"
                           class="ml-3"
                           @click="submit">
                Save Task
            </PrimaryButton>
        </div>
    </Modal>
</template>
