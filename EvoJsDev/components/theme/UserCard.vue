<template>
    <a :href="getProfileLink">
        <div class="user-card animate__animated animate__pulse">
            <div class="user-img">
                <img :src="userImg" :alt="user.surname+'\'s image'" :class="user.gender">
            </div>
            <div class="user-info">
                <h2 class="animate__animated animate__pulse">{{ user.surname }}, {{ user.middle_name }} {{ user.other_names }}</h2>
                <p>
                    {{ user.profile }}
                    <span v-if="user.email != null">{{ user.email }} <br></span>
                    <slot></slot>
                    <span><span class="status-indicator" :class="user.status"></span> {{ roleName }}<br></span>
                    <slot name="buttons"></slot>
                </p>
            </div>
        </div>
    </a>
</template>

<script setup>
    import { computed, ref } from 'vue';
    import { useUsersStore } from '@/Modules/Main/store/users'
    import { isEmpty, imageExists } from '@/helpers'
    import "/color-scheme.css";
    import 'animate.css'
    import male from '../images/male_avatar.svg'
    import female from '../images/female_avatar.svg'
    import config from '/config.json'
    import {useAuthStore} from '@/store/auth'

    const usersStore = useUsersStore();
    const auth = useAuthStore()
    const currentUser = computed(() => auth.getUser)

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

    const roleName = computed(() => {
        if(config.Auth.roles[user.value.role] == undefined) return ""
        return config.Auth.roles[user.value.role].name
    })

    const getProfileLink = computed(() => {
        if(user.value.role == undefined) return "#"
        if("profile" in config.Auth.roles[user.value.role]) {
            return ("/"+config.Auth.roles[user.value.role].profile+"/"+props.userId).replace("//", "/").replace("//", "/");
        }
        if(currentUser.value != undefined && currentUser.value.id == props.userId) {
            return "/#/profile"
        }
        return ("/admin/#/profile/"+props.userId).replace("//", "/").replace("//", "/")
    })
</script>

<style lang="scss" scoped>
    * {
        padding: 0;
        margin: 0;
    }
    a {
        text-decoration: none;
    }
    .user-card {
        display: flex;
        max-width: 411px;
        background: var(--highlight1);
        padding: 13px 20px;
        border-radius: 9px;
        // margin-top: 10px;
        margin-bottom: 10px;
        box-shadow: 0px 7px 7px -8px var(--highlight3);
    }
    .user-info h2{
        font-size: 17px;
        margin-top: 10px;
        margin-bottom: 5px;
        color: var(--shadow3);
    }
    .user-img {
        width: 38%;
    }
    .user-img img {
        margin: 0 !important;
        border-radius: 50%;
        width: 80%;
    }
    .user-img img.Male, .user-img img.male {
        border: 2px solid var(--blue);
    }
    .user-img img.Female, .user-img img.female {
        border: 2px solid var(--purple);
    }
    .user-info p {
        color: var(--shadow3);
        margin: 0;
        font-size: 15px;
    }
    .status-indicator {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }
    .status-indicator.active {
        background-color: var(--green);
    }
    .status-indicator.inactive {
        background-color: var(--red);
    }
</style>