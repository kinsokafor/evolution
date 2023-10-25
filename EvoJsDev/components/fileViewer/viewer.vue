<template>
    <div>
        <ListItem v-for="item in filesArr" :key="item">
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
    .download-btn {
        color: white;
        background: #798079;
        display: flex;
        border-radius: 50px;
        width: 32px;
        height: 32px;
        justify-content: center;
        align-items: center;
        transition: 0.5s linear;
    }
    @keyframes changeColor {
        0% {
            box-shadow: 0 0 5px #ffb24f8a;
        }

        30% {
            box-shadow: 0 0 5px #55ff4f8a;
        }

        60% {
            box-shadow: 0 0 5px #694fff8a;
        }
        80% {
            box-shadow: 0 0 5px #f81ce68a;
        }
        100% {
            box-shadow: 0 0 5px #ff0714b7;
        }
    }
    .download-btn:hover {
        color: #b51313;
        scale: 1.05;
        animation: changeColor-36d79186 infinite 2s alternate;
        background-image: linear-gradient(10deg, #f4f4f4, #f7eecd, #fdd1d1);
    }
</style>