import {defineStore} from 'pinia';
import Input from '@/components/form/Input.vue';
import Select from '@/components/form/Select.vue';
import Radio from '@/components/form/Radio.vue';
import Checkbox from '@/components/form/Checkbox.vue';
import Croppie from '@/components/form/Croppie.vue';
import Password from '@/components/form/Password.vue';
import Label from '@/components/form/Label.vue';
import Range from '@/components/form/Range.vue';
import Currency from '@/components/form/Currency.vue';
import Collection from '@/components/form/Collection.vue';
import SingleColumn from '@/components/layouts/SingleColumn.vue'
import DoubleColumn from '@/components/layouts/DoubleColumn.vue'
import TripleColumn from '@/components/layouts/TripleColumn.vue'
import Textarea from '@/components/form/Textarea.vue';
import WYSIWYG from '@/components/form/WYSIWYG.vue';
import FilePond from '@/components/form/FilePond.vue';
import VSelect from '@/components/form/VSelect.vue';

export const useCreateFormStore = defineStore('useCreateFormStore', {
    state: () => {
        return {
            fields: {},
            layout: 1
        }
    },
    actions: {
        getComponent(as) {
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

                case "vselect":
                        return VSelect;
                    break;
            
                default:
                        return Input;
                    break;
            }
        }
    },
    getters: {
        getRightFields: (state) => {
            return state.getFields.filter(field => field.column == 'right')
        },
        getLeftFields: (state) => {
            return state.getFields.filter(field => field.column == 'left')
        },
        getCenterFields: (state) => {
            return state.getFields.filter(field => field.column == 'center')
        },
        getFields: (state) => {
            return state.fields.map(field => {
                field['as'] = field.as == undefined ? "input" : field.as;
                field['label'] = field.label == undefined ? "" : field.label;
                field['placeholder'] = field.placeholder == undefined ? field.label : field.placeholder;
                field['error'] = field.error == undefined ? "" : field.error;
                field['class'] = field.class == undefined ? "form-control" : field.class;
                field['layout'] = field.layout !== "linear" ? "" : field.layout;
                field['layout'] = field.linear == true ? "linear" : field.layout;
                field['column'] = field.column == undefined ? "left" : field.column;
                switch (field.column) {
                    case "left":
                    case "right":
                        
                        break;

                    case "center":
                        field['column'] = state.layout < 3 ? "left" : "center";
                        break;
                
                    default:
                        field['column'] = "left"
                        break;
                }
                return field;
            })
        },
        formLayout: (state) => {
            switch (state.layout) {
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
    }
})