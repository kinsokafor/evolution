<template>
    <div class="row">
        <div class="col-md-4 profile-block">
            <UserData :user-id="route.params.id" />
        </div>
        <div class="col-md-8">
            <Menu :items="menu" container-class="col-md-4"/>
            <slot></slot>
        </div>
    </div>
</template>

<script setup>
    import { onMounted, ref, computed } from 'vue';
    import UserData from '@/components/theme/UserData.vue'
    import { useAuthStore } from '@/store/auth';
    import { useRoute } from 'vue-router';

    const route = useRoute()
    const menu = computed(() => [])

</script>

<style lang="scss" scoped>

</style>