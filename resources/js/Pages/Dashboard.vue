<script lang="ts" setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Search from "@/Assets/Icons/Search.vue";
import {router} from "@inertiajs/vue3";
import {onMounted, ref, watch} from "vue";
import Settings from "@/Assets/Icons/Settings.vue";
import {Chart} from "chart.js/auto";
import type {ChartConfiguration, ChartItem} from "chart.js"
import type {Status} from "@/Types/Status";
import KanbanView from "@/Assets/Icons/KanbanView.vue";
import {DisplayMode} from "@/Types/DisplayMode";
import TableView from "@/Assets/Icons/TableView.vue";
import TableMode from "@/PageComponents/Tasks/TableMode.vue";
import type {Pagination} from "@/Types/Pagination";
import type {Task} from "@/Types/Task";
import Add from "@/Assets/Icons/Add.vue";
import CreateEditModalComponent from "@/PageComponents/Tasks/Modals/CreateEditModalComponent.vue";
import ConfirmDeleteModalComponent from "@/PageComponents/Tasks/Modals/ConfirmDeleteModalComponent.vue";
import KanbanMode from "@/PageComponents/Tasks/KanbanMode.vue";

const props = defineProps<{
    tasks: Pagination<Task>,
    statuses: Array<Status>,
    chartData: Array<number>,
}>()

const search = ref<string | null>(null)
const chartElement = ref<ChartItem | null>(null)
const chart = ref<any>(null)
const mode = ref<DisplayMode>(DisplayMode.Kanban)
const creatingOrEditingTask = ref<boolean>(false);
const createEditTask = ref<InstanceType<typeof ConfirmDeleteModalComponent> | null>(null);
const config = <ChartConfiguration>{
    type: 'doughnut',
    data: {
        labels: props.statuses.map(status => status.name),
        datasets: [{
            hoverOffset: 4,
            label: 'Todos Chart',
            data: props.chartData,
            backgroundColor: props.statuses.map(status => status.color),
        }]
    },
    options: {
        plugins: {
            legend: {
                position: 'bottom',
            }
        }
    }
}

onMounted(() => {
    chart.value = new Chart(chartElement.value, config);
})

watch(() => props.chartData, () => {
    chart.value?.destroy()
    chart.value = new Chart(chartElement.value, config);
})

const searchTodos = () => (router.get(route('tasks.index'), {search: search.value}, {
    preserveState: true,
    preserveScroll: true,
}))

const changeView = (view: DisplayMode) => {
    mode.value = view
}

const createOrEditTask = (task: Task | null) => {
    if (task != null) {
        createEditTask.value?.editTask(task)
    }
    creatingOrEditingTask.value = true
};

const closeEditModal = () => (creatingOrEditingTask.value = false)
</script>

<template>
    <AppLayout title="Dashboard">
        <div class="py-10 max-w-7xl mx-auto p-4 sm:px-6 lg:px-8">
            <div class="flex w-full mb-10">
                <div class="relative flex-grow mr-0 sm:mr-4 text-slate-600 dark:text-slate-200">
                    <Search class="absolute top-[.6875rem] left-2"/>

                    <TextInput v-model="search" class="w-full pl-9" placeholder="Search todos..." type="text"
                               @update:model-value="searchTodos"/>
                </div>

                <PrimaryButton class="hidden sm:flex items-center flex-nowrap" @click="createOrEditTask">
                    <Add class="mr-1"/>
                    New Todo
                </PrimaryButton>
            </div>

            <div class="flex flex-wrap sm:flex-nowrap w-full space-x-0 sm:space-x-6 mb-6">
                <div
                    class="bg-white dark:bg-gray-900 flex-grow basis-auto sm:basis-2/5 p-4 rounded-2xl border border-slate-300 dark:border-slate-600">
                    <div class="flex justify-between items-center mb-4">
                        <h1 class="text-black dark:text-slate-200 font-bold text-xl">Progress</h1>

                        <button
                            class="text-slate-600 dark:text-slate-200 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-0">
                            <Settings/>
                        </button>
                    </div>

                    <div class="chart relative w-full h-full">
                        <canvas ref="chartElement"/>
                    </div>
                </div>

                <div
                    class="mt-6 sm:mt-0 bg-white dark:bg-gray-900 flex-grow basis-auto sm:basis-3/5 p-4 rounded-2xl border border-slate-300 dark:border-slate-600">
                    <h1 class="text-black dark:text-slate-200 font-bold text-xl mb-1">My Calendar</h1>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-800 flex-grow basis-2/5 p-4 rounded-2xl border border-slate-300 dark:border-slate-600">
                <div class="flex justify-between items-center mb-8">
                    <h1 class="text-black dark:text-slate-200 font-bold text-xl">My Todos</h1>

                    <div class="flex space-x-3">
                        <button v-for="viewType in [DisplayMode.Kanban, DisplayMode.Table]" :class="{
                            'bg-blue-600 dark:bg-gray-200 text-white dark:text-gray-800': mode == viewType,
                            'text-slate-600 dark:text-slate-200': mode != viewType,
                        }"
                                class="hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded text-sm p-1"
                                @click.prevent="changeView(viewType)">
                            <KanbanView v-if="viewType == DisplayMode.Kanban"/>

                            <TableView v-else/>
                        </button>
                    </div>
                </div>

                <div class="w-full overflow-x-auto">
                    <TableMode v-if="mode == DisplayMode.Table" :statuses="statuses" :tasks="tasks" @create-or-edit="createOrEditTask($event)"/>

                    <KanbanMode v-else :statuses="statuses" :tasks="tasks" />
                </div>
            </div>
        </div>

        <CreateEditModalComponent ref="createEditTask" :show="creatingOrEditingTask" :statuses="statuses"
                                  @close-create="closeEditModal"/>
    </AppLayout>
</template>
