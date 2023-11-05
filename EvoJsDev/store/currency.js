import {defineStore, storeToRefs} from 'pinia';
import {currencyConverter} from '@/helpers';
import { useConfigStore } from '@/store/config'

export const useCurrencyStore = defineStore('useCurrencyStore', {
    state: () => {
        return {
            active: "any",
            displayAmount: 0,
            base: "any"
        }
    },
    actions: {
        setActiveCurrency(currency) {
            this.active = currency;
            currencyConverter(this.active, this.baseCurrency, this.amount).then(response => {
                this.displayAmount = response;
                this.base = currency;
            });
        },
        displayAmount(amount, base) {
            return currencyConverter(this.activeCurrency, base, amount);
        }
    },
    getters: {
        activeCurrency: (state) => {
            if(state.active == "any") {
                return state.baseCurrency
            }
            return state.active;
        },
        baseCurrency: (state) => {
            const config = storeToRefs(useConfigStore());
            if(state.base == "any") {
                return config.get.value('currency.base')
            }
            return state.base;
        },
        allowedCurrencies: (state) => {
            const config = storeToRefs(useConfigStore());
            if(config.get.value('currency.allowed') == "") return []
            return config.get.value('currency.allowed').filter(x => x != state.active);
        },
        amount: (state) => {
            return state.displayAmount;
        }
    }
})