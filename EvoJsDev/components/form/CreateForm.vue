<template :values="values" :meta="meta">
    <form @submit="onSubmit">
        <loading :active="isSubmitting || processing" 
            :can-cancel="true" 
            :is-full-page=false></loading>
        <slot name="fields" :fields="getFields" :values="values">
            <component :is="formLayout('top')" >
                <template v-slot>
                    <DisplayFields :fields="getTopFields" :values="values" :initial-values="initialValues"/>
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
            <component :is="formLayout('topAfter')" >
                <template v-slot>
                    <DisplayFields :fields="getTopAfterFields" :values="values" :initial-values="initialValues"/>
                </template>

                <template #left>
                    <DisplayFields :fields="getLeftAfterFields" :values="values" :initial-values="initialValues"/>
                </template>

                <template #center>
                    <DisplayFields :fields="getCenterAfterFields" :values="values" :initial-values="initialValues"/>
                </template>

                <template #right>
                    <DisplayFields :fields="getRightAfterFields" :values="values" :initial-values="initialValues"/>
                </template>
            </component>
            <component :is="formLayout('middle')" >
                <template v-slot>
                    <DisplayFields :fields="getMiddleFields" :values="values" :initial-values="initialValues"/>
                </template>

                <template #left>
                    <DisplayFields :fields="getMiddleLeftFields" :values="values" :initial-values="initialValues"/>
                </template>

                <template #center>
                    <DisplayFields :fields="getMiddleCenterFields" :values="values" :initial-values="initialValues"/>
                </template>

                <template #right>
                    <DisplayFields :fields="getMiddleRightFields" :values="values" :initial-values="initialValues"/>
                </template>
            </component>
            <component :is="formLayout('middleAfter')" >
                <template v-slot>
                    <DisplayFields :fields="getMiddleAfterFields" :values="values" :initial-values="initialValues"/>
                </template>

                <template #left>
                    <DisplayFields :fields="getMiddleAfterLeftFields" :values="values" :initial-values="initialValues"/>
                </template>

                <template #center>
                    <DisplayFields :fields="getMiddleAfterCenterFields" :values="values" :initial-values="initialValues"/>
                </template>

                <template #right>
                    <DisplayFields :fields="getMiddleAfterRightFields" :values="values" :initial-values="initialValues"/>
                </template>
            </component>
            <component :is="formLayout('bottom')" >
                <template v-slot>
                    <DisplayFields :fields="getBottomFields" :values="values" :initial-values="initialValues"/>
                </template>

                <template #left>
                    <DisplayFields :fields="getBottomLeftFields" :values="values" :initial-values="initialValues"/>
                </template>

                <template #center>
                    <DisplayFields :fields="getBottomCenterFields" :values="values" :initial-values="initialValues"/>
                </template>

                <template #right>
                    <DisplayFields :fields="getBottomRightFields" :values="values" :initial-values="initialValues"/>
                </template>
            </component>
            <component :is="formLayout('bottomAfter')" >
                <template v-slot>
                    <DisplayFields :fields="getBottomAfterFields" :values="values" :initial-values="initialValues"/>
                </template>

                <template #left>
                    <DisplayFields :fields="getBottomAfterLeftFields" :values="values" :initial-values="initialValues"/>
                </template>

                <template #center>
                    <DisplayFields :fields="getBottomAfterCenterFields" :values="values" :initial-values="initialValues"/>
                </template>

                <template #right>
                    <DisplayFields :fields="getBottomAfterRightFields" :values="values" :initial-values="initialValues"/>
                </template>
            </component>
        </slot>
        <slot :values="values"></slot>
        <slot name="submitButton" :meta="meta"><Button type="submit" v-bind="buttonAttributes">{{ buttonText }}</Button></slot>
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
            type: [Number, Object],
            default: 1,
            validator(value, props) {
                if(typeof value == 'object') {
                    Object.keys(value).forEach(i => {
                        if (["top", "middle", "bottom", "topAfter", "middleAfter", "bottomAfter"].indexOf(i) == -1) return false
                    })
                    return true
                } else return true
            }
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
        },
        buttonText: {
            type: String,
            default: "Submit"
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
        isSubmitting.value = true
        setTimeout(() => {
            resetForm({values: i})
            isSubmitting.value = false
        }, 3000)
        tempInitialValues.value = i
    })

    // computed properties

    //top fields
    const getRightFields = computed(() => {
        return getFields.value.filter(field => (field.column == 'right' && field.position == 'top'))
    });

    const getLeftFields = computed(() => {
        return getFields.value.filter(field => (field.column == 'left' && field.position == 'top'))
    })

    const getCenterFields = computed(() => {
        return getFields.value.filter(field => (field.column == 'center' && field.position == 'top'))
    })

    const getTopFields = computed(() => {
        return getFields.value.filter(field => (field.position == 'top'))
    })

    //top fields
    const getRightAfterFields = computed(() => {
        return getFields.value.filter(field => (field.column == 'right' && field.position == 'topAfter'))
    });

    const getLeftAfterFields = computed(() => {
        return getFields.value.filter(field => (field.column == 'left' && field.position == 'topAfter'))
    })

    const getCenterAfterFields = computed(() => {
        return getFields.value.filter(field => (field.column == 'center' && field.position == 'topAfter'))
    })

    const getTopAfterFields = computed(() => {
        return getFields.value.filter(field => (field.position == 'topAfter'))
    })
    //top after fields ends

    //middle fields
    const getMiddleRightFields = computed(() => {
        return getFields.value.filter(field => (field.column == 'right' && field.position == 'middle'))
    });

    const getMiddleLeftFields = computed(() => {
        return getFields.value.filter(field => (field.column == 'left' && field.position == 'middle'))
    })

    const getMiddleCenterFields = computed(() => {
        return getFields.value.filter(field => (field.column == 'center' && field.position == 'middle'))
    })

    const getMiddleFields = computed(() => {
        return getFields.value.filter(field => (field.position == 'middle'))
    })
    //middle fields ends

    //middle fields
    const getMiddleAfterRightFields = computed(() => {
        return getFields.value.filter(field => (field.column == 'right' && field.position == 'middleAfter'))
    });

    const getMiddleAfterLeftFields = computed(() => {
        return getFields.value.filter(field => (field.column == 'left' && field.position == 'middleAfter'))
    })

    const getMiddleAfterCenterFields = computed(() => {
        return getFields.value.filter(field => (field.column == 'center' && field.position == 'middleAfter'))
    })

    const getMiddleAfterFields = computed(() => {
        return getFields.value.filter(field => (field.position == 'middleAfter'))
    })
    //middle fields ends

    //bottom fields
    const getBottomRightFields = computed(() => {
        return getFields.value.filter(field => (field.column == 'right' && field.position == 'bottom'))
    });

    const getBottomLeftFields = computed(() => {
        return getFields.value.filter(field => (field.column == 'left' && field.position == 'bottom'))
    })

    const getBottomCenterFields = computed(() => {
        return getFields.value.filter(field => (field.column == 'center' && field.position == 'bottom'))
    })

    const getBottomFields = computed(() => {
        return getFields.value.filter(field => (field.position == 'bottom'))
    })
    //bottom fields ends

    //bottom fields
    const getBottomAfterRightFields = computed(() => {
        return getFields.value.filter(field => (field.column == 'right' && field.position == 'bottomAfter'))
    });

    const getBottomAfterLeftFields = computed(() => {
        return getFields.value.filter(field => (field.column == 'left' && field.position == 'bottomAfter'))
    })

    const getBottomAfterCenterFields = computed(() => {
        return getFields.value.filter(field => (field.column == 'center' && field.position == 'bottomAfter'))
    })

    const getBottomAfterFields = computed(() => {
        return getFields.value.filter(field => (field.position == 'bottomAfter'))
    })
    //bottom fields ends

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
                condition: true,
                position: "top"
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
        }).filter(i => i.condition == true)
    })

    const formLayout = (position = 'top') => {
        const columns = function() {
            if(typeof props.columns == "object") {
                if(props.columns[position] == undefined) {
                    return 1;
                } else return parseInt(props.columns[position])
            } else return props.columns
        }
        switch (columns()) {
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
    }
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