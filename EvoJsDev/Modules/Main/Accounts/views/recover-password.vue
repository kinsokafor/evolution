<template>
    <div>
        {{ code }}
        <div class="alert alert-info" v-if="stage == 2">
            Check your email for verification code
        </div>
        <create-form
            :fields="fields"
            :processing="processing"
            @submit="handleSubmit"
            @values="v => values = v"
        >

        </create-form>
    </div>
</template>

<script setup>
    import CreateForm from '@form/CreateForm.vue'
    import {computed, ref} from 'vue'
    import {Request, randomId, isPassword} from '@/helpers'
    import * as yup from 'yup'
    import {useAlertStore} from '@/store/alert'
    import {useConfigStore} from '@/store/config'

    const processing = ref(false)
    const stage = ref(1)
    const values = ref({})
    const code = randomId(6, "1234567890")
    const req = new Request()
    const alertStore = useAlertStore()
    const configStore = useConfigStore()
    const loginLink = computed(() => configStore.get("links.login"))
    const userId = ref(null)
    const attempts = ref(2);

    const fields = computed(() => [
        {
            label: "Email",
            name: "email",
            rules: yup.string().email().required(),
            condition: stage.value == 1
        },
        {
            label: "Verification code",
            name: "code",
            rules: yup.string().required(),
            condition: stage.value == 2
        },
        {
            label: "New password",
            name: "password",
            as: "password",
            rules: isPassword,
            condition: stage.value == 3
        },
        {
            label: "Repeat password",
            name: "repeat_password",
            as: "password",
            rules: function() {
                return values.value.password == values.value.repeat_password ? true : "Password combination do not match";
            },
            condition: stage.value == 3
        }
    ])

    const handleSubmit = (values, actions) => {
        processing.value = true
        if(stage.value == 1) {
            values['code'] = code;
            return req.post(req.root+"/api/recover-password/check-email", values).then(r => {
                stage.value = 2
                userId.value = r.data
                actions.resetForm()
            }).catch(e => {
                alertStore.add(e.response.data, "danger")
            }).finally(() => {
                processing.value = false
            })
        }
        if(stage.value == 2) {
            if(attempts.value <= 0) {
                stage.value = 1;
                userId.value = null;
                actions.resetForm()
                attempts.value = 2
            }
            if(values.code != code) {
                alertStore.add(`Incorrect verification code. ${attempts.value} attempts remaining.`, "danger")
                attempts.value--
            } else {
                stage.value = 3
            }
            processing.value = false
            return;
        }
        if(stage.value == 3) {
            values['id'] = userId.value;
            return req.post(req.root+"/api/recover-password/change", values).then(r => {
                actions.resetForm()
                alertStore.add("Successful")
                setTimeout(() => {
                    window.location = loginLink.value
                }, 3000);
            }).catch(e => {
                alertStore.add(e.response.data, "danger")
            }).finally(() => {
                processing.value = false
            })
        }
    }

</script>

<style lang="scss" scoped>

</style>