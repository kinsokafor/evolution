import {defineStore} from 'pinia';
import { useConfigStore } from './config';
import {currencyConverter} from '@/helpers';
import {ref} from 'vue';

export const useCurrencyStore = defineStore('useCurrencyStore', {
    state: () => {
        return {
            config: useConfigStore(),
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
                state.active = state.config.currency.base;
            }
            return state.active;
        },
        baseCurrency: (state) => {
            if(state.base == "any") {
                return state.config.currency.base;
            }
            return state.base;
        },
        allowedCurrencies: (state) => {
            return state.config.currency.allowed.filter(x => x != state.active);
        },
        amount: (state) => {
            return state.displayAmount;
        }
    }
})