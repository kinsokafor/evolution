<template>
    <Restricted access="2">
        <UserCard :user-id="parseInt(route.params.id)"></UserCard>
        <CreateForm
            :fields="fields"
            :columns="2"
            @submit="onSubmit"
            :button-attributes="buttonAttributes"
        ></CreateForm>
    </Restricted>
</template>

<script setup>
    import UserCard from '@/components/theme/UserCard.vue'
    import Restricted from '@/components/Restricted.vue'
    import CreateForm from '@/components/form/CreateForm.vue'
    import { useRoute } from 'vue-router';
    import { isRequired, isPassword, nonce } from '@/helpers'
    import { ref } from 'vue';
    import axios from 'axios'
    import { useAlertStore } from '@/store/alert';

    const alertStore = useAlertStore();
    const route = useRoute();

    const fields = [
        {
            "label": "New User Password",
            "type": "password",
            "name": "password",
            "rules": isPassword,
            "as": "password"
        },
        {
            "label": "Your Password",
            "type": "password",
            "name": "yourPassword",
            "rules": isRequired,
            "as": "password"
        }
        
    ]

    const buttonAttributes = ref({
        "class": "btn btn-primary",
        "processing": false
    })

    const onSubmit = async (values) => {
        values['user_id'] = parseInt(route.params.id);
        buttonAttributes.value.processing = true
        await axios.post(process.env.EVO_API_URL + "/api/change-user-password/", values, {
            withCredentials: true,
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