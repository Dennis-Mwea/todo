<script lang="ts" setup>
import DropdownLink from "@/Components/DropdownLink.vue";
import Dropdown from "@/Components/Dropdown.vue";
import {Link, router, usePage} from "@inertiajs/vue3";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import Logo from "@/Assets/Icons/Logo.vue";
import type {PropType} from "vue";
import {computed, onBeforeMount, ref} from "vue";
import type {User} from "@/Types/User";
import {ThemeMode} from "@/Types/ThemeMode";
import Light from "@/Assets/Icons/Light.vue";
import Dark from "@/Assets/Icons/Dark.vue";

const theme = ref<ThemeMode | null>(null);
const showingNavigationDropdown = ref(false);
const user = computed<PropType<User | null>>(() => usePage().props.auth.user)
const setTheme = () => {
    if (theme.value == ThemeMode.Dark) {
        document.documentElement.classList.add('dark')
    } else {
        document.documentElement.classList.remove('dark')
    }
}

onBeforeMount(() => {
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        theme.value = ThemeMode.Dark
    } else {
        theme.value = ThemeMode.Light
    }
    setTheme()
})

const logout = () => (router.post(route('logout')));

const toggleDarkTheme = () => {
    theme.value = theme.value == ThemeMode.Light ? ThemeMode.Dark : ThemeMode.Light
    localStorage.theme = theme.value == ThemeMode.Light ? 'light' : 'dark'
    setTheme()
}
</script>

<template>
    <nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
        <!-- Primary Navigation Menu -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <!-- Logo -->
                    <div class="shrink-0 flex items-center">
                        <Link href="/">
                            <Logo class="block h-9 w-auto"/>
                        </Link>
                    </div>

                    <!-- Navigation Links -->
                    <div v-if="user != null" class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                        <NavLink :active="route().current('dashboard')" :href="route('dashboard')">
                            Dashboard
                        </NavLink>

                        <NavLink :active="route().current('tasks.index')" :href="route('tasks.index')">
                            Tasks
                        </NavLink>
                    </div>
                </div>

                <div class="hidden sm:flex sm:items-center sm:ml-6">
                    <button
                        class="text-slate-600 dark:text-slate-200 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5"
                        @click="toggleDarkTheme">
                        <Light v-if="theme == ThemeMode.Dark"/>
                        <Dark v-else/>
                    </button>

                    <!-- Settings Dropdown -->
                    <div class="ml-3 relative">
                        <Dropdown v-if="user != null" align="right" width="48">
                            <template #trigger>
                                <button
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150"
                                    type="button">
                                    {{ user.name }}

                                    <svg class="ml-2 -mr-0.5 h-4 w-4" fill="none" stroke="currentColor"
                                         stroke-width="1.5" viewBox="0 0 24 24"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path d="M19.5 8.25l-7.5 7.5-7.5-7.5" stroke-linecap="round"
                                              stroke-linejoin="round"/>
                                    </svg>
                                </button>
                            </template>

                            <template #content>
                                <!-- Account Management -->
                                <div class="block px-4 py-2 text-xs text-gray-400">
                                    Manage Account
                                </div>

                                <DropdownLink :href="route('profile.show')">
                                    Profile
                                </DropdownLink>

                                <div class="border-t border-gray-200 dark:border-gray-600"/>

                                <!-- Authentication -->
                                <form @submit.prevent="logout">
                                    <DropdownLink as="button">
                                        Log Out
                                    </DropdownLink>
                                </form>
                            </template>
                        </Dropdown>

                        <template v-if="user == null">
                            <Link :href="route('login')"
                                  class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">
                                Log in
                            </Link>

                            <Link :href="route('register')"
                                  class="ml-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">
                                Register
                            </Link>
                        </template>
                    </div>
                </div>

                <!-- Hamburger -->
                <div class="-mr-2 flex items-center sm:hidden">
                    <button
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out"
                        @click="showingNavigationDropdown = ! showingNavigationDropdown">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                :class="{'hidden': showingNavigationDropdown, 'inline-flex': ! showingNavigationDropdown }"
                                d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2"/>
                            <path
                                :class="{'hidden': ! showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Responsive Navigation Menu -->
        <div :class="{'block': showingNavigationDropdown, 'hidden': ! showingNavigationDropdown}" class="sm:hidden">
            <div class="pt-2 pb-3 space-y-1">
                <ResponsiveNavLink v-if="user != null" :active="route().current('dashboard')"
                                   :href="route('dashboard')">
                    Dashboard
                </ResponsiveNavLink>

                <template v-else>
                    <ResponsiveNavLink :active="route().current('login')" :href="route('login')">
                        Login
                    </ResponsiveNavLink>

                    <ResponsiveNavLink :active="route().current('register')" :href="route('register')">
                        Register
                    </ResponsiveNavLink>
                </template>
            </div>

            <!-- Responsive Settings Options -->
            <div v-if="user != null" class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
                <div class="flex items-center px-4 justify-between">
                    <div>
                        <div class="font-medium text-base text-gray-800 dark:text-gray-200">
                            {{ user.name }}
                        </div>
                        <div class="font-medium text-sm text-gray-500">
                            {{ user.email }}
                        </div>
                    </div>

                    <button
                        class="text-slate-600 dark:text-slate-200 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5"
                        @click="toggleDarkTheme">
                        <Light v-if="theme == ThemeMode.Dark"/>
                        <Dark v-else/>
                    </button>
                </div>

                <div class="mt-3 space-y-1">
                    <ResponsiveNavLink :active="route().current('profile.show')"
                                       :href="route('profile.show')">
                        Profile
                    </ResponsiveNavLink>

                    <!-- Authentication -->
                    <form method="POST" @submit.prevent="logout">
                        <ResponsiveNavLink as="button">
                            Log Out
                        </ResponsiveNavLink>
                    </form>
                </div>
            </div>
        </div>
    </nav>
</template>
