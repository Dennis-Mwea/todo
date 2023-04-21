<script lang="ts" setup>
import DropdownLink from "@/Components/DropdownLink.vue";
import {router} from "@inertiajs/vue3";
import PaginationComponent from "@/Components/PaginationComponent.vue";
import Dropdown from "@/Components/Dropdown.vue";
import Checkbox from "@/Components/Checkbox.vue";
import type {Status} from "@/Types/Status";
import type {Task} from "@/Types/Task";
import {ref} from "vue";
import type {Pagination} from "@/Types/Pagination";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import ConfirmDeleteModalComponent from "@/PageComponents/Tasks/Modals/ConfirmDeleteModalComponent.vue";

const props = defineProps<{
    tasks: Pagination<Task>,
    statuses: Array<Status>,
}>()

let selected = ref<Array<number>>([])
const confirmingTaskDeletion = ref(false);
const taskToDelete = ref<number | null>(null);
const childRef = ref<InstanceType<typeof ConfirmDeleteModalComponent> | null>(null);

defineEmits(['createOrEdit'])

const check = (optionId, checked) => {
    if (checked) {
        selected.value.push(optionId)
    } else {
        selected.value.splice(selected.value.indexOf(optionId), 1)
    }
}

const selectAll = (checked) => {
    selected.value = []
    if (checked) {
        selected.value = props.tasks.data.map(task => task.id)
    }
}

const changeStatus = (status: number) => {
    router.post(route('tasks.progress'), {
        status: status,
        tasks: selected.value,
    });
}

const getTasks = ({page = 1, perPage = 1}) => {
    router.get(route('dashboard', {page, perPage}));
}

const confirmTaskDeletion = (task: number) => {
    taskToDelete.value = task
    confirmingTaskDeletion.value = true

    setTimeout(() => childRef.value!.focusToInput(), 250);
};

const closeModal = () => {
    taskToDelete.value = null
    confirmingTaskDeletion.value = false
};
</script>

<template>
    <div class="">
        <table class="border-collapse table-auto w-full text-sm">
            <thead>
            <tr>
                <th class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                    <Checkbox id="select-all" :checked="selected.length === tasks.data.length"
                              name="select_all" @update:checked="selectAll($event)"/>
                </th>

                <th class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                    Name
                </th>

                <th class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                    Description
                </th>

                <th class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                    <span v-if="selected.length === 0" class="">Status</span>

                    <Dropdown v-else align="right">
                        <template #trigger>
                                    <span class="cursor-pointer flex items-center">
                                        Status

                                        <svg class="ml-2 -mr-0.5 h-4 w-4" fill="none" stroke="currentColor"
                                             stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M19.5 8.25l-7.5 7.5-7.5-7.5" stroke-linecap="round"
                                                  stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                        </template>

                        <template #content>
                            <DropdownLink v-for="(status) in statuses" :key="`status-${status.id}`" as="button"
                                          @click="changeStatus(status.id)">
                                {{ status.name }}
                            </DropdownLink>
                        </template>
                    </Dropdown>
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
                        <Checkbox :id="`select-${task.id}-${index}`" :checked="selected.includes(task.id)"
                                  name="selected" @update:checked="check(task.id, $event)"/>
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
                                        <path
                                            d="M6 10c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm12 0c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm-6 0c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"
                                            fill="currentColor"/>
                                    </svg>
                                </div>
                            </template>

                            <template #content>
                                <DropdownLink as="button">View</DropdownLink>

                                <div class="border-t border-gray-200 dark:border-gray-600"/>

                                <DropdownLink as="button" @click.prevent="$emit('createOrEdit', task)">
                                    Edit
                                </DropdownLink>

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
                        <PaginationComponent :data="tasks" @page="getTasks"/>
                    </td>
                </tr>
            </template>

            <tr v-else>
                <td class="text-center text-gray-800 dark:text-gray-200 py-16" colspan="5">
                    <p>No tasks available for you.</p>

                    <PrimaryButton class="mt-5" @click="$emit('createOrEdit')">
                        Create
                    </PrimaryButton>
                </td>
            </tr>
            </tbody>
        </table>

        <!-- Delete Account Confirmation Modal -->
        <ConfirmDeleteModalComponent ref="childRef" :show="confirmingTaskDeletion" :task="taskToDelete"
                                     @close="closeModal"/>

    </div>
</template>
