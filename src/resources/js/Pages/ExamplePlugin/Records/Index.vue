<script setup>
import IndexPage from '@crud/IndexPage.vue';

defineProps({
    filters: { type: Object, required: true },
    crud: { type: Object, required: true },
    rows: { type: Array, required: true },
    sidebarPanels: { type: Array, default: () => [] },
});
</script>

<template>
    <IndexPage title="Example records" :crud="crud" :filters="filters" :rows="rows" :sidebar-panels="sidebarPanels" />
</template>
