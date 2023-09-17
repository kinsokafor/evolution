<template>
    <Restricted access="1,2,3,4,5,6,7,8,9">
        <div>
            <UserCard :user-id="auth.currentUser.id"></UserCard>
        </div>
        <div class="row">
          <div class="col-md-4">
            <router-link to="users" class="counter-link">
              <CounterCard 
              end-point="api/user?status=active" 
              title="Active Users" 
              icon-class="fa-users" 
              color="var(--green)"
              />
            </router-link>
          </div>
          <div class="col-md-4">
            <CounterCard 
            end-point="api/user?status=inactive" 
            title="Inactive Users" 
            icon-class="fa-users" 
            color="var(--yellow)"
            text-color="var(--shadow1)"
            />
          </div>
        </div>
    </Restricted>
</template>

<script setup>
    import Restricted from '@/components/Restricted.vue';
    import UserCard from '@/components/theme/UserCard.vue';
    import CounterCard from '@/components/theme/CounterCard.vue';
    import {useAuthStore} from '@/store/auth'
    const auth = useAuthStore();
</script>

<style lang="scss" scoped>
    .counter-link {
        text-decoration: none;
    }
</style>