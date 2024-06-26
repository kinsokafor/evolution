<template>
    <div :class="containerClass">
        <loading :active=processing 
            :can-cancel="true" 
            :is-full-page=false>
        </loading>
        <data-filter 
            :search-columns="columns" 
            :data="data" v-slot="{outputData, page, limit}" 
            :quick-filters="quickFilters"
            :sort-by="sortBy">
            <slot name="before"></slot>
            <table :class="tableClass">
                <thead>
                    <slot name="row-head-before" :cols="Object.keys(columns).length"></slot>
                    <tr>
                        <th>SN</th>
                        <th v-for="(column, index) in columns" :key="column" @click="setSortBy(index)">{{getHeading(column)}}</th>
                        <th v-if="actions.length > 0">Actions</th>
                    </tr>
                    <slot name="row-head-after" :cols="Object.keys(columns).length"></slot>
                </thead>
                <tbody>
                    <slot name="row-body-before" :cols="Object.keys(columns).length"></slot>
                    <tr v-for="(row, index) in outputData" :key="row.id">
                        <td>{{index + ((page - 1) * limit) + 1}}</td>
                        <td v-for="(dcolumn, index) in columns" :key="dcolumn" v-html="getContent(dcolumn, row[index], row)"></td>
                        <td v-if="actions.length > 0">
                            <div class="actions-container">
                                <span v-for="action in getActions(actions, row)" :key="action.name">
                                    <a class="action-btn" :href="getUrl(action, row)" v-if="action.type == 'link'">{{action.name}}</a>
                                    <router-link class="action-btn" :to="getUrl(action, row)" v-if="action.type == 'router-link'">{{action.name}}</router-link>
                                    <a class="action-btn" href="#" v-if="action.type == 'action'" @click.prevent="$emit(action.callback, row, index)">{{action.name}}</a>
                                </span>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="outputData.length == 0">
                        <td :colspan="(Object.keys(columns).length + 1)"><em>Nothing found...</em></td>
                        <td v-if="actions.length > 0"></td>
                    </tr>
                    <slot name="row-body-after" :cols="Object.keys(columns).length"></slot>
                </tbody>
                <tfoot>
                    <slot name="row-foot" :cols="Object.keys(columns).length"></slot>
                </tfoot>
            </table>
            <slot name="after"></slot>
        </data-filter>
    </div>
</template>

<script setup>
    import { dynamicSort } from '@/helpers';
    import Loading from 'vue3-loading-overlay';
    import 'vue3-loading-overlay/dist/vue3-loading-overlay.css';
    import "/color-scheme.css";
    import {computed, ref} from 'vue'
    import DataFilter from './filters/DataFilter.vue';

    const props = defineProps({
        tableClass: {
            type: String,
            default: "table"
        },
        containerClass: {
            type: String,
            default: "table-responsive"
        },
        columns: {
            type: Object,
            default: {}
        },
        actions: {
            type: Array,
            default: []
        },
        processing: {
            type: Boolean,
            default: false
        },
        data: {
            type: Array,
            default: []
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

    // const computedData = computed(() => {
    //     var d = [...props.data]
    //     if(sortBy.value !== null) {
    //         d.sort(dynamicSort(sortBy.value))
    //     }
    //     var end = page.value * limit.value
    //     var start = (page.value - 1) * limit.value;
    //     if(search.value != "") {
    //         if(limit.value == 0) return d.filter(i => {
    //             for(var j in props.columns) {
    //                 if(i[j].toString().toLowerCase().search(search.value.toLowerCase()) != -1) {
    //                     return true
    //                 }
    //             }
    //             return false
    //         });
    //         return d.filter(i => {
    //             for(var j in props.columns) {
    //                 if(i[j].toString().toLowerCase().search(search.value.toLowerCase()) != -1) {
    //                     return true
    //                 }
    //             }
    //             return false
    //         }).slice(start, end)
    //     }
    //     if(limit.value == 0) return d;
    //     return d.slice(start, end)
    // })

    const getContent = (column, content, row) => {
        if(typeof column == "object") {
            if(typeof column?.processor == "function") {
                content = column.processor.call(row)
            }
        }
        if(row?.link == undefined) return content;
        return `<a href="${row?.link ?? '#'}">${content}</a>`
    }

    const getHeading = (column) => {
        if(typeof column == "string") {
            return column;
        }
        else if(typeof column == "object") {
            return column?.heading ?? ""
        }
        else return ""
    }

    function getActions(actions, row) {
        return actions.filter((action) => {
            if(action.conditions !== undefined) {
                for(var condition in action.conditions) {
                    if(action.conditions[condition] !== row[condition]) return false;
                }
            }
            if(action.notConditions !== undefined) {
                for(var condition in action.notConditions) {
                    if(action.notConditions[condition] == row[condition]) return false;
                }
            }
            return true;
        })
    }

    function getUrl(action, row) {
        if(action.params == undefined) return action.url;
        var url = action.url;
        if(typeof(action.params) == "string") {
            if(Object.hasOwnProperty.call(row, action.params)) {
                return url.replaceAll("{"+action.params+"}", row[action.params]);
            }
        }
        else if(typeof(action.params) == "object") {
            action.params.forEach(param => {
                if(Object.hasOwnProperty.call(row, param)) {
                    url = url.replaceAll("{"+param+"}", row[param]);
                }
            });
        }
        return url;
    }

    function setSortBy(index) {
        if(index == sortBy.value) {
            sortBy.value = `-${index}`
        }
        else {
            sortBy.value = index
        }
    }

    // const setPage = (index) => {
    //     if(index == "<") {
    //         page.value = pageArray.value[pageArray.value.findIndex(i => i == "<")+1]-1
    //         return
    //     }
    //     if(index == ">") {
    //         page.value = pageArray.value[pageArray.value.findIndex(i => i == ">")-1]+1
    //         return
    //     }
    //     page.value = index
    // }
</script>

<style lang="scss" scoped>
    table td {
        vertical-align: middle;
    }
</style>

<style>
    table td > a {
        color: inherit;
    }
</style>