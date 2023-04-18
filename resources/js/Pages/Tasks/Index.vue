<script lang="ts" setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import {computed, ref} from "vue";
import {Link, useForm} from "@inertiajs/vue3";
import Checkbox from "@/Components/Checkbox.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import DangerButton from "@/Components/DangerButton.vue";
import DialogModal from "@/Components/DialogModal.vue";
import InputError from "@/Components/InputError.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";

defineProps<{
	tasks: Pagination<Task>,
}>()

const selected = computed<Array<number>>(() => [])
const confirmingTaskDeletion = ref(false);
const taskToDelete = ref<number|null>(null);
const passwordInput = ref<HTMLElement|null>(null);
const form = useForm({
	password: '',
});

const confirmTaskDeletion = (task: number) => {
    taskToDelete.value = task
    confirmingTaskDeletion.value = true

    setTimeout(() => passwordInput.value!.focus(), 250);
};

const closeModal = () => {
    form.reset()
    taskToDelete.value = null
    confirmingTaskDeletion.value = false
};

const deleteTask = () => {
    if (taskToDelete.value != null) {
        form.delete(route('tasks.destroy', taskToDelete.value), <Partial<VisitOptions>>{
            preserveScroll: true,
            onFinish: () => form.reset(),
            onSuccess: () => closeModal(),
            onError: () => passwordInput.value!.focus(),
        });
    }
}
</script>

<template>
    <AppLayout title="Todos">
        <template #header>
            <div class="flex justify-between">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Tasks
                </h2>

                <Link :href="route('tasks.create')"
                      class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    Create Task
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <table class="border-collapse table-auto w-full text-sm">
                    <thead>
                    <tr>
                        <th class="">
                            <Checkbox v-model:checked="selected" name="selected"/>
                        </th>

                        <th class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                            Name
                        </th>

                        <th class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                            Description
                        </th>

                        <th class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                            Status
                        </th>

                        <th class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                            Actions
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-slate-800">
                    <template v-if="tasks.data.length > 0">
                        <tr v-for="(task, index) in tasks.data" :key="`task-${task.id}-${index}`">
                            <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                                <Checkbox v-model:checked="selected" name="selected"/>
                            </td>

                            <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                                {{ task.name }}
                            </td>

                            <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                                {{ task.description }}
                            </td>

                            <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                                {{ task.status.name }}
                            </td>

                            <td class="border-b border-slate-100 dark:border-slate-700 p-4 pr-8 text-slate-500 dark:text-slate-400">
                                <Dropdown align="right">
                                    <template #trigger>
                                        <div class="p-3 cursor-pointer">
                                            <svg class="ms-auto" height="24px" viewBox="0 0 24 24"
                                                 width="24px" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M6 10c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm12 0c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm-6 0c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"
                                                      fill="currentColor"/>
                                            </svg>
                                        </div>
                                    </template>

                                    <template #content>
                                        <DropdownLink as="button">View</DropdownLink>

                                        <div class="border-t border-gray-200 dark:border-gray-600"/>

                                        <DropdownLink :href="route('tasks.edit', task.id)">Edit</DropdownLink>

                                        <div class="border-t border-gray-200 dark:border-gray-600"/>

                                        <DropdownLink as="button" @click.prevent="confirmTaskDeletion(task.id)">
                                            Delete
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </td>
                        </tr>

                        <tr>
                            <td class="text-end border-b border-slate-100 dark:border-slate-700 p-4 pr-8 text-slate-500 dark:text-slate-400"
                                colspan="5">
                                Pagination here
                            </td>
                        </tr>
                    </template>

                    <tr v-else>
                        <td class="text-center text-gray-800 dark:text-gray-200 py-16" colspan="5">
                            <p>No tasks available for you.</p>

                            <Link :href="route('tasks.create')"
                                  class="mt-5 inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                Create
                            </Link>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Delete Account Confirmation Modal -->
        <DialogModal :show="confirmingTaskDeletion" @close="closeModal">
            <template #title>
                Delete Task
            </template>

            <template #content>
                Are you sure you want to delete This task?

                <div class="mt-4">
                    <TextInput ref="passwordInput"
                               v-model="form.password"
                               autocomplete="current-password"
                               class="mt-1 block w-3/4"
                               placeholder="Password"
                               type="password"
                               @keyup.enter="deleteTask"/>

                    <InputError :message="form.errors.password" class="mt-2"/>
                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="closeModal">Cancel</SecondaryButton>

                <DangerButton
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        class="ml-3"
                        @click="deleteTask">
                    Delete Task
                </DangerButton>
            </template>
        </DialogModal>
    </AppLayout>
</template>
