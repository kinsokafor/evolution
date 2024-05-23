<template>
    <div class="list-item-container animate__animated animate__fadeInUp">   
        <div class="header" v-if="header != null">
            <h3>{{ header }}</h3>
        </div>
        <div class="list-item-flex">
            <div class="list-left"
                @click="goToLink"
                :class="{pointer: (href != '')}"
            >
                <slot></slot>
            </div>
            <div class="list-right">
                <slot name="right"></slot>
            </div>
        </div>
        <div class="footer" v-if="footer != null">
            <small>{{ footer }}</small>
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
        },
        href: {
            type: String,
            default: ""
        }
    })

    const goToLink = () => {
        if(props.href != "" && props.href != "#") {
            window.location = props.href
        }
    }
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
        padding: 15px 10px;
        margin-bottom: 15px;
        box-shadow: 0px 8px 10px -10px rgba(97, 89, 89, 0.3607843137);
        border-radius: 3px;
        border: 1px solid rgba(0, 0, 0, 0.031);
        // .list-right {
        //     display: flex;
        //     flex-direction: column;
        // }
    }
    .list-item-container .list-right {
        text-align:right;
    }
    .pointer {
        cursor: pointer;
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