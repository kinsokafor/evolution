<template>
    <div class="row">
        <div class="col-md-4 profile-block">
            <UserData :user-id="auth.getUser.id" :data="auth.getUser" />
        </div>
        <div class="col-md-8">
            <slot></slot>
        </div>
    </div>
</template>

<script setup>
    import UserData from '@/components/theme/UserData.vue'
    import { useAuthStore } from '@/store/auth';
    import { watchEffect, computed } from 'vue';
    import {useConfigStore} from '@/store/config'

    const auth = useAuthStore();
    const config = useConfigStore()
    const roles = computed(() => {
        if(auth.getUser.role == undefined) return {}
        return config.get(`Auth.roles.${auth.getUser.role}`)
    })

    watchEffect(() => {
      if(roles.value?.profile != undefined) {
        window.location = roles.value.profile
      }
    })

</script>

<style lang="scss" scoped>

</style>