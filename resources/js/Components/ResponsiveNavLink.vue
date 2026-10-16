<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'block w-full border-l-4 border-brand-500 bg-brand-50 px-3 py-2 text-start text-base font-semibold text-brand-800 transition duration-150 ease-in-out focus:border-brand-700 focus:bg-brand-100 focus:outline-none focus:text-brand-900'
        : 'block w-full border-l-4 border-transparent px-3 py-2 text-start text-base font-medium text-zinc-600 transition duration-150 ease-in-out hover:border-zinc-300 hover:bg-zinc-50 hover:text-zinc-800 focus:border-zinc-300 focus:bg-zinc-50 focus:outline-none focus:text-zinc-800',
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
