<template>
    <div class="row">
        <div class="col-md-4 profile-block">
            <UserData :user-id="userId" />
        </div>
        <div class="col-md-8">
            <Menu :items="menu" container-class="col-md-4"/>
            <slot></slot>
        </div>
    </div>
</template>

<script setup>
    import { onMounted, ref, computed } from 'vue';
    import { useAlertStore } from '@/store/alert';
    import UserData from '@/components/theme/UserData.vue'
    import {Users} from '@/helpers';
    import { useConfigStore } from '@/store/config';
    import { useAuthStore } from '@/store/auth';

    const users = new Users;
    const userId = ref(null);
    const alertStore = useAlertStore();
    const processing = ref(false);
    const configStore = useConfigStore();
    const auth = useAuthStore();

    const menu = computed(() => [])
    
    onMounted(() => {
        processing.value = true;
        auth.getLoginStatus().then(response => {
            userId.value = response.data.currentUser.id
        })
        
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