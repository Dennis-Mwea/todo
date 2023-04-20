<script setup lang="ts">
import {computed, watch} from 'vue';
import {Head, usePage} from '@inertiajs/vue3';
import Banner from '@/Components/Banner.vue';
import flasher, {FlasherResponse} from "@flasher/flasher";
import NavBar from "@/Layouts/NavBar.vue";

defineProps<{
    title: String,
}>();

const messages = computed(() => usePage().props.messages)

flasher.render(messages)
watch(messages, (value: FlasherResponse) => flasher.render(value))
</script>

<template>
    <div>
        <Head :title="title" />

        <Banner />

        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            <NavBar />

            <!-- Page Heading -->
            <header v-if="$slots.header" class="bg-white dark:bg-gray-800 shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
