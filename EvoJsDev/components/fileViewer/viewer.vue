<template>
    <div>
        <ListItem v-for="item in filesArr" :key="item" class="file">
            <p>{{ getName(item) }}</p>
            <template #right>
                <div class="d-flex">
                    <a href="javaScript:void(0)" class="download-btn" @click.prevent="pushModal(imageTemplate, {img: item})">
                        <FontAwesomeIcon icon="fas fa-eye"/>
                    </a>
                    <a :href="item" download class="download-btn">
                        <FontAwesomeIcon icon="fas fa-download"/>
                    </a>
                </div>
            </template>
        </ListItem>
        
    </div>
</template>

<script setup>
    import {computed} from 'vue'
    import ListItem from '../theme/ListItem.vue'
    import { library } from '@fortawesome/fontawesome-svg-core'
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
    import {fas} from '@fortawesome/free-solid-svg-icons'
    import {pushModal} from "jenesius-vue-modal";
    import imageTemplate from './imageTemplate.vue'

    library.add(fas)

    const props = defineProps(["files"])

    const filesArr = computed(() => {
        if(typeof(props.files) == "string") {
            return [props.files]
        }
        return props.files
    })

    const getName = function(file) {
        return file.split("/").slice(-1)[0]
    }

    const getExt = function(file) {
        return getName(file).split(".").slice(-1)[0]
    }
</script>

<style lang="scss" scoped>
    .file {
        line-height: 2.2;
        font-size: 0.8rem;
        color: #798079;
        text-transform: lowercase;
    }
    .download-btn {
        color: var(--shadow2);
        background: var(--highlight2);
        display: flex;
        border-radius: 50px;
        width: 27px;
        height: 27px;
        justify-content: center;
        align-items: center;
        transition: 0.2s linear;
    }
    .d-flex {
        display: flex;
        gap: 5px;
    }
    @keyframes changeColor {
        0% {
            box-shadow: 0 0 0px rgba(255, 255, 255, 0.541);
        }

        30% {
            box-shadow: 0 0 1px rgba(237, 233, 10, 0.635);
        }

        60% {
            box-shadow: 0 0 2px rgba(103, 77, 255, 0.541)        }
        80% {
            box-shadow: 0 0 1px rgba(248, 27, 230, 0.541)        }
        100% {
            box-shadow: 0 0 3px rgba(255, 8, 20, 0.718);
        }
    }
    .download-btn:hover {
        color: var(--shadow1);
        animation: changeColor infinite 0.5s alternate;
    }
</style>