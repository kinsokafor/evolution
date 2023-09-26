<template>
    <div class="k-input-group" :class="layout">
        <label :for="name" class="label">{{ label }}</label>
        <Field :as="as" :id="name" :name="name" v-bind="getAttributes" v-slot="{value}">
            <option disabled>Select {{ label }}</option>
            <option 
            v-for="(option, index) in getSelectOptions" 
            :key="index" 
            :value="option.value" 
            :selected="selected(value, option.value)">{{ option.name }}</option>
        </Field>
    </div>
</template>

<script setup>
    import { Field } from 'vee-validate';
    import { computed } from 'vue';

    const selected = (value, optionValue) => {
        switch(typeof(optionValue)) {
            case "number":
                return value && value == optionValue
                break;
            
            case "string":
                return value && value == optionValue
                break;

            default:
                return false
                break;
        }
    }

    const props = defineProps({
        layout: {
            type: String,
            default: ""
        },
        label: {
            type: String,
            default: ""
        },
        name: {
            type: String
        },
        as : {
            type: String,
            default: "select"
        },
        attrs: {
            type: Object,
            default: {}
        },
        column: {
            type: String
        },
        options: {
            type: Array,
            default: []
        },
        values: Object
    })

    const getAttributes = computed(() => {
        return Object.fromEntries(
            Object.entries(props.attrs).filter(([key, val]) => key != "options")
        );
    })

    const getSelectOptions = computed(() => {
        const options = (Array.isArray(props.attrs.options)) ? props.attrs.options : props.options;
        return options.map(option => {
            return typeof(option) == "string" ? {"name": option, "value": option} : option;
        });
    })
</script>

<style lang="scss" scoped>
    
</style>