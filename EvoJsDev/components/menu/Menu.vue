<template>
    <div>
        <input type="text" v-if="enableSearch" v-model="searchQuery" class="search-menu mb-4" placeholder="search for...">
        <div :class="parentContainerClass" class="k-menu" v-if="!_.isEmpty(searchedMenu)">
            <slot name="header">
                <div :class="titleContainerClass" class="k-menu-header" v-if="title !== ''">
                    <h3>{{title}}</h3>
                    <hr/>
                </div>
            </slot>
            <div :class="containerClass" v-for="item in searchedMenu" :key="item.link">
                <component v-bind="item" :is="template"></component>
            </div>
        </div>
    </div>
</template>

<script setup>
    import MenuButton from './MenuButton.vue';
    import {useAuthStore} from '@/store/auth';
    import { computed, ref, watchEffect } from 'vue';
    import _ from 'lodash'

    const auth = useAuthStore();
    const searchQuery = ref("")
    const lastSQ = ref("")

    const props = defineProps({
        items: {
            type: Array,
            default: []
        },
        title: {
            type: String,
            default: ""
        },
        containerClass: {
            type: String,
            default: "col-md-3"
        },
        parentContainerClass: {
            type: String,
            default: "row"
        },
        titleContainerClass: {
            type: String,
            default: "col-12"
        },
        template: {
            type: Object,
            default: MenuButton
        },
        enableSearch: {
            type: Boolean,
            default: true
        },
        search: {
            type: String,
            default: ""
        }
    })

    watchEffect(() => {
        searchQuery.value = props.search
    })

    const menuItems = ref([])

    watchEffect(() => {
        menuItems.value = props.items.map(x => {
            x.access = x.access ?? []
            if(typeof(x.access) == 'string') {
                x.access = auth.toArray(x.access)
            }
            return x;
        })
    })

    const searchedMenu = computed(() => {
        return menuItems.value.filter(item => {
            if(item.condition != undefined && item.condition == false) return false 
            return auth.testAccess(item.access);
        }).filter((m) => {
            return (
              m.label
                .toLowerCase()
                .indexOf(searchQuery.value.toLowerCase()) != -1
            );
        });
    });
</script>

<style lang="scss" scoped>
    .search-menu {
        border: 1px solid var(--highlight2);
        background: var(--highlight1);
        padding: 5px 10px;
        width: 100%;
        max-width: 300px;
        border-radius: 5px;
    }
    .search-menu::placeholder {
        font-style: italic;
        color: #c6c6c6;
        font-size: 14px;
    }
</style>