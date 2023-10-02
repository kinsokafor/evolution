<template>
    <div class="list-item-container animate__animated animate__fadeInUp">
        <div class="list-item-flex">
            <div class="list-left">
                <slot></slot>
            </div>
            <div class="list-right">
                <slot name="right"></slot>
            </div>
        </div>
        
    </div>
</template>

<script setup>
    import 'animate.css'

    const props = defineProps({
        header: {
            type: String,
            default: null
        },
        footer: {
            type: String,
            default: null
        }
    })
</script>

<style lang="scss" scoped>
    * {
        padding: 0;
        margin: 0;
        line-height: normal;
    }
    .list-item-container .list-item-flex {
        display: flex;
        justify-content: space-between;
        gap: 5px;
    }
    .list-item-container {
        padding: 10px;
        margin-bottom: 15px;
        box-shadow: 2px 0px 2px #6159595c;
        border-radius: 3px;
    }
    .list-item-container .list-right {
        text-align:right;
    }
</style>

<style>
    .list-item-container .left {
        word-wrap: anywhere;
        overflow: clip;
        max-width: 500px;
    }
    .list-item-container .small {
        font-size: 0.8rem;
    }
</style>