<template>
    <div class="user-img animate__animated animate__fadeInDown">
        <img :src="userImg" alt="" :class="user.gender">
        <h3>{{ user.surname }}, {{ user.other_names }} {{ user.middle_name }}</h3>
    </div>
    <div class="user-data animate__animated animate__fadeInDown">
        <div>
            <div class="label">Surname</div>
            <div class="value">{{ user.surname }}</div>
        </div>
        <div>
            <div class="label">Middle Name</div>
            <div class="value">{{ user.middle_name }}</div>
        </div>
        <div>
            <div class="label">Other Names</div>
            <div class="value">{{ user.other_names }}</div>
        </div>
        <div>
            <div class="label">{{uNameLabel}}</div>
            <div class="value">{{ user.username }}</div>
        </div>
        <div>
            <div class="label">Gender</div>
            <div class="value">{{ user.gender }}</div>
        </div>
        <div>
            <div class="label">Phone Number</div>
            <div class="value">{{ user.phone }}</div>
        </div>
        <slot :user="user"></slot>
    </div>
</template>

<script setup>
    import {ref, computed, watchEffect} from 'vue'
    import { useUsersStore } from '@/Modules/Main/store/users'
    import {isEmpty, getProfilePicture} from '@/helpers'

    const usersStore = useUsersStore()
    const props = defineProps({
        userId: {
            type: Number
        },
        data: {
            type: Object,
            default: {}
        },
        uNameLabel: {
            type: String,
            default: "Registration Number"
        }
    })

    const realUser = computed(() => {
        if(props.userId == undefined) return {}
        return usersStore.getUser(props.userId)
    })

    const user = computed(() => {
        if(isEmpty(realUser.value)) return props.data
        return realUser.value
    })

    const userImg = ref("#")

    watchEffect(() => {
        getProfilePicture(user.value).then(r => {
            userImg.value = r
        })
    })
</script>

<style lang="scss" scoped>
    .user-img {
        position: relative;
        margin-bottom: 24px;
        background: var(--highlight1);
        padding: 11px;
    }
    .user-img img {
        margin: 0 auto !important;
        border-radius: 50%;
        width: 40%;
        min-width: 150px;
        display: block;
    }
    .user-img h3 {
        text-align: center;
        position: absolute;
        padding: 2px 12px;
        font-size: 0.72em;
        color: white;
        bottom: 2px;
        left: 50%;
        transform: translate(-50%);
        border-radius: 30px;
        background: var(--color1);
        text-overflow: ellipsis;
        overflow: hidden;
        white-space: nowrap;
        width: 151px;
    }
    .user-img img.Male + h3, .user-img img.male + h3 {
        background: var(--blue);
        color: var(--highlight1);
    }
    .user-img img.Female + h3, .user-img img.female + h3 {
        background: var(--purple);
        color: var(--highlight1);
    }
    .user-img img.Male, .user-img img.male {
        border: 2px solid var(--blue);
    }
    .user-img img.Female, .user-img img.female {
        border: 2px solid var(--purple);
    }
</style>
<style>
    .user-data > div {
        display: flex;
        padding: 3px 10px;
        margin-bottom: 5px;
        gap: 14px;
        background: var(--highlight1);
        border-radius: 5px;
        justify-content: space-between;
    }
    .user-data > div .label {
        width: 200px;
        max-width: 37%;
        font-size: 0.8em;
        text-transform: uppercase;
        color: var(--color2);
        font-weight: 500;
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
    }
    .value {
        padding-left: 5px;
    }
</style>