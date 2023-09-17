<template>
    <div class="row">
        <div :class="className">
            <slot></slot>
        </div>
    </div>
</template>

<script setup>
    const props = defineProps({
        className: {
            type: String,
            default: "col-md-12"
        }
    })
</script>

<style lang="scss" scoped>

</style>