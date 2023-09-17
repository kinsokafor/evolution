import {defineStore} from 'pinia';
const config = require('../../config.json');

export const useConfigStore = defineStore('useConfigStore', {
    state: () => {
        return {
            ...config
        }
    },
    getters: {
        roles: (state) => {
            return Object.entries(state.Auth.roles).map(role => {
                return {
                    name: role[1].name,
                    value: role[0],
                    capacity: role[1].capacity,
                }
            })
        }
    }
})