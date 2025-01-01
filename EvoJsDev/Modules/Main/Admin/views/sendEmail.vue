<template>
    <div>
        <CreateForm
            :fields="fields"
            :columns="2"
            @submit="onSubmit"
            :initialValues="initialValues"
            :processing="processing"
        ></CreateForm>
    </div>
</template>

<script setup>
    import CreateForm from '@/components/form/CreateForm.vue'
    import axios from 'axios';
    import * as yup from 'yup'
    import {isEmail, nonce} from '@/helpers'
    import { useRoute } from 'vue-router'
    import {computed, ref} from 'vue'
    import {useAlertStore} from '@/store/alert'

    const route = useRoute()
    const alertStore = useAlertStore()
    const processing = ref(false)
    const initialValues = computed(() => {
        if(route.params.emails == undefined) return {}
        return {emails: route.params.emails}
    })
    const testEmails = async (value) => {
        if(value == undefined) return "Please enter an email address"
        if(value.trim() == "") return "Please enter an email address"
        let message = ""
        let adj = "is"
        let postFix = ""
        let count = 0
        const result = await new Promise((resolve, reject) => {
            const arr = value.split(",")
            arr.forEach((v, i, a) => {
                const test = isEmail(v)
                if(test !== true) {
                    count++
                    if(count > 1) {
                        adj = "are"
                        postFix = "s"
                    }
                    message += v + ", ";
                    resolve(false)
                }
                if((i + 1) >= a.length) {
                    message = message.length > 0 ? message.slice(0, -2) : message; //remove last two lines
                    resolve(true)
                }
            })
        })
        if(result == true) {
            return true
        } else {
            if(message != "") {
                return `${message} ${adj} not valid email${postFix}`;
            }
            return "Email is not validated. Remove the last comma."
        }
    }

    const fields = [
        {
            label: "Emails",
            name: "emails",
            hint: "Seperate multiple emails with commas",
            rules: testEmails
        },
        {
            label: "Subject",
            name: "subject",
            rules: yup.string().required("Please enter the subject")
        },
        {
            label: "Message",
            name: "message",
            as: "wysiwyg",
            rules: yup.string().required("Please enter a message")
        }
    ]

    const onSubmit = async (values, action) => {
        processing.value = true
        await axios.post(process.env.EVO_API_URL + '/api/send-email', values, {
            'Access-Control-Allow-Credentials':true,
            headers: {
                // 'Access-Control-Allow-Origin': '*', 
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${nonce()}` 
            }
        }).then(r => {
            processing.value = false
            // action.resetForm()
            if(r.data == "") {
                alertStore.add("E-mail sent successfully", "success")
            } else {
                alertStore.add(`Failed: ${r.data}`, "danger")
            }
        });
    }
</script>

<style lang="scss" scoped>

</style>