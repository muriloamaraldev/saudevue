<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="relative flex min-h-screen flex-col items-center justify-center bg-brand-glow px-4 py-12 sm:px-6 lg:px-8"
    >
        <div class="w-full sm:max-w-md">
            <Link
                href="/"
                class="mb-8 flex flex-col items-center gap-3 rounded-xl transition duration-150 hover:opacity-90"
            >
                <ApplicationLogo class="h-14 w-14" />

                <span class="text-center text-lg font-semibold tracking-tight text-zinc-900">
                    SaúdeVue
                </span>
            </Link>

            <div
                class="rounded-2xl bg-white p-8 shadow-card-lg ring-1 ring-zinc-900/5"
            >
                <slot />
            </div>
        </div>
    </div>
</template>
