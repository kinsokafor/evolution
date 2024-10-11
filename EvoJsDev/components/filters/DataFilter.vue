<template>
    <div class="data-filters">
        <data-filter-tools 
            :pageArray="pageArray" 
            :page="page" 
            :computedData="computedData" 
            :data="filteredData"
            v-model="limit"
            @setPage="setPage"
            @print="print">
            <div class="search">
                <div>
                    <input v-model="search" @input="page = 1" class="search-input" :class="appData().inputFieldClass ?? ''"/>
                </div>
            </div>
            <template #newLine>
                <div class="filters-container">
                    <button 
                        v-for="filter in quickFilters" 
                        :key="filter.key"
                        class="filter-btn text-nowrap"
                        :class="{active: (selFilters[filter.key] == filter.value)}"
                        @click.prevent="toggleFilter(filter)"
                    >{{filter.label}}</button>
                </div>
            </template>
        </data-filter-tools>
        <div id="dataprint">
            <slot :output-data="computedData" :page="page" :limit="limit"></slot>
        </div>
        <data-filter-tools 
            :pageArray="pageArray" 
            :page="page" 
            :computedData="computedData" 
            :data="filteredData"
            v-model="limit"
            @setPage="setPage"
            @print="print"
            position="footer">
        </data-filter-tools>
    </div>
</template>

<script setup>
    import { dynamicSort } from '@/helpers';
    import 'vue3-loading-overlay/dist/vue3-loading-overlay.css';
    import "/color-scheme.css";
    import {computed, ref, watchEffect} from 'vue'
    import { appData, Print, linkParams } from '@/helpers'
    import DataFilterTools from './DataFilterTools.vue';
    import _ from 'lodash'

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
        },
        quickFilters: {
            type: Array,
            default: [],
            validator(value, props) {
                let valid = true
                value.forEach(i => {
                    if(i?.label == undefined) valid = false 
                    if(i?.key == undefined) valid = false 
                    if(i?.value == undefined) valid = false 
                })
                return valid
            }
        },
        filters: {
            type: Object,
            default: {}
        },
        sortBy: {
            type: [String, null],
            default: null
        }
    })

    const limit = ref(50)

    const page = ref(1)

    const search = ref("")

    const pageSize = computed(() => {
        let l = filteredData.value.length ?? 0
        const max = limit.value == 0 ? 1 : Math.ceil(l/limit.value)
        if(page.value > max && l != 0) {
            page.value = max
        }
        return max;
    })

    const selFilters = ref(props.filters);

    watchEffect(() => {
        const hrefFilters = linkParams()
        selFilters.value = {...hrefFilters, ...props.filters}
    })

    const toggleFilter = (filter) => {
        if(filter.key in selFilters.value) {
            if(selFilters.value[filter.key] == filter.value) {
                delete selFilters.value[filter.key]
            } else {
                selFilters.value[filter.key] = filter.value
            }
            
        } else {
            selFilters.value[filter.key] = filter.value
        }
    }

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

    const filteredData = computed(() => {
        var d = [...props.data]
        if(!_.isEmpty(selFilters.value)) {
            return d.filter(i => {
                let t = true
                for(var k in selFilters.value) {
                    if(props.quickFilters.findIndex(j => j.key == k) != -1) {
                        if(i[k] != selFilters.value[k]) t = false
                    }
                }
                return t
            })
        } else return d
    })

    const computedData = computed(() => {
        var d = filteredData.value

        if(props.sortBy !== null) {
            d.sort(dynamicSort(props.sortBy))
        }
        var end = page.value * limit.value
        var start = (page.value - 1) * limit.value;
        if(search.value != "") {
            d = d.filter(i => {
                for(var j in props.searchColumns) {
                    const k = Array.isArray(props.searchColumns) ? props.searchColumns[j] : j;
                    if(k in i) {
                        if(i[k].toString().toLowerCase().search(search.value.toLowerCase()) != -1) {
                            return true
                        }
                    }
                }
                return false
            });
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

    
    .filters-container {
        justify-content: end;
        gap: 3px;
        margin-top: 1px;
        margin-bottom: 21px;
        display: flex;
        flex-wrap: wrap;
        .filter-btn {
            border: none;
            padding: 1.4px 8px 5px;
            line-height: 1;
            font-size: 11px;
            text-transform: lowercase;
            border-radius: 10px;
            background-color: var(--highlight3);
            color: var(--shadow3);
        }
        .filter-btn.active {
            background-color: var(--color2);
            color: var(--highlight1);
        }
    }
     /* Extra small devices (phones, 600px and down) */
    @media only screen and (max-width: 600px) {}

    /* Small devices (portrait tablets and large phones, 600px and up) */
    @media only screen and (min-width: 600px) {}

    /* Medium devices (landscape tablets, 768px and up) */
    @media only screen and (min-width: 768px) {
        .tools {
            flex-direction: row;
        }
    }

    /* Large devices (laptops/desktops, 992px and up) */
    @media only screen and (min-width: 992px) {}

    /* Extra large devices (large laptops and desktops, 1200px and up) */
    @media only screen and (min-width: 1200px) {} 
</style>