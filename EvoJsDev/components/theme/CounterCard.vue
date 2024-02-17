<template>
    <div class="main-counter-container">
        <div class="counter-container animate__animated animate__flipInX" :class="layoutStyle">
            <div class="count">
                <loading :active=processing 
                    :can-cancel="true" 
                    :is-full-page=false>
                </loading>
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
    import { onMounted, ref } from 'vue'
    import Loading from 'vue3-loading-overlay';
    import 'vue3-loading-overlay/dist/vue3-loading-overlay.css';
    import "/color-scheme.css";
    import { library } from '@fortawesome/fontawesome-svg-core'
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
    import {fas} from '@fortawesome/free-solid-svg-icons'
    import { fab } from '@fortawesome/free-brands-svg-icons';
    import { far } from '@fortawesome/free-regular-svg-icons';
    import "animate.css"
    import {Request} from '@/helpers'
    import { useLocalStorage } from '@vueuse/core'

    library.add(fas, far, fab)

    const props = defineProps({
        endPoint: String,
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

    /*list of available themes are
        default
    */
    const count = ref(useLocalStorage(`${props.endPoint}-count`, 0));
    const link = new URL(process.env.EVO_API_URL + "/" + props.endPoint);
    link.searchParams.append("iscount", 1);
    const nextRun = ref(useLocalStorage(`${props.endPoint}-nextrun`, 0))
    const processing = ref(false)

    onMounted( () => {
        const nowTime = new Date().getTime();
        if(nowTime > nextRun.value) {
            getCount().then(r => {
                nextRun.value = nowTime + (props.interval * 1000)
            });
        }
    })

    const getCount = async () => {
        processing.value = true
        const req = new Request
        return req.get(link).then(r => {
            count.value = r.data;
            processing.value = false
        })
    }
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
    }
    // default
    .counter-container.default {
        background: var(--highlight1);
        box-shadow: 1px 4px 12px -4px var(--highlight2);
    }
    .counter-container.default::before {
        content: "";
        width: 0%;
        height: 4px;
        display: block;
        background: v-bind(color);
        transition: 0.3s all linear;
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
    }
    .counter-container.default .count {
        font-size: 2.5em;
        color: var(--shadow1);
        font-weight: 700;
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
        font-size: 3.62em;
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