<script lang="ts" setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import {Link} from "@inertiajs/vue3";
import Next from "@/Assets/Images/Next.vue";
import WelcomeImage from "@/Assets/Images/WelcomeImage.png"
</script>

<template>
    <AppLayout title="Welcome">
        <div class="max-w-7xl mx-auto p-4 sm:px-6 lg:px-8">
            <div class="flex flex-col min-h-[calc(100vh_-_65px)] h-full">
                <div class="flex-grow flex items-center">
                    <div class="flex w-full flex-wrap sm:flex-nowrap">
                        <div class="flex-grow basis-auto sm:basis-0 mt-6 sm:mt-0">
                            <h1 class="font-bold text-[3.5rem] leading-[4.375rem] text-black dark:text-slate-200 mb-4">
                                My Todo App better than yours
                            </h1>

                            <p class="text-xl text-slate-600 dark:text-slate-200 mb-8">Lorem ipsum dolor sit amet,
                                consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna
                                aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut
                                aliquip ex ea commodo consequat.</p>

                            <Link v-if="$page.props.auth.user == null" :href="route('register')"
                                  class="float-left flex items-center space-x-2 px-4 py-2 bg-blue-600 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                Get Started

                                <Next class="ms-2"/>
                            </Link>
                        </div>

                        <div class="flex-grow basis-auto sm:basis-0 flex items-center justify-center">
                            <img :src="WelcomeImage" alt="Welcome Image">
                        </div>
                    </div>
                </div>

                <div class="my-4 sm:my-10 flex flex-col-reverse sm:flex-row justify-between items-center">
                    <p class="text-slate-600 dark:text-slate-200 mt-4 sm:mt-0">© 2023 Todo App</p>

                    <div class="flex space-x-2 text-blue-600 dark:text-blue-300 items-center">
                        <Link href="/">Terms & Conditions</Link>
                        <span>.</span>
                        <Link href="/">Privacy Policy</Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
