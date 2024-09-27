<template>
    <div class="row">
        <div class="col-md-4 profile-block">
            <UserData :user-id="id" :u-name-label="uNameLabel" v-slot="{user}">
                <slot name="userData" :user="user"></slot>
            </UserData>
            <hr>
            <Restricted access="1,2">
                <template #message><slot></slot></template>
                <a class="btn btn-primary" :href="`/login-as/${id}`">Login to profile</a>
            </Restricted>
            <slot name="left"></slot>
        </div>
        <div class="col-md-8">
            <slot name="beforeMenu"></slot>
            <Menu :items="items" :enable-search="false" container-class="col-md-4"/>
            <slot></slot>
        </div>
    </div>
</template>

<script setup>
    import { computed } from 'vue';
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
        },
        uNameLabel: {
            type: String,
            default: "Registration Number"
        },
        editLink: {
            type: String,
            default: "/admin/#/edit-profile/{id}"
        },
        cpLink: {
            type: String,
            default: "/admin/#/change-user-password/{id}"
        }
    })

    const eL = computed(() => props.editLink.replace("{id}", id.value))

    const cpL = computed(() => props.cpLink.replace("{id}", id.value))

    const items = computed(() => [
        ...props.menu,
        {
            link: eL.value,
            label: 'Edit',
            iconClass: 'fa-edit',
            access: '1,2,3',
            isRouter: false
        },
        {
            link: cpL.value,
            label: 'Change password',
            iconClass: 'fa-lock',
            access: '1,2',
            isRouter: false
        }]
    )

</script>

<style lang="scss" scoped>

</style>