<script setup>
defineProps({
    message: {
        type: String,
    },
});
</script>

<template>
    <div v-show="message">
        <p class="mt-1.5 text-sm font-medium text-red-600">
            {{ message }}
        </p>
    </div>
</template>
