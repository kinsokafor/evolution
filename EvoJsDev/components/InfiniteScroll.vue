<template>
    <div>
        <slot :data="output"></slot>
        <div ref="el">
            <Suspense>
                <a href="javaScript:void(0)" @click.prevent="load" v-show="state == 'ready'">Load more</a>
                <template #fallback>
                    Loading...
                </template>
            </Suspense>
        </div>
    </div>
</template>

<script setup>
    import {ref, watchEffect, onMounted, computed} from 'vue'
    import jQuery from 'jquery'

    const props = defineProps({
        data: {
            type: [Array, null],
            default: []
        },
        chunk: {
            type: Number,
            default: 20
        },
        key: {
            type: String,
            default: "id"
        }
    })

    const emit = defineEmits("load")

    const init = ref(false)

    const el = ref(null)

    const loaded = ref([])

    const position = ref(0)

    const state = ref("ready")

    const output = computed(() => {
        if(props.data.length < 1) return []
        return loaded.value.filter(i => props.data.findIndex(j => j[props.key] == i[props.key]) != -1)
    })

    const changeState = {
        loading: () => {
            state.value = "loading"
        },
        complete: () => {
            state.value = "complete"
        },
        ready: () => {
            state.value = "ready"
        },
        reset: () => {
            position.value = 0
            loaded.value = []
        }
    }

    const load = (delay = 2000) => {
        if(state.value == "complete") return;
        state.value == 'loading'
        setTimeout(() => {
            const d = props.data.slice(position.value, (position.value + props.chunk))
            if(d.length < props.chunk) {
                state.value = "complete"
            } else state.value == 'ready'
            loaded.value.push(...d)
            position.value = loaded.value.length
        }, delay)
    }

    watchEffect(() => {
        if(props.data.length > 0 && !init.value) {
            load(0)
            init.value = true
        }
    })

    onMounted(() => {
        window.addEventListener("scroll", e => {
            const wh = window.innerHeight
            const elPos = el.value.offsetTop
            const top = jQuery(window).scrollTop()
            if((top + wh > elPos) && state.value == 'ready') {
                load()
            }
        })
    })
</script>

<style lang="scss" scoped>

</style>