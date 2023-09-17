<template>
    {{ initialValues }}
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
    import {useAlertStore} from '@/store/alert'
    import {useConfigStore} from '@/store/config'
    import { Config, findByDottedIndex } from '@/helpers'
    
    const processing = ref(false)
    const alertStore = useAlertStore()
    const config = useConfigStore()
    const configAPI = new Config;
    const props = defineProps({
        fields: Array
    })

    const initialValues = ref({})

    const handleSubmit = (values) => {
        processing.value = true;
        configAPI.update(values).then(response => {
            console.log(response.data)
            alertStore.add("Done")
            processing.value = false;
        }).catch(error => {
            alertStore.add(error.message, "danger")
            processing.value = false;
        })
    }

    onBeforeMount(() => {
        props.fields.forEach(item => {

            // initialValues.value[item] = config[item]//findByDottedIndex(item.name, config)
        });
    })
</script>

<style lang="scss" scoped>

</style>