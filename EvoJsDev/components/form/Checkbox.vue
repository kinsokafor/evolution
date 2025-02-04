<template> 
    <Field v-slot="{ field }" :name="name" type="checkbox" v-bind="attrs" :value="true">
        <label>
            <input type="checkbox" v-bind="{...field, ...attrs}" @input="(val) => update(val.target.checked)" />
            {{ label }}
        </label>
    </Field>
</template>

<script setup>
import { Field, useField } from 'vee-validate';
import { onMounted, watch } from 'vue';

const emit = defineEmits(["update:modelValue"]);

const props = defineProps({
    layout: { type: String, default: "" },
    label: { type: String, default: "" },
    name: { type: String },
    attrs: { type: Object, default: () => ({}) },
    column: { type: String },
    initialValues: { type: Object, default: () => ({}) },
    modelValue: Boolean,
});

const { value, setValue } = useField(props.name);

onMounted(() => {
    setValue(props.initialValues[props.name] ?? props.attrs.value ?? false);
});

watch(() => props.initialValues[props.name], (newVal) => {
    if (newVal !== undefined) {
        setValue(newVal);
    }
});

const update = (value) => {
    setValue(value);
    emit("update:modelValue", value);
};
</script>

<style lang="scss" scoped>
@supports (-webkit-appearance: none) or (-moz-appearance: none) {
    input[type=checkbox],
    input[type=radio] {
        --active: var(--color1);
        --active-inner: var(--highlight1);
        --focus: 2px var(--color2);
        --border: var(--highlight3);
        --border-hover: var(--color1);
        --background: var(--highlight1);
        --disabled: var(--highlight2);
        --disabled-inner: var(--highlight3);
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        height: 21px;
        outline: none;
        display: inline-block;
        vertical-align: top;
        position: relative;
        margin: 0;
        cursor: pointer;
        border: 1px solid var(--bc, var(--border));
        background: var(--b, var(--background));
        transition: background 0.3s, border-color 0.3s, box-shadow 0.2s;
        justify-content: center;
        align-items: center;
    }

    input[type=checkbox]:checked,
    input[type=radio]:checked {
        --b: var(--active);
        --bc: var(--active);
    }

    input[type=checkbox]:disabled,
    input[type=radio]:disabled {
        --b: var(--disabled);
        cursor: not-allowed;
        opacity: 0.9;
    }

    input[type=checkbox].switch {
        width: 38px;
        border-radius: 11px;
    }

    input[type=checkbox].switch:checked {
        --ab: var(--active-inner);
        --x: 17px;
    }
}
</style>
