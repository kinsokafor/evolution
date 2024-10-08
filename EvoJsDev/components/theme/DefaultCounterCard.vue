<template>
    <div class="main-counter-container">
        <div class="counter-container animate__animated animate__flipInX" :class="layoutStyle">
            <div class="count">
                <small>{{prefix}}</small>{{ count }}<small>{{surfix}}</small>
            </div>
            <div class="count-title animate__animated animate__bounceIn">
                {{ title }}
            </div>
            <div class="count-icon" v-if="iconClass != ''">
                <FontAwesomeIcon :icon="iconClass"></FontAwesomeIcon>
            </div>
        </div>
    </div>
</template>

<script setup>
    import "/color-scheme.css";
    import { library } from '@fortawesome/fontawesome-svg-core'
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
    import {fas} from '@fortawesome/free-solid-svg-icons'
    import { fab } from '@fortawesome/free-brands-svg-icons';
    import { far } from '@fortawesome/free-regular-svg-icons';
    import "animate.css"

    library.add(fas, far, fab)

    const props = defineProps({
        count: {
            type: Number,
            default: 0,
        },
        title: {
            type: String,
            default: ""
        },
        interval: {
            type: Number,
            default: 600
        },
        layoutStyle: {
            type: String,
            default: "default"
        },
        color: {
            type: String,
            default: "var(--blue)"
        },
        textColor: {
            type: String,
            default: "var(--highlight1)"
        },
        iconClass: {
            type: String,
            default: ""
        },
        surfix: {
            type: String,
            default: ""
        },
        prefix: {
            type: String,
            default: ""
        }
    })
</script>

<style lang="scss" scoped>
    .main-counter-container {
        height: 88px;
        overflow: hidden;
    }
    .counter-container {
        border-radius: 3px;
        padding: 5px;
        position: relative;
        height: 76px;
    }
    // default
    .counter-container.default {
        background: var(--highlight1);
        box-shadow: 1px 4px 12px -4px var(--highlight2);
    }
    .counter-container.default::before {
        content: "";
        width: 0%;
        height: 1px;
        display: block;
        background: v-bind(color);
        transition: 0.3s all linear;
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
    }
    .counter-container.default::after {
        content: "";
        width: 100%;
        height: 1px;
        display: block;
        background: var(--shadow2);
        transition: 0.3s all linear;
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        opacity: 0.3;
    }
    .counter-container.default .count {
        font-size: 2em;
        color: var(--shadow2);
        font-weight: 400;
        line-height: normal;
        transition: 0.3s all linear;
    }
    .counter-container.default .count-title {
        font-weight: 100;
        color: var(--muted);
        transition: 0.3s all linear;
    }
    .counter-container.default .count-icon {
        position: absolute;
        top: 20px;
        right: 15px;
        color: v-bind(color);
        font-size: 18px;
        opacity: 1;
        transition: 0.3s all linear;
    }
    .counter-container.default:hover {
        display: flex;
    }
    .counter-container.default:hover::before {
        width: 100%;
    }
    .counter-container.default:hover .count {
        transform: scale(1.2) translateY(27px);
    }
    .counter-container.default:hover .count-title {
        color: v-bind(color);
        padding-top: 44px;
        padding-left: 13px;
    }
    .counter-container.default:hover .count-icon {
        opacity: 0.3;
    }
    // classic
    .counter-container.classic {
        background: var(--highlight1);
        border: 1px solid var(--muted);
        margin-top: 25px;
        margin-left: 20px;
        transition: all linear 0.3s;
    }
    .counter-container.classic .count-icon {
        display: inline-block;
        width: 80px;
        height: 80px;
        position: absolute;
        background: v-bind(color);
        border-radius: 50%;
        text-align: center;
        font-size: 1.5em;
        line-height: 2.25em;
        color: v-bind(textColor);
        right: 20px;
        top: -23px;
        transition: all linear 0.3s;
    }
    .counter-container.classic .count-icon > svg {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
    .counter-container.classic .count {
        position: absolute;
        width: 40px;
        background: v-bind(color);
        color: v-bind(textColor);
        text-align: center;
        left: -20px;
        border-radius: 4px;
        padding-left: 4px;
        padding-right: 4px;
        transition: all linear 0.3s;
    }
    .counter-container.classic .count-title {
        padding-top: 26px;
        color: var(--shadow3);
        transition: all linear 0.3s;
    }
    .counter-container.classic:hover {
        background: v-bind(color);
        border-color: v-bind(color);
        margin-left: 0;
    }
    .counter-container.classic:hover .count-title {
        padding-top: 26px;
        color: v-bind(textColor);
        font-style: italic;
        font-size: 0.9em;
        font-weight: 600;
    }
    .counter-container.classic:hover .count {
        left: 5px;
        background: v-bind(textColor);
        color: v-bind(color);
    }
    .counter-container.classic:hover .count-icon {
        font-size: 2em;
    }
    
</style>