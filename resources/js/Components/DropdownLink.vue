<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Link
        :href="href"
        class="block w-full px-4 py-2.5 text-start text-sm leading-5 text-zinc-700 transition duration-150 ease-in-out hover:bg-brand-50 hover:text-brand-800 focus:bg-brand-50 focus:text-brand-800 focus:outline-none"
    >
        <slot />
    </Link>
</template>
