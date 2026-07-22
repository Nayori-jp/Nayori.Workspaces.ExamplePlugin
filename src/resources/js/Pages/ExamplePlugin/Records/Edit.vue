<script setup>
import FormPage from '@crud/FormPage.vue';
import FormActions from '@ui/FormActions.vue';
import { useWorkspaceForm } from '@workspace/use-workspace-form';

const props = defineProps({
    header: { type: Object, required: true },
    form: { type: Object, required: true },
    fields: { type: Array, required: true },
    routes: { type: Object, required: true },
    workspaceRecord: { type: Object, required: true },
    sidebarPanels: { type: Array, default: () => [] },
});

const form = useWorkspaceForm('example-plugin.records.edit', { ...props.form }, props.workspaceRecord);

function submit() {
    form.submitWorkspace('put', props.routes.update);
}
</script>

<template>
    <FormPage
        title="Edit Example Record"
        :header="header"
        :form="form"
        :fields="fields"
        :cancel-href="routes.index"
        submit-label="Save changes"
        :on-submit="submit"
        :sidebar-panels="sidebarPanels"
    >
        <template #actions="actionProps">
            <FormActions v-bind="actionProps" />
        </template>
    </FormPage>
</template>
