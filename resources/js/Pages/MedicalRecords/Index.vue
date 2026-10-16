<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    prontuarios: { type: Object, required: true },
    pacientes: { type: Array, required: true },
});

const formatarData = (data) =>
    data ? new Date(data).toLocaleDateString('pt-BR') : '—';
</script>

<template>
    <Head title="Prontuários" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-zinc-900">
                Prontuários
            </h2>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <!-- Resumo -->
                <div class="grid gap-6 sm:grid-cols-2">
                    <div
                        class="rounded-2xl bg-white p-6 shadow-card ring-1 ring-zinc-900/5"
                    >
                        <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500">
                            Pacientes
                        </p>
                        <p class="mt-2 text-3xl font-semibold tabular-nums text-zinc-900">
                            {{ pacientes.length }}
                        </p>
                    </div>

                    <div
                        class="rounded-2xl bg-white p-6 shadow-card ring-1 ring-zinc-900/5"
                    >
                        <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500">
                            Atendimentos
                        </p>
                        <p class="mt-2 text-3xl font-semibold tabular-nums text-zinc-900">
                            {{ prontuarios.total }}
                        </p>
                    </div>
                </div>

                <!-- Lista -->
                <div
                    class="overflow-hidden rounded-2xl bg-white shadow-card ring-1 ring-zinc-900/5"
                >
                    <div v-if="prontuarios.data.length === 0" class="p-10 text-center">
                        <p class="text-sm text-zinc-500">
                            Nenhum atendimento registrado ainda.
                        </p>
                    </div>

                    <ul v-else class="divide-y divide-zinc-100">
                        <li
                            v-for="registro in prontuarios.data"
                            :key="registro.id"
                            class="p-6 transition duration-150 hover:bg-zinc-50"
                        >
                            <div class="flex flex-wrap items-baseline justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <span
                                        class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-100 text-sm font-semibold uppercase text-brand-800"
                                    >
                                        {{ registro.patient?.name?.charAt(0) ?? '?' }}
                                    </span>
                                    <span class="text-sm font-semibold text-zinc-900">
                                        {{ registro.patient?.name ?? 'Paciente removido' }}
                                    </span>
                                </div>

                                <span class="text-sm tabular-nums text-zinc-500">
                                    {{ formatarData(registro.occurred_at) }}
                                </span>
                            </div>

                            <p
                                v-if="registro.chief_complaint"
                                class="mt-3 text-sm leading-relaxed text-zinc-700"
                            >
                                {{ registro.chief_complaint }}
                            </p>

                            <p
                                v-if="registro.diagnosis"
                                class="mt-1 text-sm leading-relaxed text-zinc-500"
                            >
                                <span class="font-medium text-zinc-600">Diagnóstico:</span>
                                {{ registro.diagnosis }}
                            </p>
                        </li>
                    </ul>
                </div>

                <!-- Paginação -->
                <nav
                    v-if="prontuarios.links.length > 3"
                    class="flex flex-wrap items-center gap-1"
                >
                    <template v-for="(link, i) in prontuarios.links" :key="i">
                        <span
                            v-if="!link.url"
                            class="px-3 py-2 text-sm text-zinc-400"
                            v-html="link.label"
                        />
                        <a
                            v-else
                            :href="link.url"
                            class="rounded-lg px-3 py-2 text-sm transition duration-150"
                            :class="
                                link.active
                                    ? 'bg-brand-600 font-semibold text-white'
                                    : 'text-zinc-600 hover:bg-zinc-100'
                            "
                            v-html="link.label"
                        />
                    </template>
                </nav>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
