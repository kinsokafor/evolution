<template :values="values" :meta="meta">
    <form>
        <loading :active="isSubmitting || processing" 
            :can-cancel="true" 
            :is-full-page=false></loading>
        <component
            :is="formLayout"
        >
            <template v-slot>
                <DisplayFields :fields="getFields" :values="values" :initial-values="initialValues"/>
            </template>

            <template #left>
                <DisplayFields :fields="getLeftFields" :values="values" :initial-values="initialValues"/>
            </template>

            <template #center>
                <DisplayFields :fields="getCenterFields" :values="values" :initial-values="initialValues"/>
            </template>

            <template #right>
                <DisplayFields :fields="getRightFields" :values="values" :initial-values="initialValues"/>
            </template>
        </component>
        <slot :values="values"></slot>
        <slot name="submitButton" :meta="meta"><Button type="submit" v-bind="buttonAttributes" :disabled="!meta.valid">Submit</Button></slot>
    </form>
</template>

<script setup>
    import { Form, useForm } from 'vee-validate';
    import Button from '@/components/Button.vue';
    // import { useCreateFormStore } from '@/store/createForm';
    import DisplayFields from './DisplayFields.vue';
    import Loading from 'vue3-loading-overlay';
    import 'vue3-loading-overlay/dist/vue3-loading-overlay.css';
    import "/color-scheme.css";
    import { ref, computed, watchEffect } from 'vue'
    import SingleColumn from '@/components/layouts/SingleColumn.vue'
    import DoubleColumn from '@/components/layouts/DoubleColumn.vue'
    import TripleColumn from '@/components/layouts/TripleColumn.vue'
    
    // const store = useCreateFormStore();

    const props = defineProps({
        fields: {
            type: Object,
            default: ref({})
        },
        initialValues: {
            type: Object,
            default: {}
        },
        columns: {
            type: Number,
            default: 1
        },
        buttonAttributes: {
            type: Object,
            default: {
                class: "btn btn-primary"
            }
        },
        processing: {
            type: Boolean,
            default: false
        }
    })

    const {values, handleSubmit, isSubmitting, meta, resetForm} = useForm({
        initialValues: props.initialValues
    })

    const emit = defineEmits(['submit', 'values'])

    handleSubmit((values, actions) => {
        emit("submit", values, actions);
    })

    watchEffect(() => {
        emit("values", values)
    })

    watchEffect(() => {
        resetForm({values: props.initialValues})
    })

    // computed properties

    const getRightFields = computed(() => {
        return getFields.value.filter(field => field.column == 'right')
    });

    const getLeftFields = computed(() => {
        return getFields.value.filter(field => field.column == 'left')
    })

    const getCenterFields = computed(() => {
        return getFields.value.filter(field => field.column == 'center')
    })

    const getFields = computed(() => {
        return props.fields.map(field => {
            field['as'] = field.as == undefined ? "input" : field.as;
            field['label'] = field.label == undefined ? "" : field.label;
            field['placeholder'] = field.placeholder == undefined ? field.label : field.placeholder;
            field['error'] = field.error == undefined ? "" : field.error;
            field['class'] = field.class == undefined ? "form-control" : field.class;
            field['layout'] = field.layout !== "linear" ? "" : field.layout;
            field['layout'] = field.linear == true ? "linear" : field.layout;
            field['column'] = field.column == undefined ? "left" : field.column;
            if(field['as'] == 'input') {
                field['type'] = field.type == undefined ? "text" : field.type;
            }
            switch (field.column) {
                case "left":
                case "right":
                    
                    break;

                case "center":
                    field['column'] = props.columns < 3 ? "left" : "center";
                    break;
            
                default:
                    field['column'] = "left"
                    break;
            }
            return field;
        })
    })

    const formLayout = computed(() => {
            switch (props.columns) {
                case 2:
                        return DoubleColumn
                    break;

                case 3:
                        return TripleColumn
                    break;
            
                default:
                        return SingleColumn
                    break;
            }
        })
</script>

<style lang="scss">
    span[role="alert"] {
        color: var(--red);
        font-size: 12px;
        line-height: 20px;
    }
    .k-input-group {
        margin-top: 10px;
        margin-bottom: 10px;
    }
    .k-input-group label {
        color: var(--highlight3);
    }

    .k-input-group.linear {
        display: flex;
    }
    .k-input-group.linear .label {
        background: var(--highlight1);
        padding: 10px;
        border-radius: .375rem 0 0 .375rem;
        line-height: 1;
        font-size: 15px;
        width: 35%;
    }
    .k-input-group.linear .form-control {
        width: 65%;
        border-radius: 0 .375rem .375rem 0;
    }
</style>

<style lang="scss">
    .k-input-group label {
        font-size: 11px;
        margin-bottom: 0.1rem;
    }
</style>