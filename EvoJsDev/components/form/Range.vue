<template>
    <div class="k-input-group" :class="layout">
        <label :for="name" class="label">{{ label }}: {{ value }}</label>
        <Field as="input" type="range" :id="name" :name="name" v-bind="{...attrs, class: 'k-range'}" />
        <small><slot name="hint"></slot></small>
    </div>
</template>

<script setup>
    import { Field } from 'vee-validate';
    import { useField } from 'vee-validate'
    import "/color-scheme.css";

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
            default: "input"
        },
        attrs: {
            type: Object,
            default: {}
        },
        column: {
            type: String
        }
    })

    const { value, errorMessage } = useField(props.name, props.required ? isRequired : true)

</script>

<style lang="scss" scoped>
    .k-range {
        -webkit-appearance: none;
        appearance: none;
        width: 100%;
        height: 3px;
        background: var(--highlight2);
        outline: none;
        opacity: 0.7;
        -webkit-transition: 0.2s;
        transition: opacity 0.2s;
    }
    .k-range:hover {
        opacity: 1; /* Fully shown on mouse-over */
    }
    .k-range::-webkit-slider-thumb {
        -webkit-appearance: none; /* Override default look */
        appearance: none;
        width: 5px; /* Set a specific slider handle width */
        height: 25px; /* Slider handle height */
        background: var(--color2); /* Green background */
        cursor: pointer; /* Cursor on hover */
        border: none;
        border-radius: 0;
    }
    .k-range::-moz-range-thumb {
        width: 5px; /* Set a specific slider handle width */
        height: 25px; /* Slider handle height */
        background: var(--color2); /* Green background */
        cursor: pointer; /* Cursor on hover */
        border: none;
        border-radius: 0;
    }
</style>