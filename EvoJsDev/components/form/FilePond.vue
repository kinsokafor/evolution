<template>
    <input type="file" :name="`input-${name}`" ref="myFile"/>
</template>

<script setup>
    import * as FilePond from 'filepond';
    import 'filepond/dist/filepond.min.css';
    import { onMounted, ref } from 'vue';
    import {nonce} from '@/helpers';
    import { useField } from 'vee-validate'
    import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';

    const myFile = ref(null)
    
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
        values: Object
    })

    const { value, errorMessage, setErrors, setValue } = useField(props.name, props.attrs.rules ?? '')

    onMounted(() => {
        // Create a FilePond instance
        const {acceptedFileTypes, ...attrs} = props.attrs;
        FilePond.registerPlugin(FilePondPluginFileValidateType);
        FilePond.setOptions({
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
        const pond = FilePond.create(myFile.value);

    })
</script>

<style lang="scss" scoped>

</style>