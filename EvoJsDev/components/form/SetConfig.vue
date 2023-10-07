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
    import { ref, watchEffect } from 'vue'
    import {useAlertStore} from '@/store/alert'
    import { findByDottedIndex } from '@/helpers'
    import _ from 'lodash';
    import { useConfigStore } from '@/store/config'

    const config = useConfigStore();
    const processing = ref(false)
    const alertStore = useAlertStore()
    const props = defineProps({
        fields: Array
    })

    const initialValues = ref({})

    const handleSubmit = (values) => {
        processing.value = true;
        config.update(values).then(response => {
            alertStore.add("Done")
            processing.value = false;
        }).catch(error => {
            alertStore.add(error.message, "danger")
            processing.value = false;
        })
    }

    watchEffect(() => {
        if(config.all == {}) return
        props.fields.forEach(item => {
            const arr = item.name.split('.')
            const temp = arr.reduce((a,b,i,ar)=> {
                let v = null
                if((i+1) == ar.length) {
                    const vTemp = findByDottedIndex(item.name, config.props)
                    if(vTemp != undefined) {
                        switch(typeof(vTemp)) {
                            case "number":
                            case "boolean":
                                v = vTemp
                            break;
                                
                            default:
                                v = "\""+vTemp+"\""
                        }
                    }
                } else v = "{?}"
                return a.replace("?", `"${b}":${v}`)
            }, "{?}")
            initialValues.value = _.merge(initialValues.value, JSON.parse(temp))
        });
    })
</script>

<style lang="scss" scoped>

</style>