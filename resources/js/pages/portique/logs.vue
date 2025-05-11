
<script setup>
    import { ref } from "vue";
    import { Head, router } from '@inertiajs/vue3';

    const props = defineProps({
        logs: {
            type: Object,
            required: true
        }
    });
</script>

<template>
    <h1>Logs</h1>
</template>