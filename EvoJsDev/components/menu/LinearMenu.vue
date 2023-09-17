<template>
    <div :class="parentContainerClass" class="k-menu">
        <div :class="titleContainerClass" class="k-menu-header" v-if="title !== ''">
            <h3>{{title}}</h3>
            <hr/>
        </div>
        <ul>
            <li v-for="item in menuItems" :key="item.link">
                <MenuButtonLinear v-bind="item"/>
            </li>
        </ul>
    </div>
</template>

<script setup>
    import MenuButtonLinear from './MenuButtonLinear.vue'
    import {useAuthStore} from '@/store/auth';
    import { computed } from 'vue';

    const auth = useAuthStore();

    const props = defineProps({
        items: {
            type: Array,
            default: []
        },
        title: {
            type: String,
            default: ""
        },
        parentContainerClass: {
            type: String,
            default: "row"
        },
        titleContainerClass: {
            type: String,
            default: "col-12"
        }
    })

    const menuItems = computed(() => {
        return props.items.filter(item => {
            return auth.testAccess(item['access'] ?? []);
        })
    })
</script>

<style lang="scss" scoped>
    .k-menu li {
        display: block;
        margin-bottom: 5px;
    }
</style>