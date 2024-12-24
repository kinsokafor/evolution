<template>
    <img :src="userImg" alt="" :class="user.gender">
</template>

<script setup>
    import {ref, watchEffect, computed} from 'vue'
    import { useUsersStore } from '@/Modules/Main/store/users'
    import {getProfilePicture, isEmpty} from '@/helpers'
    const userImg = ref("#")

    const usersStore = useUsersStore()
    const props = defineProps({
        userId: {
            type: [null, Number],
            default: null
        },
        data: {
            type: Object,
            default: {}
        }
    })

    const realUser = computed(() => {
        if(props.userId == null) return {}
        return usersStore.getUser(props.userId)
    })

    const user = computed(() => {
        if(isEmpty(realUser.value)) return props.data
        return realUser.value
    })

    watchEffect(() => {
        getProfilePicture(user.value).then(r => {
            userImg.value = r
        })
    })
</script>

<style lang="scss" scoped>
    img.Male, img.male {
        border: 2px solid var(--blue);
    }
    img.Female, img.female {
        border: 2px solid var(--purple);
    }
</style>