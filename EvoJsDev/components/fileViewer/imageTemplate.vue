<template>
    <div class="img-responsive">
        <span class="close-btn" @click.prevent="closeModal"></span>
        <img class="img-fluid" :src="img" :alt="title"/>
    </div>
</template>

<script setup>
    import {closeModal} from "jenesius-vue-modal";
    const props = defineProps({
        img: String,
        title: {
            type: String,
            default: ""
        }
    })
</script>

<style lang="scss" scoped>
    .img-responsive {
        position: relative;
    }
    .close-btn {
        position: absolute;
        top: 73px;
        right: 14px;
        cursor: pointer;
        background: var(--highlight1);
        border-radius: 3px;
        display: flex;
        width: 30px;
        height: 30px;
        justify-content: center;
        align-items: center;
        opacity: 0.5;
        transition: 0.3s linear;
    }
    .close-btn:hover {
        opacity: 1;
    }
    .close-btn::before {
        content: "";
        width: 2px;
        height: 18px;
        transform: rotate(45deg);
        display: inline-block;
        background-color: var(--color2);
        border-radius: 1px;
    }
    .close-btn::after {
        content: "";
        width: 2px;
        height: 18px;
        transform: rotate(-45deg) translate(-1px, -1px);
        display: inline-block;
        background-color: var(--color2);
        border-radius: 1px;
    }
</style>