<template>
    <div class="row">
        <div class="col-12">
            <router-view></router-view>
        </div>
    </div>
    <AlertVue />
    <container></container>
</template>

<script setup>
    import AlertVue from '@/components/Alert.vue';
    import {container} from "jenesius-vue-modal";
    import {useRouter} from 'vue-router'

    const router = useRouter()

    if(document.getElementById('goBackBtn') !== null) {
        document.getElementById("goBackBtn").onclick = function() {
            router.back()
        }
    }
    
</script>

<style lang="scss" scoped>

</style>