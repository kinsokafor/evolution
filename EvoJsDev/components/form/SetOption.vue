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
    import { ref, onMounted, watch } from 'vue'
    import { Options, Records } from '@/helpers'
    import {useAlertStore} from '@/store/alert'
    import * as _ from 'lodash'

    const props = defineProps({
        fields: Array,
        as: {
            type: String,
            default: 'options'
        }
    })
    
    const processing = ref(false)
    const alertStore = useAlertStore();
    const options = props.as == 'options' ? new Options : new Records;

    const initialValues = ref({})

    const handleSubmit = (values) => {
        var noChanges = true;
        props.fields.forEach(item => {
            if(item.as == 'collection') {
                values[item.name] = values[item.name].filter(i => {
                    let ret = false
                    Object.values(i).map(j => {
                        if(j != undefined) ret = true
                    })
                    return ret
                })
            }
            processing.value = true;
            if(values[item.name] != undefined && values[item.name] != initialValues.value[item.name]) {
                noChanges = false;
                options.update(item.name, values[item.name]).then(response => {
                    initialValues.value[item.name] = response.data
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

    onMounted(() => {
        loadInitialValues()
    })

    watch(() => props.fields, () => {
        loadInitialValues()
    })

    const loadInitialValues = () => {
        props.fields.forEach(item => {
            options.get(item.name).then(response => {
                initialValues.value[item.name] = response.data
            }).catch(e => {
                if(item.default != undefined) {
                    initialValues.value[item.name] = item.default
                }
            })
        });
    }
</script>

<style lang="scss" scoped>

</style>