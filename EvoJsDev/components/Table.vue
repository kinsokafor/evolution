<template>
    <div :class="containerClass">
        <restricted access="">
            <template #message>
                <span></span>
            </template>
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
                            <th v-if="useActions.length > 0">Actions</th>
                        </tr>
                        <slot name="row-head-after" :cols="Object.keys(columns).length"></slot>
                    </thead>
                    <tbody>
                        <slot name="row-body-before" :cols="Object.keys(columns).length"></slot>
                        <tr v-for="(row, index) in outputData" :key="row.id">
                            <td>{{index + ((page - 1) * limit) + 1}}</td>
                            <td v-for="(dcolumn, index) in columns" :key="dcolumn" v-html="getContent(dcolumn, row[index], row)"></td>
                            <td v-if="useActions.length > 0">
                                <div class="actions-container">
                                    <span v-for="action in getActions(useActions, row)" :key="action.name">
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
        </restricted>
    </div>
</template>

<script setup>
    import {useAuthStore} from '@/store/auth'
    import Loading from 'vue3-loading-overlay';
    import 'vue3-loading-overlay/dist/vue3-loading-overlay.css';
    import "/color-scheme.css";
    import {computed, ref} from 'vue'
    import DataFilter from './filters/DataFilter.vue';

    const auth = useAuthStore();

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

    const useActions = computed(() => {
        return props.actions.filter(i => {
            var access = i.access ?? [];
            if(typeof(access) == "string") {
                access = auth.toArray(access)
            }
            return auth.testAccess(access)
        })
    })

    const pageSize = computed(() => {
        let l = props.data.length ?? 0
        const max = limit.value == 0 ? 1 : Math.ceil(l/limit.value)
        if(page.value > max && l != 0) {
            page.value = max
        }
        return max;
    })

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