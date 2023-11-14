<template>
    <div
        v-for="{ as, name, label, error, layout, ...attrs } in fields" 
        :key="name"
    >
        <component 
            :is="getComponent(as)"
            :name="name"
            :label="label"
            :layout="layout"
            :attrs="attrs"
            :as="as"
            :initial-values="initialValues"
        ></component>
        <ErrorMessage :name="name" v-if="error == '' && as !== 'label'"></ErrorMessage>
        <ErrorMessage :name="name" v-if="error !== '' && as !== 'label'">{{error}}</ErrorMessage>
    </div>
</template>

<script setup>
    import { ErrorMessage } from 'vee-validate';
    import Input from './Input.vue';
    import Textarea from './Textarea.vue';
    import Select from './Select.vue';
    import Radio from './Radio.vue';
    import Checkbox from './Checkbox.vue';
    import Croppie from './Croppie.vue';
    import Password from './Password.vue';
    import Label from './Label.vue';
    import Range from './Range.vue';
    import Currency from './Currency.vue';
    import Collection from './Collection.vue';
    import WYSIWYG from '@/components/form/WYSIWYG.vue';
    import FilePond from './FilePond.vue';

    // const store = useCreateFormStore();
    const props = defineProps(['fields', 'values', 'initialValues'])

    const getComponent = (as) => {
            switch (as) {
                case "select":
                        return Select;
                    break;

                case "radio":
                        return Radio;
                    break;

                case "checkbox":
                        return Checkbox;
                    break;

                case "croppie":
                        return Croppie;
                    break;

                case "password":
                        return Password;
                    break;

                case "range":
                        return Range;
                    break;

                case "label":
                        return Label;
                    break;

                case "currency":
                        return Currency;
                    break;

                case "collection":
                        return Collection;
                    break;

                case "textarea":
                        return Textarea;
                    break;
                
                case "wysiwyg":
                        return WYSIWYG;
                    break;

                case "filepond":
                        return FilePond;
                    break;
            
                default:
                        return Input;
                    break;
            }
        }
</script>

<style lang="scss" scoped>

</style>