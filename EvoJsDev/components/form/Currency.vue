<template>
    <div>
        <Input 
            v-bind="$attrs" 
            :label="$attrs.label + ' in ' + currencyStore.activeCurrency" 
            as="input" 
            :attrs="attributes"
            >
            <template #hint v-if="currencyStore.allowedCurrencies.length > 1 && showConverter && convertible">
                <em class="note">convert to
                <button 
                    class="currency-switch-buttons"
                    v-for="currency in currencyStore.allowedCurrencies" 
                    :key="currency"
                    @click.prevent="currencyStore.setActiveCurrency(currency)"
                    >{{currency}}</button>
                    conversions are done using the official central banks rate. Please confirm before you accept.
                </em>
            </template>
            
        </Input>
    </div>
</template>

<script setup>
    import Input from './Input.vue'
    import { ref, useAttrs, onMounted, watch } from 'vue';
    import { useCurrencyStore } from '@/store/currency';
    import { useField } from 'vee-validate';

    const attributes = ref(useAttrs().attrs);
    const convertible = ref(true);
    const currencyStore = useCurrencyStore();
    const showConverter = ref(true);
    
    const amount = useField(useAttrs().name);

    const currencyIn = useField(`${useAttrs().name}_currency`);

    currencyStore.displayAmount = amount.value

    const props = defineProps({
        as: {
            type: String,
            default: "input"
        },
        values: Object
    })

    onMounted(() => {
        if(attributes.value.currency != undefined) {
            currencyStore.active = attributes.value.currency;
            currencyStore.base = attributes.value.currency;
        }
        if(attributes.value.convertible != undefined) {
            convertible.value = attributes.value.convertible
        }
    })

    watch(currencyStore, (state) => {
        currencyIn.value.value = state.activeCurrency;
    })

</script>

<script>
    export default {
        inheritAttrs: false
    }
</script>

<style lang="scss" scoped>
    .currency-switch-buttons {
        border: none;
        border-radius: 3px;
        background: var(--blue);
        color: var(--highlight1);
        display: inline-block;
        margin-right: 5px;
        font-size: smaller;
    }
    .note {
        color: var(--highlight3)
    }
</style>