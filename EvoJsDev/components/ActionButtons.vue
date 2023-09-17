<template>
    <div class="actions-container">
        <span v-for="action in getActions(actions, data)" :key="action.name">
            <a class="action-btn" :href="getUrl(action, data)" v-if="action.type == 'link'">{{action.name}}</a>
            <router-link class="action-btn" :to="getUrl(action, data)" v-if="action.type == 'router-link'">{{action.name}}</router-link>
            <a class="action-btn" href="#" v-if="action.type == 'action'" @click.prevent="$emit(action.callback, data, index)">{{action.name}}</a>
        </span>
    </div>
</template>

<script setup>
    const props = defineProps({
        actions: {
            type: Array,
            default: []
        },
        data: {
            type: Object,
            default: {}
        }
    })
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
</script>

<style lang="scss" scoped>

</style>