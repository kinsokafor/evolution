<template>
    <div class="row">
        <div class="col-md-4 profile-block">
            <UserData :user-id="id" />
            <hr>
            <Reatricted access="1,2">
                <template #message><slot></slot></template>
                <a class="btn btn-primary" :href="`/login-as/${id}`">Login to profile</a>
            </Reatricted>
            <slot name="left"></slot>
        </div>
        <div class="col-md-8">
            <slot name="beforeMenu"></slot>
            <Menu :items="items" container-class="col-md-4"/>
            <slot></slot>
        </div>
    </div>
</template>

<script setup>
    import { onMounted, ref, computed } from 'vue';
    import UserData from '@/components/theme/UserData.vue'
    import Menu from '@/components/menu/Menu.vue'
    import { useRoute } from 'vue-router';
    import {useAuthStore} from '@/store/auth'

    const auth = useAuthStore()
    const route = useRoute()
    const id = computed(() => {
        if(route.params.id != "") return parseInt(route.params.id)
        return auth.getUser.id
    })
    const props = defineProps({
        menu: {
            type: Object,
            default: []
        }
    })

    const items = computed(() => [
        {
            link: `/admin/#/edit-profile/${id.value}`,
            label: 'Edit',
            iconClass: 'fa-edit',
            access: '1,2,3',
            isRouter: false
        },
        {
            link: `/admin/#/change-user-password/${id.value}`,
            label: 'Change password',
            iconClass: 'fa-lock',
            access: '1,2',
            isRouter: false
        },    
        ...props.menu]
    )

</script>

<style lang="scss" scoped>

</style>