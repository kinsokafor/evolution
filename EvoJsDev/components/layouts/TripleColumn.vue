<template>
    <div class="row">
        <div :class="className">
            <slot name="left"></slot>
        </div>
        <div :class="className">
            <slot name="center"></slot>
        </div>
        <div :class="className">
            <slot name="right"></slot>
        </div>
    </div>
</template>

<script setup>
    const props = defineProps({
        className: {
            type: String,
            default: "col-md-4 col-lg-4 col-sm-12 col-xs-12"
        }
    })
</script>

<style lang="scss" scoped>

</style>