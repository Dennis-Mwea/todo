<script lang="ts" setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import {ref} from "vue";
import {Link, router} from "@inertiajs/vue3";
import Checkbox from "@/Components/Checkbox.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import PaginationComponent from "@/Components/PaginationComponent.vue";
import ConfirmDeleteModalComponent from "@/PageComponents/Tasks/Modals/ConfirmDeleteModalComponent.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import CreateEditModalComponent from "@/PageComponents/Tasks/Modals/CreateEditModalComponent.vue";
import {DisplayMode} from "@/Types/DisplayMode";
import type {Pagination} from "@/Types/Pagination";
import type {Task} from "@/Types/Task";
import type {Status} from "@/Types/Status";

const props = defineProps<{
    tasks: Pagination<Task>,
    statuses: Array<Status>,
}>()

const confirmingTaskDeletion = ref(false);
const taskToDelete = ref<number | null>(null);
const childRef = ref<InstanceType<typeof ConfirmDeleteModalComponent> | null>(null);
const creatingOrEditingTask = ref<boolean>(false);
const mode = ref<DisplayMode>(DisplayMode.Kanban);
const createEditTask = ref<InstanceType<typeof ConfirmDeleteModalComponent> | null>(null);
let selected = ref<Array<number>>([])

const confirmTaskDeletion = (task: number) => {
    taskToDelete.value = task
    confirmingTaskDeletion.value = true

    setTimeout(() => childRef.value!.focusToInput(), 250);
};

const closeModal = () => {
    taskToDelete.value = null
    confirmingTaskDeletion.value = false
};

const createOrEditTask = (task: Task | null) => {
    if (task != null) {
        createEditTask.value?.editTask(task)
    }
    creatingOrEditingTask.value = true
};

const closeEditModal = () => {
    creatingOrEditingTask.value = false
}

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

const getTasks = ({page = 1, perPage = 1}) => {
    router.get(route('tasks.index', {page, perPage}));
}

const changeStatus = (status: number) => {
    router.post(route('tasks.progress'), {
        status: status,
        tasks: selected.value,
    });
}

const filterTasks = (status: Status) => {
    return props.tasks.data.filter(task => task.status_id === status.id)
}
</script>

<template>
    <AppLayout title="Todos">
        <template #header>
            <div class="flex justify-between">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Tasks
                </h2>

                <PrimaryButton
                    class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                    @click="createOrEditTask">
                    Create Task
                </PrimaryButton>
            </div>
        </template>

        <div class="pt-12">
            <div v-if="mode == DisplayMode.Kanban" class="max-w-full">
                <div class="flex space-x-8 ms-4">
                    <div v-for="status in statuses" :key="`columns-${status.id}`"
                         class="w-[21.875rem] min-w-[21.875rem] rounded-md min-h-[calc(100vh_-_211px)] border dark:border-slate-600">
                        <div class="p-6">
                            <h3 class="text-slate-400 dark:text-slate-200">{{ status.name }}</h3>

                            <hr class="mt-2 mb-6">
                        </div>

                        <div class="tasks max-h-[calc(100vh_-_316px)] overflow-y-auto ms-6">
                            <div v-for="(task, index) in filterTasks(status)"
                                 :key="`filtered-task-${status.id}-${task.id}`"
                                 :class="{'mb-4': filterTasks(status).length !== index}"
                                 class="item rounded shadow border dark:border-slate-600 p-3 me-6">
                                <h2 class="text-slate-400 dark:text-slate-200 font-semibold">{{ task.name }}</h2>

                                <p class="text-slate-400 dark:text-slate-200">{{ task.description }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-12">
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

                                        <DropdownLink as="button" @click.prevent="createOrEditTask(task)">
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
        <ConfirmDeleteModalComponent ref="childRef" :show="confirmingTaskDeletion" :task="taskToDelete"
                                     @close="closeModal"/>

        <CreateEditModalComponent ref="createEditTask" :show="creatingOrEditingTask" :statuses="statuses"
                                  @close-create="closeEditModal"/>
    </AppLayout>
</template>
