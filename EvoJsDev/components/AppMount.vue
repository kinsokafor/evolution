<template>
    <div class="row">
        <div class="col-12">
            <router-view></router-view>
        </div>
    </div>
    <AlertVue />
</template>

<script setup>
    import AlertVue from '@/components/Alert.vue';
</script>

<style lang="scss" scoped>

</style>