<template>
    <div>
        <button ref="draggable" class="floating-modal-trigger" @click="pushModalx">
            <div class="floating-modal-handle" ref="handle">
                <FontAwesomeIcon icon="fa-solid fa-grip-lines-vertical" />
            </div>
            <FontAwesomeIcon icon="fa-solid fa-plus" />
        </button>
        <container />
    </div>
</template>

<script setup>
    import { ref, onMounted, shallowRef } from "vue";
    import {container} from "jenesius-vue-modal";
    import {pushModal} from "jenesius-vue-modal";
    import {dragElement} from "@/helpers";
    import FloatingModalComponent from "./FloatingModalComponent.vue";
    /* import the fontawesome core */
    import { library } from '@fortawesome/fontawesome-svg-core'

    /* import font awesome icon component */
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
    /* import specific icons */
    import { faPlus, faGripLinesVertical } from '@fortawesome/free-solid-svg-icons'
    
    library.add(faPlus, faGripLinesVertical)

    const draggable = ref(null)
    const handle = ref(null)
    var properties = {};
    onMounted(() => {
        dragElement(draggable.value, handle.value)
        properties = {
            "modalComponent": shallowRef(props.modalComponent),
            "props": props.props
        }
    })

    const props = defineProps({
        modalComponent: Object,
        props: {
            type: Object,
            default: {}
        }
    })

    const pushModalx = () => {
        pushModal(FloatingModalComponent, properties)
    }
</script>

<style lang="scss" scoped>
    
    @keyframes blink {
        0%,
        50%,
        100%,
        28%,
        78% {
            opacity: 1;
        }

        25%,
        75%,
        30%,
        80% {
            opacity: 0.5;
        }
    }

    .floating-modal-handle {
        top: -21px;
        position: absolute;
        transform: rotate(54deg);
        left: -1px;
        cursor: move;
        font-size: 18px;
    }
    .floating-modal-trigger {
        width: 55px;
        height: 55px;
        // background: rgb(21, 98, 170);
        border-radius: 50%;
        transition: all 0.4s ease-in-out;
        border: none;
        position:fixed;
        right: 10%;
        bottom: 45%;
        opacity: 0.5;
        animation: blink 12s ease 0s infinite normal forwards;
        // color: white;
        z-index: 1000;
    }
    .floating-modal-trigger:hover {
        width: 58px;
        height: 58px;
        // background: rgb(21, 98, 170);
        border-radius: 50%;
        opacity: 1;
        animation: none;
    }
</style>