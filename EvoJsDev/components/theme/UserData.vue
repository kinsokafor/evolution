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
            <div class="label">Registration Number</div>
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
        <!-- <QrShow :user-id="data.username"/>
        <QrAccess /> -->
        <slot></slot>
    </div>
</template>

<script setup>
    import {ref, computed} from 'vue'
    import { useUsersStore } from '@/Modules/Main/store/users'
    import {isEmpty, imageExists} from '@/helpers'
    import male from '../images/male_avatar.svg'
    import female from '../images/female_avatar.svg'

    const usersStore = useUsersStore()
    const props = defineProps({
        userId: {
            type: Number
        },
        data: {
            type: Object,
            default: {}
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

    const tempImg = ref("#")

    const userImg = computed(() => {
        const setTemp = () => {
            switch(user.value.gender) {
                case "female":
                case "Female":
                tempImg.value = female
                    break;
                default:
                tempImg.value = male
                    break;
            }
        }
        if(user.value.profile_picture == undefined) {
            setTemp()
            return tempImg.value
        }
        imageExists(user.value.profile_picture, () => {
            tempImg.value = user.value.profile_picture
        }, () => {
            setTemp()
        })
        return tempImg.value
    })
</script>

<style lang="scss" scoped>
    .user-img {
        position: relative;
        margin-bottom: 24px;
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
    }
    .user-img img.Female + h3, .user-img img.female + h3 {
        background: var(--purple);
    }
    .user-img img.Male, .user-img img.male {
        border: 2px solid var(--blue);
    }
    .user-img img.Female, .user-img img.female {
        border: 2px solid var(--purple);
    }
    .user-data > div[data-v-5beb1081] {
        display: flex;
        padding: 3px 10px;
        margin-bottom: 5px;
        gap: 14px;
        background: #f4f4f4;
        border-radius: 5px;
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