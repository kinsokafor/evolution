<template>
    <Restricted access="1,2,3">
        <div class="row">
            <div class="col-md-6">
                <CreateForm :fields="fields" @submit="onSubmit" :initial-values="user" :columns=2>
                    <template #submitButton>
                        <Button type="submit" class="btn btn-primary" :processing="processing">Update</Button>
                    </template>
                </CreateForm>
            </div>
        </div>
    </Restricted>
</template>

<script setup>
    import { ref, computed } from 'vue';
    import { useRoute } from 'vue-router';
    import Restricted from '@/components/Restricted.vue'
    import CreateForm from '@/components/form/CreateForm.vue';
    import { isRequired } from '@/helpers'
    import { useAlertStore } from '@/store/alert';
    import { useAuthStore } from '@/store/auth';
    import Button from '@/components/Button.vue'
    import {Users} from '@/helpers';
    import { useUsersStore } from '@/Modules/Main/store/users'
    import config from '/config.json'
    import * as yup from 'yup'

    const users = new Users;
    const route = useRoute();
    const alertStore = useAlertStore();
    const processing = ref(false);
    const usersStore = useUsersStore();
    const authStore = useAuthStore()

    const access = computed(() => authStore.hasAccess("1"))
    const user = computed(() => {
        let u = {...usersStore.getUser(route.params.id)}
        delete u?.password
        return u
    });

    const roles = computed(() => {
        return Object.entries(config.Auth.roles).map(role => {
                return {
                    name: role[1].name,
                    value: role[0],
                    capacity: role[1].capacity,
                }
            }).filter(role => {
                if(role.value == 'software_engineer') return false
                if(!access.value) {
                    if(["super_admin", "admin"].findIndex(i => i == role.value) != -1) return false
                }
                return true
            })
    })

    const fields = computed(() => [
        {
            "label": "Surname",
            "as": "input",
            "name": "surname",
            "rules": isRequired,
            "class": "form-control",
            "column": "left"
        },
        {
            "label": "Profile",
            "as": "croppie",
            "name": "profile_picture",
            "class": "form-control",
            "column": "right",
            "viewport": {
                width: 250,
                height: 250,
                type: "circle"
            },
            "boundary": {
                width: 260,
                height: 260
            }
        },
        {
            "label": "Middle Name",
            "as": "input",
            "name": "middle_name"
        },
        {
            "label": "Other Names",
            "as": "input",
            "name": "other_names",
            "rules": isRequired,
        },
        {
            "label": "Email",
            "as": "input",
            "name": "email",
            "rules": isRequired,
            "column": "left"
        },
        {
            "label": "Phone Number",
            "as": "input",
            "name": "phone",
            "rules": isRequired,
            "column": "left"
        },
        {
            "label": "Address",
            "as": "textarea",
            "name": "address",
            "column": "right"
        },
        {
            "label": "Role",
            "as": "select",
            "name": "role",
            "column": "left",
            "options": roles.value
        },
        {
            "label": "Gender",
            "as": "select",
            "name": "gender",
            "column": "right",
            "options": [
                {
                    name: "Male",
                    value: "male"
                },
                {
                    name: "Female",
                    value: "female"
                }
            ]
        }
    ])
    
    // onMounted(() => {
    //     user.value = usersStore.getUser(route.params.id);
    // })

    const onSubmit = async (values, actions) => {
        processing.value = true;
        values.file_attachments = ["profile_picture"]
        users.update(user.value.id, values).then(function(response){
            processing.value = false;
            alertStore.add("Profile updated", "success");
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