<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PatientForm from './Partials/PatientForm.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    cpf: '',
    birth_date: '',
    phone: '',
    email: '',
    notes: '',
});

const submit = () => form.post(route('patients.store'));
</script>

<template>
    <Head title="Novo paciente" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-zinc-900">
                Novo paciente
            </h2>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div
                    class="rounded-2xl bg-white p-6 shadow-card ring-1 ring-zinc-900/5 sm:p-8"
                >
                    <PatientForm
                        :form="form"
                        rotulo-submit="Cadastrar"
                        @submit="submit"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
