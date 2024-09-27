<template>
    <a :href="getProfileLink">
        <div class="user-card animate__animated animate__pulse">
            <div class="user-img">
                <img :src="userImg" :alt="user.surname+'\'s image'" :class="user.gender">
            </div>
            <div class="user-info">
                <h2 class="animate__animated animate__pulse">{{ user.surname }}, {{ user.middle_name }} {{ user.other_names }}</h2>
                <div>
                    <div v-if="user?.email != undefined" class="email" :title="user?.email">{{ user?.email }}</div>
                    <slot></slot>
                    <div>
                        <span><span class="status-indicator" :class="user.status"></span> {{ roleName }}</span>
                    </div>
                    <slot name="buttons"></slot>
                </div>
            </div>
        </div>
    </a>
</template>

<script setup>
    import { computed, ref, watchEffect } from 'vue';
    import { useUsersStore } from '@/Modules/Main/store/users'
    import { isEmpty, getProfilePicture } from '@/helpers'
    import "/color-scheme.css";
    import 'animate.css'
    import {useConfigStore} from '@/store/config'
    import {useAuthStore} from '@/store/auth'
    import _ from 'lodash'

    const usersStore = useUsersStore();
    const auth = useAuthStore()
    const currentUser = computed(() => auth.getUser)
    const configStore = useConfigStore()

    const roles = computed(() => configStore.get("Auth.roles") ?? {})

    const props = defineProps({
        userId: {
            type: Number
        },
        data: {
            type: Object,
            default: {}
        },
        baseUrl: {
            type: String,
            default: "admin/"
        },
        leadingPath: {
            type: String,
            default: "/"
        },
        role: {
            type: String,
            default: ""
        }
    })

    const realUser = computed(() => {
        if(props.userId == undefined) return {}
        if(!_.isEmpty(props.data)) return {}
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

    const roleName = computed(() => {
        if(props.role != "") return props.role
        if(roles.value[user.value.role] == undefined) return ""
        return roles.value[user.value.role].name
    })

    const getProfileLink = computed(() => {
        if(user.value.role == undefined) return "javaScript:void(0)"
        if(user.value?.id == undefined) return "javaScript:void(0)"
        if("profile" in (roles.value[user.value.role] ?? {})) {
            return (`${props.leadingPath}${roles.value[user.value.role].profile}/${user.value?.id}`).replace("//", "/").replace("//", "/");
        }
        if(currentUser.value != undefined && currentUser.value.id == user.value?.id) {
            return "/#/profile"
        }
        return (`${props.leadingPath}${props.baseUrl}#/profile/+${user.value?.id}`).replace("//", "/").replace("//", "/")
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
    .user-info {
        width: 62%;
    }
    .user-img img {
        margin: 0 !important;
        border-radius: 50%;
        width: 80%;
    }
    .user-info .email {
        white-space: nowrap; 
        width: 100%;
        max-width: 178px; 
        overflow: hidden;
        text-overflow: ellipsis; 
    }
    .user-img img.Male, .user-img img.male {
        border: 2px solid var(--blue);
    }
    .user-img img.Female, .user-img img.female {
        border: 2px solid var(--purple);
    }
    .user-info > div {
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