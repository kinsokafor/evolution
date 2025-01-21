<template>
    <div>
        <ListItem v-for="item in filesArr" :key="item" class="file">
            <div class="d-flex lhs">
                <div class="preview-image" :style="`background-image: url(${item})`" @click.prevent="pushModal(imageTemplate, {img: item})"></div>
                <p>{{ getName(item) }}</p>
            </div>
            <template #right>
                <div class="d-flex">
                    <a :href="item" download class="download-btn">
                        <FontAwesomeIcon icon="fas fa-download"/>
                    </a>
                    <a href="javaScript:void(0)" class="download-btn" @click.prevent="removeFile(item)" v-if="enableRemove">
                        <FontAwesomeIcon icon="fa-solid fa-circle-xmark"/>
                    </a>
                </div>
            </template>
        </ListItem>
        
    </div>
</template>

<script setup>
    import {ref, watchEffect} from 'vue'
    import ListItem from '../theme/ListItem.vue'
    import { library } from '@fortawesome/fontawesome-svg-core'
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
    import {fas} from '@fortawesome/free-solid-svg-icons'
    import {pushModal} from "jenesius-vue-modal";
    import imageTemplate from './imageTemplate.vue'
    import {Request} from '@/helpers'

    library.add(fas)

    const props = defineProps({
        files: Array | String,
        enableRemove: {
            type: Boolean,
            default: false
        }
    })

    const emits = defineEmits(["remove"])

    const filesArr = ref([])

    watchEffect(() => {
        if(typeof(props.files) == "string") {
            filesArr.value = [props.files]
        }
        filesArr.value = props.files
    })

    const getName = function(file) {
        return file.split("/").slice(-1)[0]
    }

    const getExt = function(file) {
        return getName(file).split(".").slice(-1)[0]
    }

    const removeFile = function(file) {
        if(confirm("Are you sure you want to delete this file?")) {
            const req = new Request()
            req.post(req.root+"/filepond/revert", {file: file}).then(r => {
                const index = filesArr.value.findIndex(i => i === file)
                filesArr.value.splice(index, 1);
                emits("remove", file);
            }).catch(e => {

            })
        }
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
    .preview-image {
        height: 40px;
        width: 40px;
        background-size: cover;
        border-radius: 3px;
        cursor: pointer;
    }
    .d-flex.lhs p {
        margin-bottom: 0;
    }
    .d-flex.lhs {
        align-items: center;
        justify-content: center;
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