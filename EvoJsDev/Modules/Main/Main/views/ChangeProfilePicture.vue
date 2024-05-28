<template>
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Change Profile Picture</h5>
                    <CreateForm :fields="fields" @submit="onSubmit" :initial-values="initialValues">
                        <template #submitButton>
                            <Button type="submit" class="btn btn-primary" :processing="processing">Update</Button>
                        </template>
                    </CreateForm>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    import CreateForm from '@/components/form/CreateForm.vue'
    import {computed, ref} from 'vue'
    import { useAlertStore } from '@/store/alert'
    import { useAuthStore } from '@/store/auth';
    import { useUsersStore } from '@/Modules/Main/store/users'
    import {Users} from '@/helpers';

    const processing = ref(false)
    const authStore = useAuthStore()
    const alertStore = useAlertStore()
    const users = new Users()

    const user = computed(() => authStore.getUser);
    const usersStore = useUsersStore();

    const initialValues = computed(() => {
        if(user.value?.profile_picture != undefined) {
            return {profile_picture: user.value?.profile_picture}
        }
        return {}
    })

    const fields = computed(() => [
        {
            label: "Profile",
            as: "croppie",
            name: "profile_picture",
            class: "form-control",
            viewport: {
                width: 250,
                height: 250,
                type: "circle"
            },
            boundary: {
                width: 260,
                height: 260
            }
        }
    ])

    const onSubmit = (values, actions) => {
        processing.value = true;
        values.file_attachments = ["profile_picture"]
        users.update(user.value.id, values).then(function(response){
            processing.value = false;
            alertStore.add("Done", "success");
            if(usersStore.data.length > 0) {
                usersStore.data = usersStore.data.map(obj => {
                    if (obj.id == user.value.id) {
                        return {...obj, ...response.data}
                    }
                    return obj;
                })
            }
            
        }).catch(error => {
            alertStore.add(error.message, "danger")
            processing.value = false;
        })
    }
</script>

<style lang="scss" scoped>

</style>