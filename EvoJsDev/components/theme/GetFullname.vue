<template>
    <span>
        {{ getFullname(user) }}
    </span>
</template>

<script setup>
    import { useUsersStore } from '@module/Main/store/users'
    import { computed } from 'vue'
    import { getFullname } from '@/helpers'

    const props = defineProps({
        id: Number
    })

    const store = useUsersStore()
    const user = computed(() => store.getUser(props.id))
</script>

<style lang="scss" scoped>

</style>