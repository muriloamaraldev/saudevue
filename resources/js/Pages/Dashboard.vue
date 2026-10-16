<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-zinc-900">
                Dashboard
            </h2>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <!-- Saudação -->
                <div
                    class="overflow-hidden rounded-2xl bg-white p-8 shadow-card-md ring-1 ring-zinc-900/5"
                >
                    <div class="flex items-start gap-4">
                        <span
                            class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-brand-100 text-base font-semibold uppercase text-brand-800"
                        >
                            {{ $page.props.auth.user.name.charAt(0) }}
                        </span>

                        <div class="min-w-0">
                            <h3
                                class="text-lg font-semibold tracking-tight text-zinc-900"
                            >
                                Welcome back, {{ $page.props.auth.user.name }}
                            </h3>
                            <p class="mt-1 text-sm leading-relaxed text-zinc-500">
                                You are signed in. Your workspace is ready.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Módulos -->
                <div class="grid gap-6 sm:grid-cols-2">
                    <div
                        class="rounded-2xl bg-white p-6 shadow-card ring-1 ring-zinc-900/5"
                    >
                        <div
                            class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-brand-50 text-brand-700 ring-1 ring-brand-200"
                        >
                            <svg
                                class="h-5 w-5"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            </svg>
                        </div>

                        <h4 class="mt-4 font-semibold text-zinc-900">
                            Patients
                        </h4>
                        <p class="mt-1 text-sm leading-relaxed text-zinc-500">
                            Register and manage the patients of your practice.
                        </p>

                        <span
                            class="mt-4 inline-flex items-center gap-1.5 rounded-full bg-zinc-100 px-2.5 py-1 text-xs font-medium text-zinc-600"
                        >
                            <span class="h-1.5 w-1.5 rounded-full bg-zinc-400" />
                            Coming soon
                        </span>
                    </div>

                    <div
                        class="rounded-2xl bg-white p-6 shadow-card ring-1 ring-zinc-900/5"
                    >
                        <div
                            class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-brand-50 text-brand-700 ring-1 ring-brand-200"
                        >
                            <svg
                                class="h-5 w-5"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                <path d="M14 2v6h6" />
                                <path d="M8 13h8" />
                                <path d="M8 17h5" />
                            </svg>
                        </div>

                        <h4 class="mt-4 font-semibold text-zinc-900">
                            Medical Records
                        </h4>
                        <p class="mt-1 text-sm leading-relaxed text-zinc-500">
                            Keep the clinical history of each patient.
                        </p>

                        <span
                            class="mt-4 inline-flex items-center gap-1.5 rounded-full bg-zinc-100 px-2.5 py-1 text-xs font-medium text-zinc-600"
                        >
                            <span class="h-1.5 w-1.5 rounded-full bg-zinc-400" />
                            Coming soon
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
