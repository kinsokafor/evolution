<template>
    <div class="mb-4">
        <input type="file" :name="`input-${name}`" ref="myFile" :id="id"/>
        <small>{{ attrs.hint ?? "" }}</small>
        <viewer :files="defaultFiles" :enable-remove="true" @remove="fileRemoved"></viewer>
        <container></container>
    </div>
</template>

<script setup>
    import * as FilePond from 'filepond';
    import 'filepond/dist/filepond.min.css';
    import { onMounted, ref, inject, watchEffect, computed } from 'vue';
    import {nonce,randomId} from '@/helpers';
    import { useField } from 'vee-validate'
    import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';
    import viewer from '@/components/fileViewer/viewer.vue'
    import {container} from "jenesius-vue-modal";

    const id = randomId(8)
    const myFile = ref(null)
    
    const meta = inject("meta", {})
    const props = defineProps({
        label: {
            type: String,
            default: "your files"
        },
        name: {
            type: String
        },
        attrs: {
            type: Object,
            default: {} //used to configure filepond
        },
        column: {
            type: String
        },
        layout: {
            type: String,
            default: ""
        },
        as : {
            type: String,
            default: "input"
        },
        initialValues: Object
    })

    const defaultFiles = computed(() => {
        if(props.initialValues[props.name] != undefined) {
            return props.initialValues[props.name]
        } else return []
    })

    const { value, setErrors, setValue } = useField(props.name, props.attrs.rules ?? '')
    const pond = ref(null)
    onMounted(() => {
        // Create a FilePond instance
        FilePond.registerPlugin(FilePondPluginFileValidateType);
        const {acceptedFileTypes, ...attrs} = props.attrs;
        pond.value = FilePond.create(myFile.value);
        pond.value.setOptions({
            server: {
                url: process.env.EVO_API_URL,
                process: {
                    url: '/filepond/process',
                    method: 'POST',
                    withCredentials: false,
                    headers: {'Authorization': `Bearer ${nonce()}`},
                    timeout: null,
                    onload: r => {
                        if(props.attrs.allowMultiple == true) {
                            let temp = value.value == "" || value.value == undefined ? [] : value.value;
                            const index = temp.indexOf(JSON.parse(r))
                            temp = index == -1 ? [...temp, JSON.parse(r)] : temp;
                            setValue(temp)
                        } else setValue(JSON.parse(r))
                        return r;
                    },
                    onerror: e => {
                        setErrors(e)
                        return e
                    },
                    ondata: null,
                },
                revert: {
                    url: '/filepond/revert',
                    method: 'POST',
                    withCredentials: false,
                    headers: {'Authorization': `Bearer ${nonce()}`},
                    timeout: 7000,
                    onload: r => {
                        if(props.attrs.allowMultiple == true) {
                            var temp = [...value.value]
                            temp.splice(temp.indexOf(JSON.parse(r)), 1)
                            if(temp.length > 0) {
                                setValue(temp)
                            } else setValue("")
                        } else setValue("")
                    },
                    onerror: e => {
                        // setErrors(e)
                    },
                    ondata: null,
                }
            },
            labelIdle: `Drag & drop ${props.label} or <u>Browse</u>`,
            acceptedFileTypes: acceptedFileTypes ?? "application/pdf",
            ...attrs
        })
        

    })

    watchEffect(() => {
        if(meta.value.dirty == false) {
            if(pond.value != null)
                pond.value.removeFiles()
        }
    })

    const fileRemoved = function(file) {
        const newValue = value.value
        const index = newValue.findIndex(i => i === file)
        newValue.splice(index, 1)
        setValue(newValue);
    }
</script>

<style lang="scss">
    .filepond--credits {
        display: none;
    }
</style>