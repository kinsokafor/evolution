<template>
    <div>
        <UserCard :data="auth.getUser"></UserCard>
    </div>
    <!-- <CounterCard 
      end-point="api/user?status=active" 
      title="Active Users" 
      icon-class="fa-users" 
      color="var(--green)"
      />
      <CounterCard 
      end-point="api/user?status=inactive" 
      title="Inactive Users" 
      icon-class="fa-users" 
      color="var(--yellow)"
      text-color="var(--shadow1)"
      layout-style="classic"
      /> -->
</template>

<script setup>
    import UserCard from '@/components/theme/UserCard.vue';
    // import CounterCard from '@/components/theme/CounterCard.vue';
    import {useAuthStore} from '@/store/auth'
    const auth = useAuthStore();
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