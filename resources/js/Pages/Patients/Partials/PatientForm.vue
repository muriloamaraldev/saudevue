<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    form: { type: Object, required: true },
    rotuloSubmit: { type: String, default: 'Salvar' },
});

defineEmits(['submit']);

/** 52998224725 -> 529.982.247-25, conforme o usuário digita. */
const mascaraCpf = (valor) =>
    (valor ?? '')
        .replace(/\D/g, '')
        .slice(0, 11)
        .replace(/^(\d{3})(\d)/, '$1.$2')
        .replace(/^(\d{3})\.(\d{3})(\d)/, '$1.$2.$3')
        .replace(/\.(\d{3})(\d{1,2})$/, '.$1-$2');

// O form guarda os 11 dígitos; a máscara é só apresentação.
const cpf = computed({
    get: () => mascaraCpf(props.form.cpf),
    set: (valor) => {
        props.form.cpf = valor.replace(/\D/g, '').slice(0, 11);
    },
});
</script>

<template>
    <form @submit.prevent="$emit('submit')" class="space-y-5">
        <div>
            <InputLabel for="name" value="Nome completo" />
            <TextInput
                id="name"
                type="text"
                class="mt-1 block w-full"
                v-model="form.name"
                required
                autofocus
                autocomplete="name"
            />
            <InputError :message="form.errors.name" />
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <InputLabel for="cpf" value="CPF" />
                <TextInput
                    id="cpf"
                    type="text"
                    inputmode="numeric"
                    class="mt-1 block w-full tabular-nums"
                    v-model="cpf"
                    placeholder="000.000.000-00"
                    required
                />
                <InputError :message="form.errors.cpf" />
            </div>

            <div>
                <InputLabel for="birth_date" value="Data de nascimento" />
                <TextInput
                    id="birth_date"
                    type="date"
                    class="mt-1 block w-full"
                    v-model="form.birth_date"
                />
                <InputError :message="form.errors.birth_date" />
            </div>
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <InputLabel for="phone" value="Telefone" />
                <TextInput
                    id="phone"
                    type="text"
                    inputmode="tel"
                    class="mt-1 block w-full"
                    v-model="form.phone"
                />
                <InputError :message="form.errors.phone" />
            </div>

            <div>
                <InputLabel for="email" value="E-mail" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    autocomplete="email"
                />
                <InputError :message="form.errors.email" />
            </div>
        </div>

        <div>
            <InputLabel for="notes" value="Observações" />
            <textarea
                id="notes"
                v-model="form.notes"
                rows="4"
                class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-3.5 py-2.5 text-sm text-zinc-900 shadow-sm transition duration-150 placeholder:text-zinc-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30"
                placeholder="Alergias, medicações de uso contínuo, observações clínicas..."
            />
            <InputError :message="form.errors.notes" />
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <Link
                :href="route('patients.index')"
                class="inline-flex items-center gap-2 rounded-lg border border-zinc-300 bg-white px-4 py-2.5 text-sm font-semibold text-zinc-700 shadow-sm transition duration-150 hover:border-zinc-400 hover:bg-zinc-50"
            >
                Cancelar
            </Link>

            <PrimaryButton :disabled="form.processing">
                {{ rotuloSubmit }}
            </PrimaryButton>
        </div>
    </form>
</template>
