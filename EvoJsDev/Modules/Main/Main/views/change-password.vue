<template>
    <Restricted access="1,2,3,4,5,6,7,8,9">
        <CreateForm
            :fields="fields"
            :columns="2"
            @submit="onSubmit"
            :button-attributes="buttonAttributes"
        ></CreateForm>
    </Restricted>
</template>

<script setup>
    import Restricted from '@/components/Restricted.vue'
    import CreateForm from '@/components/form/CreateForm.vue'
    import { isRequired, isPassword, nonce } from '@/helpers'
    import { ref } from 'vue';
    import axios from 'axios'
    import { useAlertStore } from '@/store/alert';

    const alertStore = useAlertStore();

    const fields = [
        {
            "label": "Old Password",
            "type": "password",
            "name": "oldPassword",
            "rules": isRequired,
            "as": "password"
        },
        {
            "label": "New Password",
            "type": "password",
            "name": "password",
            "rules": isPassword,
            "as": "password"
        }
    ]

    const buttonAttributes = ref({
        "class": "btn btn-primary",
        "processing": false
    })

    const onSubmit = async (values) => {
        buttonAttributes.value.processing = true
        await axios.post(process.env.EVO_API_URL + "/api/change-password/", values, {
            'Access-Control-Allow-Credentials':true,
            headers: {
                'Access-Control-Allow-Origin': '*', 
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${nonce()}` 
            }
        }).then(response => {
            if(response.data.status) {
                alertStore.add(response.data.message, "success")
            } else {
                alertStore.add(response.data.message, "danger")
            }
            buttonAttributes.value.processing = false
        }).catch(error => {
            alertStore.add(error.message, "danger")
            buttonAttributes.value.processing = false
        })
    }

</script>

<style lang="scss" scoped>

</style>