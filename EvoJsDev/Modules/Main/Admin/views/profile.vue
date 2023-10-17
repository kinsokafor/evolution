<template>
    <div class="row">
        <div class="col-md-4 profile-block">
            <UserData :user-id="parseInt(route.params.id)" />
        </div>
        <div class="col-md-8">
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

    const props = defineProps({
        menu: {
            type: Object,
            default: []
        }
    })

    const route = useRoute()
    const items = computed(() => [
        {
            link: `/admin/#/edit-profile/${route.params.id}`,
            label: 'Edit',
            iconClass: 'fa-edit',
            access: '1,2,3',
            isRouter: false
        },
        {
            link: `/admin/#/change-user-password/${route.params.id}`,
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