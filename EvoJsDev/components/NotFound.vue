<template>
    <div align="center">
        <h1>404</h1>
        <h3>Page not found</h3>
        <a href="/#/">Click here to visit the home page</a>
    </div>
</template>

<script setup>
    import { onMounted } from 'vue';

    onMounted(() => {
        window.location.href = "/#/";
    })
</script>

<style lang="scss" scoped>

</style>