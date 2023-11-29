<template>
    <div class="options">
        <FontAwesomeIcon icon="fa-solid fa-ellipsis-vertical" class="iconBtn" @click.prevent="isBlock = !isBlock"/>
        <ul :class="{block: isBlock}">
            <li v-for="item in filteredItems" :key="item">
                <span v-if="item?.emit != undefined">
                    <a href="javaScript:void(0)" @click.prevent="$emit(item.emit, item)">{{item.label}}</a>
                </span>
                <span v-else>
                    <a :href="item.link" v-if="!(item?.isRouter ?? false)">{{item.label}}</a>
                    <router-link :to="item.link" v-else>{{item.label}}</router-link>
                </span>
            </li>
        </ul>
    </div>
</template>

<script setup>
    import { library } from '@fortawesome/fontawesome-svg-core'
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
    import {fas} from '@fortawesome/free-solid-svg-icons'
    import {ref, computed, watch} from 'vue'
    import {useAuthStore} from '@/store/auth';

    library.add(fas)
    const auth = useAuthStore();

    const props = defineProps({
        items: Array
    })

    const filteredItems = computed(() => props.items.filter(item => {
        auth.access = item['access'] ?? []
        if(item.condition != undefined && item.condition == false) return false 
        return auth.testAccess();
    }))

    const isBlock = ref(false)

    const timeOut = ref(null)

    watch(isBlock, () => {
        if(isBlock.value == false) return
        if(timeOut.value != null) {
            clearTimeout(timeOut.value)
        }
        timeOut.value = setTimeout(() => {
            isBlock.value = false
        }, 8000)
    })
</script>

<style lang="scss" scoped>
    .options {
        position : relative;
        ul {
            position: absolute;
            padding: 0;
            background: var(--highlight1);
            border-radius: 3px;
            transform: translateX(-50%);
            left: calc(0% + 10px);
            box-shadow: 1px 1px 2px rgba(224, 224, 224, 0.376);
            display: none;
            li {
                display: block;
                a {
                    display: block;
                    padding: 3px 8px;
                    text-align: center;
                    color: var(--shadow2);
                    text-decoration: none;
                    font-size: 0.75em;
                    text-overflow: ellipsis;
                    overflow: hidden;
                    white-space: nowrap;
                }
            }
        }
        ul.block {
            display: block;
        }
        .iconBtn {
            cursor: pointer;
            display: inline-block;
            width: 20px;
        }
    }
</style>