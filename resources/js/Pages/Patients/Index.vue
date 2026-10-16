<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { watch } from 'vue';

const props = defineProps({
    pacientes: { type: Object, required: true },
    filtros: { type: Object, required: true },
});

const form = useForm({ search: props.filtros.search ?? '' });

let temporizador;
watch(
    () => form.search,
    (valor) => {
        clearTimeout(temporizador);
        temporizador = setTimeout(() => {
            router.get(
                route('patients.index'),
                { search: valor },
                { preserveState: true, replace: true },
            );
        }, 300);
    },
);

const idade = (nascimento) => {
    if (!nascimento) return '—';
    const hoje = new Date();
    const nasc = new Date(nascimento);
    let anos = hoje.getFullYear() - nasc.getFullYear();
    const m = hoje.getMonth() - nasc.getMonth();
    if (m < 0 || (m === 0 && hoje.getDate() < nasc.getDate())) anos--;
    return `${anos} anos`;
};
</script>

<template>
    <Head title="Pacientes" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-wrap items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-zinc-900">
                    Pacientes
                </h2>

                <Link
                    :href="route('patients.create')"
                    class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition duration-150 hover:bg-brand-700"
                >
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <path d="M12 5v14M5 12h14" />
                    </svg>
                    Novo paciente
                </Link>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div
                    v-if="$page.props.status"
                    class="rounded-lg bg-brand-50 px-4 py-3 text-sm font-medium text-brand-800 ring-1 ring-brand-200"
                >
                    {{ $page.props.status }}
                </div>

                <!-- Busca -->
                <div class="max-w-md">
                    <TextInput
                        v-model="form.search"
                        type="search"
                        placeholder="Buscar por nome ou CPF..."
                    />
                </div>

                <!-- Lista -->
                <div
                    class="overflow-hidden rounded-2xl bg-white shadow-card ring-1 ring-zinc-900/5"
                >
                    <div v-if="pacientes.data.length === 0" class="p-10 text-center">
                        <p class="text-sm text-zinc-500">
                            {{
                                form.search
                                    ? 'Nenhum paciente encontrado para essa busca.'
                                    : 'Você ainda não cadastrou pacientes.'
                            }}
                        </p>
                        <Link
                            :href="route('patients.create')"
                            class="mt-4 inline-flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition duration-150 hover:bg-brand-700"
                        >
                            Cadastrar o primeiro paciente
                        </Link>
                    </div>

                    <table v-else class="min-w-full divide-y divide-zinc-200">
                        <thead class="bg-zinc-50">
                            <tr>
                                <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-zinc-500">
                                    Nome
                                </th>
                                <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-zinc-500">
                                    CPF
                                </th>
                                <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-zinc-500">
                                    Idade
                                </th>
                                <th class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-zinc-500">
                                    Telefone
                                </th>
                                <th class="px-6 py-3" />
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-zinc-100">
                            <tr
                                v-for="paciente in pacientes.data"
                                :key="paciente.id"
                                class="transition duration-150 hover:bg-zinc-50"
                            >
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-100 text-sm font-semibold uppercase text-brand-800"
                                        >
                                            {{ paciente.name.charAt(0) }}
                                        </span>
                                        <span class="text-sm font-medium text-zinc-900">
                                            {{ paciente.name }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm tabular-nums text-zinc-600">
                                    {{ paciente.cpf_formatted }}
                                </td>
                                <td class="px-6 py-4 text-sm text-zinc-600">
                                    {{ idade(paciente.birth_date) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-zinc-600">
                                    {{ paciente.phone ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-end">
                                    <Link
                                        :href="route('patients.show', paciente.id)"
                                        class="text-sm font-medium text-brand-700 transition duration-150 hover:text-brand-900"
                                    >
                                        Ver ficha
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <nav
                    v-if="pacientes.links.length > 3"
                    class="flex flex-wrap items-center gap-1"
                >
                    <template v-for="(link, i) in pacientes.links" :key="i">
                        <span
                            v-if="!link.url"
                            class="px-3 py-2 text-sm text-zinc-400"
                            v-html="link.label"
                        />
                        <Link
                            v-else
                            :href="link.url"
                            preserve-state
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
