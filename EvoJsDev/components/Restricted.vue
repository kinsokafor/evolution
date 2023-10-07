<template>
    <div class="access-restricted-area">
        <slot v-if="authStore.getAccess"></slot>
        <div v-if="!authStore.getAccess" class="no-access">
            <slot name="message">
                <div class="security-check">
                    <img :src="root+jpg" alt="" class="animate__animated animate__pulse animate__infinite">
                    <em v-if="authStore.failedTest">Please wait: security check</em>
                    <em v-if="!authStore.failedTest">Security check failed. Go to <a :href="root">Dashboard</a> or <a :href="root+appData().loginLink">Login</a> again</em>
                </div>
            </slot>
        </div>
    </div>
</template>

<script setup>
    import { useAuthStore } from '@/store/auth';
    import { onBeforeMount, ref } from 'vue';
    import jpg from './images/undraw_security_on_re_e491.svg'
    import {appData} from '@/helpers'
    import 'animate.css'

    const root = ref(process.env.EVO_API_URL)
    const authStore = useAuthStore();
    const props = defineProps({
        access: {
            type: String,
            default: ""
        }
    })

    onBeforeMount(() => {
        authStore.access = props.access.trim() == "" ? [] : props.access.trim().split(",").map((x) => parseInt(x));
    })
    
</script>

<style lang="scss" scoped>
    .no-access {
        margin-top: 30px;
    }
    .security-check[data-v-12176841] {
        position: fixed;
        width: 100vw;
        height: 100vh;
        background-color: rgba(255, 255, 255, 0.69);
        display: flex;
        flex-direction: column;
        top: 0;
        left: 0;
        justify-content: center;
        align-items: center;
    }
    .security-check img {
        display: block;
        width: 80px;
    }
</style>