<template>
    <div :class="containerClass">
        <loading :active=processing 
            :can-cancel="true" 
            :is-full-page=false>
        </loading>
        <table :class="tableClass">
            <thead>
                <tr>
                    <td :colspan="Object.keys(columns).length + 2">
                        <div class="tools">
                            <div class="controls">
                                <div>
                                    <div class="btn-group">
                                        <button 
                                            class="btn btn-primary btn-sm"
                                            v-for="index in pageArray"
                                            :key="index"
                                            @click="setPage(index)"
                                            :class="{active: page == index}"
                                            >{{ index }}</button>
                                    </div>
                                </div>
                                <div>
                                    <select v-model="limit">
                                        <option :value="20">20</option>
                                        <option :value="50">50</option>
                                        <option :value="75">75</option>
                                        <option :value="100">100</option>
                                        <option :value="200">200</option>
                                        <option :value="500">500</option>
                                        <option :value="0">All</option>
                                    </select>
                                    <em> showing {{ computedData.length }} of {{ data.length }} records on page {{ page }}</em>
                                </div>
                            </div>
                            <div class="search">
                                <input v-model="search" @input="page = 1" class="search-input" :class="appData().inputFieldClass ?? ''"/>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>SN</th>
                    <th v-for="(column, index) in columns" :key="column" @click="setSortBy(index)">{{column}}</th>
                    <th v-if="actions.length > 0">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(row, index) in computedData" :key="row.id">
                    <td>{{index + ((page - 1) * limit) + 1}}</td>
                    <td v-for="(dcolumn, index) in columns" :key="dcolumn" v-html="getContent(row[index], row.link)"></td>
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
            </tbody>
            <tfoot>
                
            </tfoot>
        </table>
    </div>
</template>

<script setup>
    import { dynamicSort } from '@/helpers';
    import Loading from 'vue3-loading-overlay';
    import 'vue3-loading-overlay/dist/vue3-loading-overlay.css';
    import "/color-scheme.css";
    import {computed, ref} from 'vue'
    import { appData } from '@/helpers'

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
        }
    })

    const sortBy = ref(null)

    const limit = ref(50)

    const page = ref(1)

    const search = ref("")

    const pageSize = computed(() => {
        let l = props.data.length ?? 0
        const max = limit.value == 0 ? 1 : Math.ceil(l/limit.value)
        if(page.value > max) {
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
                for(var j in props.columns) {
                    if(i[j].toLowerCase().search(search.value.toLowerCase()) != -1) {
                        return true
                    }
                }
                return false
            });
            return d.filter(i => {
                for(var j in props.columns) {
                    if(i[j].toLowerCase().search(search.value.toLowerCase()) != -1) {
                        return true
                    }
                }
                return false
            }).slice(start, end)
        }
        if(limit.value == 0) return d;
        return d.slice(start, end)
    })

    const getContent = (content, link) => {
        return `<a href="${link ?? '#'}">${content}</a>`
    }

    function getIndex(index) {
        return props.serialNumber + index;
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
    table td {
        vertical-align: middle;
    }
    .tools {
        display: flex;
        justify-content: space-between;
        .controls div {
            margin-bottom: 5px;
        }
    }
</style>

<style>
    table td > a {
        color: inherit;
    }
</style>