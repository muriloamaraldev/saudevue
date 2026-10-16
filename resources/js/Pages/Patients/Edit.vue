<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PatientForm from './Partials/PatientForm.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    paciente: { type: Object, required: true },
});

const form = useForm({
    name: props.paciente.name,
    cpf: props.paciente.cpf,
    birth_date: props.paciente.birth_date,
    phone: props.paciente.phone ?? '',
    email: props.paciente.email ?? '',
    notes: props.paciente.notes ?? '',
});

const submit = () => form.put(route('patients.update', props.paciente.id));
</script>

<template>
    <Head :title="`Editar ${paciente.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-zinc-900">
                Editar paciente
            </h2>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div
                    class="rounded-2xl bg-white p-6 shadow-card ring-1 ring-zinc-900/5 sm:p-8"
                >
                    <PatientForm
                        :form="form"
                        rotulo-submit="Salvar alterações"
                        @submit="submit"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
