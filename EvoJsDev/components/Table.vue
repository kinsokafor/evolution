<template>
    <div :class="containerClass">
        <loading :active=processing 
            :can-cancel="true" 
            :is-full-page=false>
        </loading>
        <table :class="tableClass">
            <thead>
                <tr>
                    <th v-if="serialNumber != null">SN</th>
                    <th v-for="(column, index) in columns" :key="column" @click="sortNow(index)">{{column}}</th>
                    <th v-if="actions.length > 0">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(row, index) in data" :key="row.id">
                    <td v-if="serialNumber != null">{{getIndex(index)}}</td>
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
        </table>
    </div>
</template>

<script setup>
    import { dynamicSort } from '@/helpers';
    import Loading from 'vue3-loading-overlay';
    import 'vue3-loading-overlay/dist/vue3-loading-overlay.css';
    import "/color-scheme.css";

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
        serialNumber: {
            type: Number,
            default: null
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
            type: Object,
            default: null
        }
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

    function sortNow(column) {
        props.data.sort(dynamicSort(column))
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
</style>

<style>
    table td > a {
        color: inherit;
    }
</style>