<template>
    <div class="data-filters">
        <data-filter-tools 
            :pageArray="pageArray" 
            :page="page" 
            :computedData="computedData" 
            :data="data"
            v-model="limit"
            @setPage="setPage"
            @print="print">
            <div class="search">
                <input v-model="search" @input="page = 1" class="search-input" :class="appData().inputFieldClass ?? ''"/>
            </div>
        </data-filter-tools>
        <div id="dataprint">
            <slot :output-data="computedData" :page="page" :limit="limit"></slot>
        </div>
        <data-filter-tools 
            :pageArray="pageArray" 
            :page="page" 
            :computedData="computedData" 
            :data="data"
            v-model="limit"
            @setPage="setPage"
            @print="print"></data-filter-tools>
    </div>
</template>

<script setup>
    import { dynamicSort } from '@/helpers';
    import 'vue3-loading-overlay/dist/vue3-loading-overlay.css';
    import "/color-scheme.css";
    import {computed, ref} from 'vue'
    import { appData, Print } from '@/helpers'
    import DataFilterTools from './DataFilterTools.vue';

    const props = defineProps({
        searchColumns: {
            type: [Object, Array],
            default: {}
        },
        data: {
            type: Array,
            default: []
        },
        dataCaption: {
            type: String,
            default: "Print out"
        }
    })

    const sortBy = ref(null)

    const limit = ref(50)

    const page = ref(1)

    const search = ref("")

    const pageSize = computed(() => {
        let l = props.data.length ?? 0
        const max = limit.value == 0 ? 1 : Math.ceil(l/limit.value)
        if(page.value > max && l != 0) {
            page.value = max
        }
        return max;
    })

    const pageArray = computed(() => {
        var items = [1]
        if(page.value >= 1 && page.value <= 4) {
            for (let i = 2; i <= (pageSize.value < 4 ? pageSize.value : 4); i++) {
                items.push(i)
            }
        } else items.push("<")
        for(let i = (pageSize.value - 7) > page.value ? page.value : pageSize.value - 7; i <= (page.value + 3); i++) {
            if(pageSize.value > 4 && i > 4 && i < (pageSize.value - 4) && (pageSize.value - 4) > page.value) {
                items.push(i)
            }
        }
        if((pageSize.value - 4) <= page.value) {
            for (let i = (pageSize.value - 4); i <= pageSize.value; i++) {
                if(i > 4) {
                    items.push(i)
                }
            }
        } else {
            items.push(">")
            items.push(pageSize.value)
        }
        return items
    })

    const computedData = computed(() => {
        var d = [...props.data]
        if(sortBy.value !== null) {
            d.sort(dynamicSort(sortBy.value))
        }
        var end = page.value * limit.value
        var start = (page.value - 1) * limit.value;
        if(search.value != "") {
            if(limit.value == 0) return d.filter(i => {
                for(var j in props.searchColumns) {
                    const k = Array.isArray(props.searchColumns) ? props.searchColumns[j] : j;
                    if(i[k].toString().toLowerCase().search(search.value.toLowerCase()) != -1) {
                        return true
                    }
                }
                return false
            });
            return d.filter(i => {
                for(var j in props.searchColumns) {
                    const k = Array.isArray(props.searchColumns) ? props.searchColumns[j] : j;
                    console.log(k)
                    if(i[k].toString().toLowerCase().search(search.value.toLowerCase()) != -1) {
                        return true
                    }
                }
                return false
            }).slice(start, end)
        }
        if(limit.value == 0) return d;
        return d.slice(start, end)
    })

    const setPage = (index) => {
        if(index == "<") {
            page.value = pageArray.value[pageArray.value.findIndex(i => i == "<")+1]-1
            return
        }
        if(index == ">") {
            page.value = pageArray.value[pageArray.value.findIndex(i => i == ">")-1]+1
            return
        }
        page.value = index
    }

    const print = () => {
        let p = new Print({el: '#dataprint', popTitle: props.dataCaption})
    }
</script>

<style lang="scss" scoped>
    .action-btn {
        background: var(--highlight3);
        text-align: center;
        padding: 4px;
        font-size: 11px;
        color: var(--highlight1);
        text-decoration: none;
        border-radius: 4px;
        border: 1px solid var(--highlight3);
        box-sizing: content-box;
        margin-right: 2px;
        transition: all 0.4s linear;
    }

    .action-btn:hover {
        background: var(--color2);
        border: 1px solid var(--color2);
    }

    .tools {
        display: flex;
        justify-content: space-between;
        .controls div {
            margin-bottom: 5px;
        }
    }
</style>