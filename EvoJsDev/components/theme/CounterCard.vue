<template>
    <div>
        <component :is="template" v-bind="$props" :count="parseInt(count)"></component>
    </div>
</template>

<script setup>
    import { onMounted, ref, onUnmounted } from 'vue'
    import "/color-scheme.css";
    import {Request} from '@/helpers'
    import { useSessionStorage } from '@vueuse/core'
    import DefaultCounterCard from './DefaultCounterCard.vue';

    const req = new Request

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
        },
        template: {
            type: Object,
            default: DefaultCounterCard
        },
        link: {
            type: [String, Boolean],
            default: false
        },
        method: {
            type: String,
            default: "get"
        },
        postData: {
            type: Object,
            default: {}
        }
    })

    /*list of available themes are
        default
    */
    const count = ref(useSessionStorage(`${props.endPoint}-count`, 0));
    const link = new URL(process.env.EVO_API_URL + "/" + props.endPoint);
    link.searchParams.append("iscount", 1);
    const nextRun = ref(0);

    onMounted( async () => {
        const nowTime = new Date().getTime();
        if(nowTime > nextRun.value) {
            await getCount().then(r => {
                nextRun.value = nowTime + (props.interval * 1000)
            });
        }
    })

    onUnmounted(() => {
        req.abort()
    })

    const getCount = async () => {
        switch (props.method.toLowerCase()) {
            case "post":
                return await req.post(link, props.postData).then(r => {
                    count.value = r.data;
                })
            break;

            default:
                return await req.get(link).then(r => {
                    count.value = r.data;

                })
            break
        }
        
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