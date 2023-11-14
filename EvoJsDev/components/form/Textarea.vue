<template>
    <div class="k-input-group" :class="layout">
        <label :for="name" class="label">{{ label }}</label>
        <Field :as="as" :id="name" :name="name" v-bind="attrs" />
        <small><slot name="hint"></slot></small>
    </div>
</template>

<script setup>
    import { Field } from 'vee-validate';

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
            default: "textarea"
        },
        attrs: {
            type: Object,
            default: {}
        },
        column: {
            type: String
        }
    })

</script>

<style lang="scss" scoped>
    input[type="color"] {
        padding: 0.15rem;
        border-radius: 0;
    }
</style>