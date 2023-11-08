<template>
    <div>
        <UserCard :user-id="auth.getUser.id" :data="auth.getUser"></UserCard>
    </div>
</template>

<script setup>
    import UserCard from '@/components/theme/UserCard.vue';
    // import CounterCard from '@/components/theme/CounterCard.vue';
    import {useAuthStore} from '@/store/auth'
    import { watchEffect, computed } from 'vue';
    import {useConfigStore} from '@/store/config'
    const auth = useAuthStore();
    const config = useConfigStore()
    const roles = computed(() => config.get(`Auth.roles.${auth.getUser.role}`))

    watchEffect(() => {
      if(roles.value.index != undefined) {
        window.location = roles.value.index
      }
    })
</script>

<script>
  export default {
    metaInfo: {
      title: 'My Example App',
      titleTemplate: '%s - Yay!',
      htmlAttrs: {
        lang: 'en',
        amp: true
      }
    }
  }
</script> 

<style lang="scss" scoped>

</style>