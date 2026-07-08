<script setup>
import ShowPage from '@crud/ShowPage.vue';

defineProps({
    header: { type: Object, required: true },
    stats: { type: Array, default: () => [] },
    sidebarPanels: { type: Array, default: () => [] },
});
</script>

<template>
    <ShowPage title="Example record" :header="header" :stats="stats" :sidebar-panels="sidebarPanels" />
</template>
