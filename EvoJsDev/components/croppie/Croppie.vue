<template>
    <input type="hidden" 
        :id="'imagebase64-' + instance"
        :name="name"
        v-model="value">
    <div class="croppie-container">
        <label class="cabinet" :id="'cabinet-' + instance" v-if="preview">
            <figure>
                <img 
                    :src="previewImage" 
                    class="gambar img-responsive img-thumbnail croppie-image" 
                    :id="'croppie-output-' + instance"/>
                <figcaption><i class="fas fa-camera"></i></figcaption>
            </figure>
            <input type="file" 
                class="croppie-file file center-block" 
                :id="'item-img-' + instance" 
                :name="'input-'+name" 
                @change="readFile"
                accept="image/*"/>
        </label>
        <div class="croppie-pannel" :id="'cropImagePop' + instance" v-else>
            <div class="croppie-pannel-dialog" :style="`width: ${boundary.width + 40}px`">
                <div class="croppie-pannel-content">
                    <div class="croppie-pannel-body" :style="`min-width: ${boundary.width + 40}px`">
                        <div :id="'croppie-preview-' + instance" ref="croppieElement" class="croppie-preview"></div>
                        <div class="croppie-controls">
                            <div class="btn-group">
                                <button type="button" @click.prevent="preview = true" class="croppie-cancel-btn btn btn-primary btn-sm">close</button>
                                <button type="button" @click.prevent="rotateLeft" class="croppie-rotate-btn btn btn-primary btn-sm">left</button>
                                <button type="button" @click.prevent="rotateRight" class="croppie-rotate-btn btn btn-primary btn-sm">right</button>
                                <button type="button" @click.prevent="crop" class="croppie-crop-btn btn btn-primary btn-sm">crop</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="croppie-wrapper image-upload">
        <div id="croppie"></div>
    </div>
</template>

<script setup>
import { ref, onMounted, watchEffect, inject, nextTick, computed } from 'vue'
import { randomId } from '@/helpers'
import Croppie from 'croppie'
import 'croppie/croppie.css'
import './style.css'
import { useField } from 'vee-validate'
import img from './images/croppie-default.gif'

const image = ref(null);
const instance = ref(randomId(8));
const preview = ref(true);
const defaultImage = ref(null);
const meta = inject("meta", {})
const croppieElement = ref(null)

const props = defineProps({
    attrs: {
        type: Object,
        default: () => ({})
    },
    default: {
        type: String,
        default: ""
    },
    viewportType: {
        type: String,
        default: "square"
    },
    viewport: {
        type: Object,
        default: () => ({
            width: 200,
            height: 200
        })
    },
    boundary: {
        type: Object,
        default: () => ({
            width: 220,
            height: 220
        })
    },
    name: {
        type: String,
        default: "myUpload"
    },
    showZoomer: {
        type: Boolean,
        default: false
    },
    class: {
        type: String,
        default: ""
    },
    enableExif: {
        type: Boolean,
        default: false
    },
    enableResize: {
        type: Boolean,
        default: false
    },
    mouseWheelZoom: {
        type: Boolean,
        default: true
    },
    required: {
        type: Boolean,
        default: false
    },
    column: {
        type: String
    },
    condition: {
        type: Boolean,
        default: true
    }
})

let c = null

onMounted(() => {
    defaultImage.value = props.default !== "" ? props.default : process.env.EVO_API_URL + img;
})

const { value } = useField(props.name, props.attrs.rules ?? '')

const setUpCroppie = () => {
    const id = "croppie-preview-" + instance.value
    c = new Croppie(croppieElement.value, {
        viewport: props.viewport,
        boundary: props.boundary,
        showZoomer: props.showZoomer,
        enableOrientation: true,
        customClass: props.class,
        enableExif: props.enableExif,
        enableResize: props.enableResize,
        mouseWheelZoom: props.mouseWheelZoom
    });
    c.bind({
        url: image.value
    })
    croppieElement.value.addEventListener('update', handleUpdate);
}

const lastTimeOut = ref(null)

const handleUpdate = (event) => {
    if(lastTimeOut.value != null) {
        clearTimeout(lastTimeOut.value)
    }
    lastTimeOut.value = setTimeout(() => {
        if(preview.value == true) return;
        crop()
    }, 5000)
}

const readFile = async (e) => {
    preview.value = false;
    const files = e.target.files || e.dataTransfer.files;
    if (!files.length) return;
    const file = files[0];
    image.value = URL.createObjectURL(file);
    await nextTick();
    setUpCroppie();
}

const rotateLeft = () => {
    c.rotate(90)
}

const rotateRight = () => {
    c.rotate(-90)
}

const crop = () => {
    c.result().then(result => {
        preview.value = true
        value.value = result
        URL.revokeObjectURL(image.value);
    })
}

watchEffect(() => {
    if (meta.value.dirty === false) {
        if (c != null) {
            value.value = undefined
        }
    }
})

const previewImage = computed(() => {
    if(value.value != undefined) {
        if(
            value.value.charAt(0) === "/" ||
            String(value.value.trim()).search("http") === 0 ||
            String(value.value.trim()).search("data:") === 0
        ) {
            return value.value
        } else {
            return `/${value.value}`
        }
    }
    return defaultImage.value
})

</script>

<style lang="scss" scoped>
img {
    margin: 0 !important;
}
</style>
