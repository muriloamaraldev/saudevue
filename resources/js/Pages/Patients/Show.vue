<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    paciente: { type: Object, required: true },
});

const confirmandoExclusao = ref(false);

const excluir = () => {
    router.delete(route('patients.destroy', props.paciente.id), {
        onFinish: () => (confirmandoExclusao.value = false),
    });
};

const formatarData = (data) =>
    data ? new Date(data + 'T00:00:00').toLocaleDateString('pt-BR') : '—';
</script>

<template>
    <Head :title="paciente.name" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-wrap items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-zinc-900">
                    {{ paciente.name }}
                </h2>

                <div class="flex items-center gap-3">
                    <Link
                        :href="route('patients.edit', paciente.id)"
                        class="inline-flex items-center gap-2 rounded-lg border border-zinc-300 bg-white px-4 py-2 text-sm font-semibold text-zinc-700 shadow-sm transition duration-150 hover:border-zinc-400 hover:bg-zinc-50"
                    >
                        Editar
                    </Link>

                    <DangerButton
                        v-if="!confirmandoExclusao"
                        @click="confirmandoExclusao = true"
                    >
                        Excluir
                    </DangerButton>

                    <template v-else>
                        <SecondaryButton @click="confirmandoExclusao = false">
                            Cancelar
                        </SecondaryButton>
                        <DangerButton @click="excluir">
                            Confirmar exclusão
                        </DangerButton>
                    </template>
                </div>
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

                <!-- Ficha -->
                <div
                    class="rounded-2xl bg-white p-6 shadow-card ring-1 ring-zinc-900/5 sm:p-8"
                >
                    <h3 class="font-semibold text-zinc-900">Dados cadastrais</h3>

                    <dl class="mt-5 grid gap-x-8 gap-y-5 sm:grid-cols-3">
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-500">CPF</dt>
                            <dd class="mt-1 text-sm tabular-nums text-zinc-900">
                                {{ paciente.cpf_formatted }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Nascimento</dt>
                            <dd class="mt-1 text-sm text-zinc-900">
                                {{ formatarData(paciente.birth_date) }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Telefone</dt>
                            <dd class="mt-1 text-sm text-zinc-900">
                                {{ paciente.phone ?? '—' }}
                            </dd>
                        </div>
                        <div class="sm:col-span-3">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-500">E-mail</dt>
                            <dd class="mt-1 text-sm text-zinc-900">
                                {{ paciente.email ?? '—' }}
                            </dd>
                        </div>
                        <div class="sm:col-span-3">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Observações</dt>
                            <dd class="mt-1 whitespace-pre-line text-sm leading-relaxed text-zinc-700">
                                {{ paciente.notes ?? '—' }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Prontuários -->
                <div
                    class="rounded-2xl bg-white p-6 shadow-card ring-1 ring-zinc-900/5 sm:p-8"
                >
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-zinc-900">Prontuários</h3>
                        <span
                            class="rounded-full bg-zinc-100 px-2.5 py-1 text-xs font-medium text-zinc-600"
                        >
                            {{ paciente.medical_records.length }}
                        </span>
                    </div>

                    <p
                        v-if="paciente.medical_records.length === 0"
                        class="mt-4 text-sm text-zinc-500"
                    >
                        Nenhum atendimento registrado para este paciente.
                    </p>

                    <ul v-else class="mt-5 divide-y divide-zinc-100">
                        <li
                            v-for="registro in paciente.medical_records"
                            :key="registro.id"
                            class="py-4"
                        >
                            <div class="flex items-baseline justify-between gap-4">
                                <span class="text-sm font-medium text-zinc-900">
                                    {{ formatarData(registro.occurred_at) }}
                                </span>
                                <span class="text-xs text-zinc-500">
                                    {{ registro.created_at }}
                                </span>
                            </div>

                            <p
                                v-if="registro.chief_complaint"
                                class="mt-1 text-sm text-zinc-700"
                            >
                                {{ registro.chief_complaint }}
                            </p>

                            <p
                                v-if="registro.diagnosis"
                                class="mt-1 text-sm text-zinc-500"
                            >
                                <span class="font-medium text-zinc-600">Diagnóstico:</span>
                                {{ registro.diagnosis }}
                            </p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
