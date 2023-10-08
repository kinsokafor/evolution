<template>
    <div class="k-input-group" :class="layout">
    <Field v-slot="{ field }" :name="name" v-bind="attrs" :value="true">
        <label :for="name" class="label">{{ label }}</label>
        <froala :tag="'textarea'" :config="config" v-bind="{...field, ...attrs}" v-model:value="model"></froala>
    </Field>
    </div>
</template>

<script setup>
    //Import Froala Editor 
    import 'froala-editor/js/plugins.pkgd.min.js';
    //Import third party plugins
    import 'froala-editor/js/third_party/embedly.min';
    import 'froala-editor/js/third_party/font_awesome.min';
    import 'froala-editor/js/third_party/spell_checker.min';
    import 'froala-editor/js/third_party/image_tui.min';
    // Import Froala Editor css files.
    import 'froala-editor/css/froala_editor.pkgd.min.css';
    import 'froala-editor/css/froala_style.min.css';
    import {ref, watchEffect} from 'vue'
    import { useField, Field } from 'vee-validate'
    import * as yup from 'yup'

    const props = defineProps({
        name: {
            type: String,
            default: "myUpload"
        },
        attrs: {
            type: Object,
            default: {}
        },
        layout: {
            type: String,
            default: ""
        },
        label: {
            type: String,
            default: ""
        },
        initialValues: Object
    })

    const { setErrors, setValue } = useField(props.name)

    const validate = (value) => {
        const schema = props.attrs.rules ?? yup.mixed();
        schema
        .validate(value)
        .then((validValue) => {
            // console.log('Validation passed:', validValue);
        })
        .catch((validationError) => {
            setErrors(validationError.errors)
        });
    } 

    const initiated = ref(false)

    const model = ref(null)
    const config = {
        events: {
          initialized: function () {
            
          },
          blur: function() {
            validate(model.value)
          }
        }
    }

    watchEffect(() => {
        if(initiated.value) return
        if(props.initialValues[props.name] != undefined) {
            model.value = props.initialValues[props.name]
            initiated.value = true
        }
    })

    watchEffect(() => {
        setValue(model.value)
        if(model.value != null) {
            validate(model.value)
        }
    })
</script>

<style lang="scss">
    .fr-wrapper > div:not(.fr-element) {
        display: none !important;
    }
</style>