<template>
    <div>
        <ListItem v-for="item in filesArr" :key="item" class="file">
            {{ getName(item) }}
            <template #right>
                <a :href="item" download class="download-btn">
                    <FontAwesomeIcon icon="fas fa-download"/>
                </a>
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
        color: white;
        background: var(--color1);
        display: flex;
        border-radius: 50px;
        width: 27px;
        height: 27px;
        justify-content: center;
        align-items: center;
        transition: 0.5s linear;
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
        color: var(--highlight2);
        scale: 1.05;
        animation: changeColor-36d79186 infinite 2s alternate;
    }
</style>