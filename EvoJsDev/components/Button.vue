<template>
    <button v-bind="$attrs" @click="$emit('click')">
        <loading :active=processing 
        :can-cancel="true" 
        :is-full-page=false></loading>
        <slot></slot>
    </button>
</template>

<script setup>
    import Loading from 'vue3-loading-overlay';
    import 'vue3-loading-overlay/dist/vue3-loading-overlay.css';
    const props = defineProps({
        processing: {
            type: Boolean,
            default: false
        }
    })
</script>

<script>
    export default {
        inheritAttrs: false
    }
</script>
<style lang="scss" scoped>

</style>