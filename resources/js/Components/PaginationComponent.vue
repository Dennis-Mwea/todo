<script lang="ts" setup>
import {computed, ref} from 'vue'

const props = defineProps<{
    data: Pagination<Task>,
    maxVisibleButtons?: {
        type: Number,
        default: 10
    }
}>()

const pages = computed(() => {
    const pages = []
    for (
        let i = 1;
        i <= Math.min(props.data.current_page + props.maxVisibleButtons - 1, props.data.last_page);
        i++
    ) {
        pages.push({
            page: i,
            label: i,
            active: i === props.data.current_page
        })
    }
    return pages
})

const emit = defineEmits(['page'])

const goToPage = (page) => {
    if (page !== props.data.current_page && page > 0 && page <= props.data.last_page) {
        if (page) {
            emit('page', {
                page: page,
                perPage: props.data.per_page
            })
        }
    }
}

const perPage = ref(props.data.per_page)

const changePerPage = () => {
    emit('page', {
        page: props.data.current_page,
        perPage: perPage.value
    })
}
</script>

<template>
    <div class="flex flex-col md:flex-row items-center justify-center md:justify-between py-3 px-4">
        <div class="flex items-center">
            <p>
                Items Per Page
                <select v-model="perPage" class="border-slate-200 ml-1 py-1 text-primary-400" @change="changePerPage">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </p>

            <p class="opacity-80 border-l ml-2 pl-3 md:mb-0">
                Showing
                <span class="font-medium">{{ data.from }}</span>
                -
                <span class="font-medium">{{ data.to }}</span>
                of
                <span class="font-medium">{{ data.total }}</span>
            </p>
        </div>
        <div class="flex items-center -space-x-px">
            <button :class="{'disabled cursor-not-allowed opacity-50': data.current_page === 1}"
                    class="px-4 py-1 mr-1 bg-slate-100 text-slate-600 flex items-center transition"
                    @click="goToPage(data.current_page - 1)">
                <svg height="24" viewBox="0 0 24 24" width="24">
                    <path d="M15.41 7.41L14 6l-6 6l6 6l1.41-1.41L10.83 12z" fill="currentColor"/>
                </svg>
                Previous
            </button>
            <div v-for="(link, i) in pages" :key="i" class="flex justify-center items-center">
                <button :class="link.active ? 'bg-primary-400 text-white hover:text-white' : 'bg-slate-100 '"
                        class="mx-1 h-8 w-8 flex items-center justify-center transition"
                        @click="goToPage(link.label)">
                    <span v-html="link.label"></span>
                </button>
            </div>
            <button :class="{'disabled cursor-not-allowed opacity-50': data.current_page === data.last_page}"
                    class="px-4 py-1 ml-1 bg-slate-100 flex text-slate-600 items-center transition"
                    @click="goToPage(data.current_page + 1)">
                Next
                <svg height="24" viewBox="0 0 24 24" width="24">
                    <path d="M10 6L8.59 7.41L13.17 12l-4.58 4.59L10 18l6-6z" fill="currentColor"/>
                </svg>
            </button>
        </div>
    </div>
</template>
