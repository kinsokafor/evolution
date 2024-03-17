<template>
    <div align="center">
        <h1>404</h1>
        <h3>Page not found</h3>
        <a :href="index">Click here to visit the home page</a>
    </div>
</template>

<script setup>
    import { onMounted, ref } from 'vue';
    import { Request } from '@/helpers'

    const index = ref("/#/")
    onMounted(() => {
        const req = new Request;
        req.post(`${req.root}/api/index`).then(r => {
            index.value = r.data
            window.location.href = index.value
        })
    })
</script>

<style lang="scss" scoped>

</style>