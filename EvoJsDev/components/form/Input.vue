<template>
    <div class="k-input-group" :class="layout">
        <label :for="name" class="label">{{ label }}</label>
        <Field :as="as" :id="name" :name="name" :class="appData().inputFieldClass ?? ''" v-bind="attrs" />
        <small><slot name="hint">{{ attrs.hint ?? "" }}</slot></small>
    </div>
</template>

<script setup>
    import { Field } from 'vee-validate'
    import { appData } from '@/helpers'

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
        },
        values: Object,
        initialValues: Object
    })

</script>

<style lang="scss" scoped>
    input[type="color"] {
        padding: 0.15rem;
        border-radius: 0;
    }
</style>