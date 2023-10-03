import {defineStore} from 'pinia';
import axios from 'axios';
import { nonce } from '@/helpers';
import { useLocalStorage } from '@vueuse/core';

export const useFilterStore = defineStore('useFilterStore', {
    state: () => {
        return {
            default: useLocalStorage('evo-default-filters', {}),
            requestURL: {},
            page: 1,
            limit: 0,
            totalRecords: 0,
            records: 0,
            filterKey: "default",
            filters: {default: {}}//useLocalStorage('evo-filters', {default: {}})
        }
    },
    actions: {
        add(filterName, value, key = "default") {
            if(value == "") {
                delete this.filters[key][filterName];
                return;
            } 
            this.filters[key][filterName] = value;
        },
        get(filter, key = "default") {
            if(Object.hasOwnProperty.call(this.filters, key)) {
                if(Object.hasOwnProperty.call(this.filters[key], filter)) {
                    return this.filters[key][filter];
                } else return false;
            } else return false;
        },
        reset(key = "default") {
            this.filters[key] = {}
            window.location.reload(true);
        },
        addFilter(name, value) {
            if(!Object.hasOwnProperty.call(this.filters, this.filterKey))
                this.filters[this.filterKey] = {};
            if(value == "") {
                delete this.filters[this.filterKey][name];
                return;
            }
            this.filters[this.filterKey][name] = value;
        },
        // getFilter(filter, key = "default") {
        //     if(Object.hasOwnProperty.call(this.filters, key)) {
        //         if(Object.hasOwnProperty.call(this.filters[key], filter)) {
        //             return this.filters[key][filter];
        //         } else return false;
        //     } else return false;
        // },
        // getFilters(key = "default") {
        //     if(Object.hasOwnProperty.call(this.filters, key)) {
        //         return this.filters[key]
        //     } else return false;
        // },
        addRequestURL(response) {
            if(!Object.hasOwnProperty.call(this.requestURL, this.filterKey) 
            && response.request.status == 200) {
                const [url, ] = response.request.responseURL.split("?");
                this.requestURL[this.filterKey] = url;
            }
            if(Object.hasOwnProperty.call(this.filters[this.filterKey], "limit")) {
                this.limit = this.filters[this.filterKey]['limit']
            } else this.limit = 0;
            this.records = response.data.length;
            this.getTotalRecords();
        },
        async getTotalRecords() {
            var link = this.getCountLink;
            await axios.get(link, {
                'Access-Control-Allow-Credentials':true,
                headers: {
                    'Access-Control-Allow-Origin': '*', 
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${nonce()}` 
                }
            })
            .then(response => {
                this.totalRecords = response.data
            });
        }
    },
    getters: {
        getFilters: (state) => {
            return (key = "default") => {
                if(key in state.filters) {
                    return state.filters[key]
                } else return {};
            }
        },
        getFilter: (state) => {
            return (filter, key = "default") => {
                if(key in state.filters)
                    return state.filters[key][filter];
            }
        },
        getCountLink: (state) => {
            const filters = state.getFilters;
            if(Object.hasOwnProperty.call(state.requestURL, state.filterKey)) {
                const link = new URL(state.requestURL[state.filterKey]);
                link.searchParams.append("iscount", true);
                for (const key in filters) {
                    if(key == "limit") continue;
                    if(key == "offset") continue;
                    if (Object.hasOwnProperty.call(filters, key)) {
                        const value = filters[key];
                        link.searchParams.append(key, value);
                    }
                }
                return link;
            }
            return "";
        },
        getSerialNumber: (state) => {
            if(state.limit == 0) return 1;
            return ((state.page - 1) * state.limit) + 1;
        }
    }
})