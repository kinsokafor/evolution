<template>
    <CreateForm :fields="fields" class="form" @submit="submit" :processing="processing" :initial-values="initialValues">
        <template #fields="{fields, values}">
            <div class="fields">
                <DisplayFields :fields="fields" :values="values" :initial-values="initialValues"/>
            </div>
        </template>
        <template #submitButton>
            <div class="btns">
                <button class="btn btn-primary">Save</button>
            </div>
        </template>
    </CreateForm>
</template>

<script setup>
    import CreateForm from './CreateForm.vue';
    import DisplayFields from './DisplayFields.vue';
    import {Request} from '@/helpers'
    import {ref} from 'vue'
    import {useAlertStore} from '@/store/alert'

    const processing = ref(false)
    const alert = useAlertStore()
    const props = defineProps({
        fields: {
            type: Array,
            default: []
        },
        initialValues: {
            type: Object,
            default: {}
        },
        endPoint: String,
        data: {
            type: Object,
            default: {}
        }
    })

    const emits = defineEmits(["done"])

    const submit = (values) => {
        values = {...props.data, ...values}
        processing.value = true
        const req = new Request;
        req.put(props.endPoint, values).then(r => {
            emits("done", r.data)
            processing.value = false
            alert.add("Done", "success")
        }).catch(e => {
            processing.value = false
            alert.add(e.message, "danger")
        })
    }
</script>

<style lang="scss" scoped>
    .form {
        position: relative;
        display: flex;
        gap: 10px;
    }
    .form .fields {
        width: 70%;
    }
    .form .btns {
        width: 30%;
        position: relative;
    }
    .btn {
        position: absolute;
        bottom: 10px;
    }
</style>