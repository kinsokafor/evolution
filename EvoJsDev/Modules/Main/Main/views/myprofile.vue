<template>
    <div class="row">
        <div class="col-md-4 profile-block">
            <UserData :user-id="auth.getUser.id" :data="auth.getUser"  v-slot="{user}">
                <slot name="userData" :user="user"></slot>
            </UserData>
        </div>
        <div class="col-md-8">
            <slot name="beforeMenu"></slot>
            <Menu :items="items" container-class="col-md-4"/>
            <slot></slot>
        </div>
    </div>
</template>

<script setup>
    import UserData from '@/components/theme/UserData.vue'
    import { useAuthStore } from '@/store/auth';
    import Menu from '@/components/menu/Menu.vue'
    import { computed } from 'vue';

    const auth = useAuthStore();

    const props = defineProps({
        menu: {
            type: Object,
            default: []
        }
    })
    
    const items = computed(() => [
        {
            link: `/#/change-password`,
            label: 'Change Password',
            iconClass: 'fa-key',
            isRouter: false
        },
        {
            link: `/#/change-profile-picture`,
            label: 'Change Profile Picture',
            iconClass: 'fa-image',
            isRouter: false
        },    
        ...props.menu]
    )

</script>

<style lang="scss" scoped>

</style>