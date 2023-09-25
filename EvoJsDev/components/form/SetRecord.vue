<template>
    <SetOption as="records" :fields="fields" />
</template>

<script setup>
    import SetOption from './SetOption.vue';
    const props = defineProps({
        fields: Array
    })
</script>

<style lang="scss" scoped>

</style>