<template>
    <div class="k-input-group" :class="layout">
        <label :for="name" class="label">{{ label }}</label>
        <div v-for="(r, i) in rows" :key = "r" v-memo="[i, memo]">
            <div class="collection-row">
                <div 
                    class="collection-column"
                    :class="c.col ? 'col'+c.col : 'col3'" 
                    v-for="{as, layout, label, ...c} in fields"
                    :key = "c">
                    <component
                        :is="store.getComponent(as)"
                        :name="name+'.'+i+'.'+c.name"
                        :label="label ?? ''"
                        :layout="layout ?? ''"
                        :attrs="getAttributes(attrs, c)"
                        :as="c.as"
                        :initialValues="initialValues"
                    ></component>
                    
                </div>
                <ErrorMessage :name="name+'.'+i"></ErrorMessage>
            </div>
        </div>
        <div class="collection-btns" v-if="adjustableRows">
            <button @click.prevent="control++">Add row</button>
            <button @click.prevent="removeRow()">Remove last row</button>
        </div>
        <small><slot name="hint"></slot></small>
    </div>
</template>

<script setup>
    import { useCreateFormStore } from '@/store/createForm';
    import { ErrorMessage } from 'vee-validate';
    import { computed, ref, watch } from 'vue'
    import '/color-scheme.css'
    import {randomId} from '@/helpers'
    import _ from 'lodash';

    const store = useCreateFormStore()
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
            default: "collection"
        },
        attrs: {
            type: Object,
            default: {}
        },
        column: {
            type: String
        },
        initialValues: Object
    })
    const oldFields = ref([])
    const memo = ref(randomId(7))
    const fields = computed(() => {
        if(props.attrs.fields == undefined) return []
        return props.attrs.fields.filter(i => i.condition == undefined ? true : i.condition);
    })
    watch(fields, () => {
        if(!_.isEqual(fields.value, oldFields.value)) {
            oldFields.value = fields.value
            memo.value = randomId(7)
            console.log("Yes")
        }
    })
    const adjustableRows = computed(() => props.attrs.adjustableRows == undefined ? true : props.attrs.adjustableRows)
    const initiated = ref(false)
    const control = ref(0)
    const initRows = ref(0)
    const rows = computed(() => {
        if(props.initialValues[props.name] != undefined && Object.values(props.initialValues[props.name]).length > 0 && !initiated.value) {
            initiated.value = true
            initRows.value = [...Object.values(props.initialValues[props.name])].length
        } else if(!initiated.value) {
            initRows.value = props.attrs.rows
        }
        return initRows.value + control.value
    })
    
    const removeRow = () => {
        if(rows.value > 1) {
            control.value--
        }
    }

    const getAttributes = (attrs, c) => {
        const attr = {...attrs}
        const x = {...c}
        delete x.name
        delete attr.rows
        delete attr.placeholder
        delete attr.fields
        return {...attr, ...x};
    }
</script>

<style lang="scss" scoped>
    // .collection-btns {
    //     position: absolute;
    //     bottom: 0;
    //     left: 0;
    // }
    .k-input-group > label ~ div {
        padding: 10px;
    }
    .k-input-group > label ~ div:nth-child(2n+1) {
        background: #f7f7f798;
        border-radius: 12px;
    }
    .collection-btns button {
        border: none;
        background: none;
        font-size: 12px;
        color: var(--blue);
        margin-right: 4px;
    }
    .collection-row {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        grid-template-rows: 1fr;
        grid-column-gap: 4px;
        grid-row-gap: 0px;
    }
    .col1 {
        grid-column: span 1;
    }
    .col2 {
        grid-column: span 2;
    }
    .col3 {
        grid-column: span 3;
    }
    .col4 {
        grid-column: span 4;
    }
    .col5 {
        grid-column: span 5;
    }
    .col6 {
        grid-column: span 6;
    }
    .col7 {
        grid-column: span 7;
    }
    .col8 {
        grid-column: span 8;
    }
    .col9 {
        grid-column: span 9;
    }
    .col10 {
        grid-column: span 10;
    }
    .col11 {
        grid-column: span 11;
    }
    .col12 {
        grid-column: span 12;
    }

    @media screen and (max-width: 840px) {
        .collection-row {
            display: grid;
            grid-column-gap: 2px;
        }
        .col1, .col2, .col3 {
            grid-column: span 3;
        }
        .col4, .col5, .col6 {
            grid-column: span 6;
        }
        .col7, .col8, .col9, .col10, .col11, .col12 {
            grid-column: span 12;
        }
    }

    @media screen and (max-width: 590px) {
        .collection-row {
            display: grid;
            grid-column-gap: 2px;
        }
        .col1, .col2, .col3, .col4, .col5 {
            grid-column: span 6;
        }
        .col6, .col7, .col8, .col9, .col10, .col11, .col12 {
            grid-column: span 12;
        }
    }

    @media screen and (max-width: 380px) {
        .collection-row {
            display: grid;
            grid-column-gap: 0px;
        }
        .col1, .col2, .col3, .col4, .col5, .col6, .col7, .col8, .col9, .col10, .col11, .col12 {
            grid-column: span 12;
        }
    }

</style>

<style lang="scss">
.k-input-group > label ~ div:nth-child(2n+1) {
    .collection-row .form-control {
        background-color: transparent;
    }
}
</style>