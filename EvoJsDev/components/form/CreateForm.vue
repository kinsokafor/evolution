<template :values="values" :meta="meta">
    <form @submit="onSubmit">
        <loading :active="isSubmitting || processing" 
            :can-cancel="true" 
            :is-full-page=false></loading>
        <slot name="fields" :fields="getFields" :values="values">
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
        </slot>
        <slot :values="values"></slot>
        <slot name="submitButton" :meta="meta"><Button type="submit" v-bind="buttonAttributes" :disabled="!meta.valid">Submit</Button></slot>
    </form>
</template>

<script setup>
    import { Form, useForm } from 'vee-validate';
    import Button from '@/components/Button.vue';
    import _ from 'lodash'
    import DisplayFields from './DisplayFields.vue';
    import Loading from 'vue3-loading-overlay';
    import 'vue3-loading-overlay/dist/vue3-loading-overlay.css';
    import "/color-scheme.css";
    import { ref, computed, watchEffect, provide } from 'vue'
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

    const {values, handleSubmit, isSubmitting, meta, resetForm} = useForm()

    const emit = defineEmits(['submit', 'values'])
    
    provide("meta", meta);

    const onSubmit = handleSubmit((values, actions) => {
        emit("submit", values, actions);
    })

    const tempInitialValues = ref({})

    watchEffect(() => {
        emit("values", values)
    })

    watchEffect(() => {
        const i = {...props.initialValues}
        const j = {...tempInitialValues.value}
        if(_.isEqual(i, j)) {
            return
        }
        resetForm({values: i})
        tempInitialValues.value = i
    })

    // computed properties

    const getRightFields = computed(() => {
        return getFields.value.filter(field => (field.column == 'right' && field.condition == true))
    });

    const getLeftFields = computed(() => {
        return getFields.value.filter(field => (field.column == 'left' && field.condition == true))
    })

    const getCenterFields = computed(() => {
        return getFields.value.filter(field => (field.column == 'center' && field.condition == true))
    })

    const getFields = computed(() => {
        
        return props.fields.map(field => {
            const defaultField = {
                as: "input",
                label: "",
                placeholder: field.label ?? "",
                error: "",
                class: "form-control",
                layout: field.linear == true ? "linear" : field.layout ?? "",
                column: "left",
                condition: true
            }
            field = {...defaultField, ...field}
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
        color: var(--shadow2);
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
        font-size: 0.8rem;
        margin-bottom: 0.3rem;
    }
</style>