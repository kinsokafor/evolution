import {defineStore} from 'pinia';
import axios from 'axios';
import {nonce, findByDottedIndex} from '@/helpers'
import _ from 'lodash'

export const useConfigStore = defineStore('useConfigStore', {
    state: () => {
        return {
            props: {},
            loaded: false
        }
    },
    actions: {
        async loadFromServer() {
            this.loaded = true
            await axios.post(process.env.EVO_API_URL + '/api/config/all', {}, {
                'Access-Control-Allow-Credentials':true,
                headers: {
                    'Access-Control-Allow-Origin': '*', 
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${nonce()}` 
                }
            }).then(r => {
                this.props = r.data.data;
            })
        },
        async update(values) {
            await axios.post(process.env.EVO_API_URL + "/api/config/", values, {
                'Access-Control-Allow-Credentials':true,
                headers: {
                    'Access-Control-Allow-Origin': '*', 
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${nonce()}` 
                }
            }).then(r => {
                for(var key in values) {
                    this.props[key] = values[key]
                }
            });
        },
        async delete(key) {
            await axios.delete(process.env.EVO_API_URL + "/api/config/"+key, {
                'Access-Control-Allow-Credentials':true,
                headers: {
                    'Access-Control-Allow-Origin': '*', 
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${nonce()}` 
                }
            }).then(r => {
                delete this.props[key]
            });
        }, 
        async new(values) {
            await this.update(values)
        }
    },
    getters: {
        get: (state) => {
            const p = state.all
            return (key) => {
                if(_.isEmpty(p)) {
                    return null
                }
                return findByDottedIndex(key, p);
            }
        },
        all: (state) => {
            if(!state.loaded) {
                state.loadFromServer()
            }
            return state.props
        }
    }
})