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
        padding: 10px;
        margin-bottom: 15px;
        box-shadow: 2px 0px 2px #6159595c;
        border-radius: 3px;
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