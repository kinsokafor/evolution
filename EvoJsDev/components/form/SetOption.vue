<template>
    <CreateForm 
        :fields="fields"
        @submit="handleSubmit"
        :processing="processing"
        :initial-values="initialValues"
    >
        <template #submitButton>
            <Button type="submit" class="btn btn-primary">Save</Button>
        </template>
    </CreateForm>
</template>

<script setup>
    import Button from '@/components/Button.vue'
    import CreateForm from './CreateForm.vue'
    import { ref, onBeforeMount } from 'vue'
    import { Options } from '@/helpers'
    import {useAlertStore} from '@/store/alert'

    const processing = ref(false)
    const alertStore = useAlertStore();
    const options = new Options;
    const props = defineProps({
        fields: Array
    })

    const initialValues = ref({})

    const handleSubmit = (values) => {
        var noChanges = true;
        props.fields.forEach(item => {
            processing.value = true;
            if(values[item.name] != undefined && values[item.name] != initialValues.value[item.name]) {
                noChanges = false;
                options.update(item.name, values[item.name]).then(response => {
                    // initialValues.value[item.name] = response.data
                    alertStore.add("Done")
                    processing.value = false;
                }).catch(error => {
                    alertStore.add(error.message, "danger")
                    processing.value = false;
                })
            } else {
                processing.value = false;
            }
        });
        if(noChanges) {
            alertStore.add("No changes found", "danger")
        }
    }

    onBeforeMount(() => {
        props.fields.forEach(item => {
            options.get(item.name).then(response => {
                initialValues.value[item.name] = response.data
            })
        });
    })
</script>

<style lang="scss" scoped>

</style>