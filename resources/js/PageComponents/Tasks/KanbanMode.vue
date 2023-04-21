<template>
    <div class="flex space-x-8 ms-4">
        <div v-for="(status, index) in statuses" :key="`columns-${status.id}`"
             class="w-[21.875rem] min-w-[21.875rem] rounded-md min-h-3/4 bg-slate-50 dark:bg-gray-900">
            <div class="p-6">
                <h3 class="text-black dark:text-slate-200 font-bold text-xl mb-1">{{ status.name }}</h3>
            </div>

            <div class="tasks h-[calc(100vh_-_332px)] overflow-y-auto ms-6 mb-4">
                <Draggable v-model="filteredTasks[index]" class="list-group h-full"
                           group="tasks" item-key="status_id" @change="logChangeEvent">
                    <template #item="{element}">
                        <div
                            class="item rounded bg-white dark:bg-gray-800 p-3 me-6 mb-4 list-group-item cursor-move">
                            <h3 class="text-black dark:text-slate-200 font-bold text-xl mb-1">
                                {{ element.name }}
                            </h3>

                            <p class="text-slate-400 dark:text-slate-200">{{ element.description }}</p>
                        </div>
                    </template>
                </Draggable>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import * as Draggable from "vuedraggable";
import {onBeforeMount, ref, watch} from "vue";
import {router} from "@inertiajs/vue3";
import type {Pagination} from "@/Types/Pagination";
import type {Task} from "@/Types/Task";
import type {Status} from "@/Types/Status";

const props = defineProps<{
    tasks: Pagination<Task>,
    statuses: Array<Status>,
}>()

let filteredTasks = ref<Array<Array<Task>>>([])

onBeforeMount(() => {
    props.statuses.forEach(status => (filteredTasks.value.push(filterTasks(status))))
})

watch(() => props.tasks, () => props.statuses.forEach(status => (filteredTasks.value.push(filterTasks(status)))))

const filterTasks = (status: Status): Array<Task> => props.tasks.data.filter(task => task.status_id === status.id)

const logChangeEvent = ({added, removed}) => {
    if (added) {
        const index = filteredTasks.value.findIndex(tasks => tasks.some(task => task.id === added.element.id))
        router.post(route('tasks.progress'), {
            status: index + 1,
            tasks: [added.element.id],
        }, {
            preserveState: true,
            preserveScroll: true,
        })
    }
}
</script>
