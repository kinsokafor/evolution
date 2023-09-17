<template>
    <div class="k-linear-menu-item">
        <a :href="link" v-if="!isRouter">
            <div class="k-linear-menu-icon">
                <FontAwesomeIcon :icon="iconClass"></FontAwesomeIcon>
            </div>
            <div class="k-linear-menu-label"><h2>{{ label }}</h2></div>
        </a>
        <router-link :to="link" v-if="isRouter">
            <div class="k-linear-menu-icon">
                <FontAwesomeIcon :icon="iconClass"/>
            </div>
            <div class="k-linear-menu-label"><h2>{{ label }}</h2></div>
        </router-link>
    </div>

</template>

<script setup>
    import { library } from '@fortawesome/fontawesome-svg-core'
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
    import {fas} from '@fortawesome/free-solid-svg-icons'
    import { fab } from '@fortawesome/free-brands-svg-icons';
    import { far } from '@fortawesome/free-regular-svg-icons';

    library.add(fas, far, fab)

    const props = defineProps({
        label: String,
        iconClass: String,
        link: String,
        isRouter: {
            type: Boolean,
            default: true
        }
    })
</script>

<style lang="scss">
    .k-linear-menu-item a {
        display: flex;
    }

    .k-linear-menu-label h2 {
        font-size: 16px;
        padding-left: 6px;
    }
    .k-linear-menu-item a {
        text-decoration: none;
        color: #848484;
        background: #f7f7f7;
        padding: 10px 10px 7px;
    }
</style>