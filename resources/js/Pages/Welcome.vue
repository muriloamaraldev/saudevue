<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});

const steps = [
    {
        step: '01',
        title: 'Conta do profissional',
        text: 'Cada profissional cria a sua própria conta. Essa conta é a raiz de tudo: é ela que delimita o espaço de trabalho isolado.',
    },
    {
        step: '02',
        title: 'Cadastro de pacientes',
        text: 'Dentro da sua conta, o profissional cadastra os pacientes que atende — dados básicos, contato e observações.',
    },
    {
        step: '03',
        title: 'Prontuários',
        text: 'Cada paciente recebe o seu prontuário: anamnese, diagnóstico, conduta e prescricao, sempre ligados ao paciente certo.',
    },
];
</script>

<template>
    <Head title="SaúdeVue" />

    <div class="min-h-screen bg-brand-glow">
        <!-- Barra superior -->
        <header
            class="sticky top-0 z-30 border-b border-zinc-200/60 bg-white/85 backdrop-blur"
        >
            <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <Link href="/" class="flex items-center gap-2.5">
                    <ApplicationLogo class="h-8 w-auto" />
                    <span class="text-base font-semibold tracking-tight text-zinc-900">
                        SaúdeVue
                    </span>
                </Link>

                <nav class="flex items-center gap-2 sm:gap-3">
                    <Link
                        v-if="canLogin"
                        href="/login"
                        class="rounded-lg px-3 py-2 text-sm font-medium text-zinc-600 transition duration-150 hover:text-zinc-900"
                    >
                        Entrar
                    </Link>

                    <Link
                        v-if="canRegister"
                        href="/register"
                        class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition duration-150 hover:bg-brand-700"
                    >
                        Criar conta
                    </Link>
                </nav>
            </div>
        </header>

        <!-- Hero -->
        <section class="mx-auto max-w-6xl px-4 pb-20 pt-16 sm:px-6 sm:pt-24 lg:px-8">
            <div class="max-w-3xl">
                <span
                    class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-1.5 text-xs font-medium text-brand-800 shadow-sm ring-1 ring-brand-200"
                >
                    <span class="h-1.5 w-1.5 rounded-full bg-brand-500" />
                    Gestão clínica
                </span>

                <h1
                    class="mt-6 text-4xl font-semibold tracking-tight text-zinc-900 sm:text-5xl sm:leading-[1.1]"
                >
                    A sua agenda e os seus prontuários,
                    <span class="text-brand-700">só seus.</span>
                </h1>

                <p class="mt-5 max-w-2xl text-lg leading-relaxed text-zinc-600">
                    O SaúdeVue organiza o consultório em três níveis: a sua
                    conta, os pacientes que você atende e o prontuário de cada
                    um. Cada profissional enxerga apenas o próprio espaço —
                    nada de dados cruzados entre contas.
                </p>

                <div class="mt-8 flex flex-wrap items-center gap-3">
                    <Link
                        v-if="canRegister"
                        href="/register"
                        class="rounded-lg bg-brand-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition duration-150 hover:bg-brand-700"
                    >
                        Criar conta grátis
                    </Link>

                    <Link
                        v-if="canLogin"
                        href="/login"
                        class="rounded-lg border border-zinc-300 bg-white px-5 py-3 text-sm font-semibold text-zinc-700 shadow-sm transition duration-150 hover:border-zinc-400 hover:bg-zinc-50"
                    >
                        Já tenho conta
                    </Link>
                </div>
            </div>
        </section>

        <!-- Como o sistema foi pensado -->
        <section class="border-t border-zinc-200/60 bg-white/70">
            <div class="mx-auto max-w-6xl px-4 py-20 sm:px-6 lg:px-8">
                <div class="max-w-2xl">
                    <h2 class="text-2xl font-semibold tracking-tight text-zinc-900 sm:text-3xl">
                        Como o sistema foi pensado
                    </h2>
                    <p class="mt-3 text-base leading-relaxed text-zinc-600">
                        Três níveis encadeados. Cada nível pertence
                        exclusivamente ao nível acima dele.
                    </p>
                </div>

                <ol class="mt-12 grid gap-6 sm:grid-cols-3">
                    <li
                        v-for="item in steps"
                        :key="item.step"
                        class="rounded-2xl bg-white p-6 shadow-card ring-1 ring-zinc-900/5"
                    >
                        <span
                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-brand-50 text-sm font-semibold text-brand-700 ring-1 ring-brand-200"
                        >
                            {{ item.step }}
                        </span>

                        <h3 class="mt-4 font-semibold text-zinc-900">
                            {{ item.title }}
                        </h3>
                        <p class="mt-2 text-sm leading-relaxed text-zinc-600">
                            {{ item.text }}
                        </p>
                    </li>
                </ol>

                <div
                    class="mt-12 rounded-2xl bg-brand-900 p-8 text-white shadow-card-lg sm:p-10"
                >
                    <h3 class="text-lg font-semibold tracking-tight">
                        Isolamento entre contas
                    </h3>
                    <p class="mt-2 max-w-3xl text-sm leading-relaxed text-brand-100">
                        O multi-tenancy aqui é simples de propósito: a conta é o
                        inquilino. Os pacientes carregam o dono da conta, e os
                        prontuários carregam o paciente e o dono. Nenhuma
                        consulta atravessa essa fronteira — mesmo que um filtro
                        seja esquecido, o banco e as regras de autorização
                        ainda bararam o acesso.
                    </p>
                </div>
            </div>
        </section>

        <!-- Rodapé -->
        <footer class="border-t border-zinc-200/60 bg-white/70">
            <div
                class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-4 px-4 py-8 sm:flex-row sm:px-6 lg:px-8"
            >
                <div class="flex items-center gap-2.5">
                    <ApplicationLogo class="h-7 w-auto" />
                    <span class="text-sm font-medium text-zinc-600">SaúdeVue</span>
                </div>

                <p class="text-sm text-zinc-500">
                    Prontuário é documento médico-legal. Dado sensível, tratado
                    como tal.
                </p>
            </div>
        </footer>
    </div>
</template>
