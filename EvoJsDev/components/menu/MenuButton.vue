<template>
    <div class="k-menu-item">
        <a :href="link" v-if="!isRouter">
            <div class="k-menu-icon">
                <FontAwesomeIcon :icon="iconClass"></FontAwesomeIcon>
            </div>
            <div class="k-menu-label"><h2>{{ label }}</h2></div>
        </a>
        <router-link :to="link" v-if="isRouter">
            <div class="k-menu-icon">
                <FontAwesomeIcon :icon="iconClass"/>
            </div>
            <div class="k-menu-label"><h2>{{ label }}</h2></div>
        </router-link>
    </div>

</template>

<script setup>
    import { library } from '@fortawesome/fontawesome-svg-core'
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
    import {fas} from '@fortawesome/free-solid-svg-icons'
    import { fab } from '@fortawesome/free-brands-svg-icons';
    import { far } from '@fortawesome/free-regular-svg-icons';
    import "/color-scheme.css";
    import { ref } from 'vue'

    library.add(fas, far, fab)

    const props = defineProps({
        label: String,
        iconClass: String,
        link: String,
        bgColor: {
            type: String,
            default: "var(--color2)"
        },
        textColor: {
            type: String,
            default: "var(--highlight1)"
        },
        isRouter: {
            type: Boolean,
            default: true
        }
    })

    const bgColor = ref(props.bgColor)
    const textColor = ref(props.textColor)
</script>

<style lang="scss">
    .k-menu-item {
        position: relative;
        z-index: 0;
        margin-bottom: 6px;
    }
    .k-menu-item::after {
        content: "";
        display: block;
        width: 0%;
        background: var(--color2);
        height: 100%;
        position: absolute;
        top: 0;
        z-index: -1;
        transition: width 1s;
    }
    // .k-menu-item:hover::after {
    //     width: 100%;
    // }
    // .k-menu-item:hover .k-menu-label h2 {
    //     color: var(--highlight1);
    // }
    .k-menu-item:hover .k-menu-icon > svg {
        font-size: 27px;
    }
    .k-menu-item > a {
        display: flex;
    }
    .k-menu-item .k-menu-icon {
        background: var(--color2);
        color: var(--highlight1);
        border-radius: 50%;
        width: 60px;
        height: 60px;
        text-align: center;
        position: relative;
    }
    .k-menu-item .k-menu-icon > svg {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 20px;
        transition: font-size 1s;
    }
    .k-menu-label {
        position: relative;
        height: 60px;
        padding-left: 10px;
        width: calc(100% - 60px);
    }
    .k-menu-item .k-menu-label h2 {
        font-size: 14px;
        text-transform: uppercase;
        position: absolute;
        top: 50%;
        transform: translate(0%, -50%);
        color: var(--shadow2);
        transition: color 1s;
    }
</style>