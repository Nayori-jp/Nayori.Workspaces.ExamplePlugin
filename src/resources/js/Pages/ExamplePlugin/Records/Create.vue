<script setup>
import FormPage from '@crud/FormPage.vue';
import { useWorkspaceForm } from '@workspace/use-workspace-form';

const props = defineProps({
    header: { type: Object, required: true },
    form: { type: Object, required: true },
    fields: { type: Array, required: true },
    routes: { type: Object, required: true },
});

const form = useWorkspaceForm('example-plugin.records.create', { ...props.form });

function submit() {
    form.post(props.routes.store);
}
</script>

<template>
    <FormPage
        title="New Example Record"
        :header="header"
        :form="form"
        :fields="fields"
        :cancel-href="routes.index"
        submit-label="Save record"
        :on-submit="submit"
    />
</template>
