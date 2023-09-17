<template>
    <Restricted access="5,6">
        <div class="row">
            <CreateForm :fields="fields" @submit="onSubmit" :initial-values="user" :columns=2>
                <template #submitButton>
                    <Button type="submit" class="btn btn-primary" :processing="processing">Update</Button>
                </template>
            </CreateForm>
        </div>
        <Restricted access="2">
            <router-link :to="'/change-user-password/'+user.id">Change Password</router-link>
        </Restricted>
    </Restricted>
</template>

<script setup>
    import { onMounted, ref } from 'vue';
    import { useRoute } from 'vue-router';
    import Restricted from '@/components/Restricted.vue'
    import CreateForm from '@/components/form/CreateForm.vue';
    import { isRequired } from '@/helpers'
    import { useAlertStore } from '@/store/alert';
    import Button from '@/components/Button.vue'
    import {Users} from '@/helpers';
    import { useUsersStore } from '@/Modules/Main/store/users'
    import { useConfigStore } from '@/store/config';

    const users = new Users;
    const user = ref({});
    const route = useRoute();
    const alertStore = useAlertStore();
    const processing = ref(false);
    const usersStore = useUsersStore();
    const configStore = useConfigStore();

    const fields = [
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
            "required": true,
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
            "options": configStore.roles
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
    ]
    
    onMounted(() => {
        if(usersStore.users.length <= 0) {
            processing.value = true;
            users.get({id: route.params.id}).then(response => {
                user.value = response.data;
                processing.value = false;
            }).catch(error => {
                alertStore.add(error.message, "danger")
                processing.value = false;
            })
        } else {
            user.value = usersStore.getUsers.find(item => item.id == route.params.id);
        }
    })

    const onSubmit = async (values) => {
        processing.value = true;
        values['file_attachments'] = {
            profile_picture: {
                data: values.profile_picture
            }
        }
        users.update(user.value.id, values).then(function(response){
            processing.value = false;
            alertStore.add("Profile updated", "success");
            if(usersStore.users.length > 0) {
                usersStore.users = usersStore.users.map(obj => {
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