<template>
    <div class="filters row">
        <slot :key="filterKey" v-if="!hideFilters"></slot>
        <div class="controls">
            <a v-if="hideFilters" href="javascript:void(0)" @click="hideFilters = !hideFilters">Show filters</a>
            <a v-if="!hideFilters" href="javascript:void(0)" @click="hideFilters = !hideFilters">Hide filters</a>
            <a href="javascript:void(0)" @click="filterStore.reset(filterKey)">Reset filters</a>
        </div>
        
    </div>
</template>

<script setup>
    import 'vue3-loading-overlay/dist/vue3-loading-overlay.css';
    import { useFilterStore } from '@/store/filter';
    import { onBeforeMount, ref } from 'vue';
    import { useLocalStorage } from '@vueuse/core';
    import "/color-scheme.css"
    const filterStore = useFilterStore();

    const props = defineProps({
        filterKey: {
            type: String,
            default: "default"
        },
        hideFilters: {
            type: Boolean,
            default: false
        }
    })

    const hideFilters = ref(props.hideFilters)

    onBeforeMount(() => {
        filterStore.filters[props.filterKey] = useLocalStorage(`filter-${props.filterKey}`, {})
    })
    
</script>

<style lang="scss">
    .filters {
        padding: 10px 10px 5px 10px;
        margin-top: 15px;
        margin-bottom: 20px;
        position: relative;
    }

    .filters .filter-container{
        margin-bottom: 5px;
    }

    .filters .controls {
        position: absolute;
        right: 22px;
        bottom: -11px;
    }

    .filters .controls a {
        font-size: 0.8em;
        color: var(--shadow1);
        text-decoration: none;
        padding: 5px;
    }

    .filters .controls a:hover {
        color: var(--color2)
    }
</style>