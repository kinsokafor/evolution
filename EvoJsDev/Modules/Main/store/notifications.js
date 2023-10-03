import {defineStore, storeToRefs} from 'pinia';
import { useAuthStore } from '@/store/auth';
import { useAlertStore } from '@/store/alert';
import { useConfigStore } from '@/store/config';
import { nonce } from '@/helpers';
import axios from 'axios';

export const useNotificationsStore = defineStore('useNotificationsStore', {
    state: () => {
        
        return {
            logs: [],
            processing: false,
            limit: 100,
            offset: 0,
            fetching: false
        }
    },
    actions: {
        async getFromServer(user_id) {
            this.processing = true
            this.fetching = true
            const url = process.env.EVO_API_URL + `/api/dbtable/notification?user_id=${user_id}&limit=${this.limit}&offset=${this.offset}&order_by=id&order=desc`
            axios.get(url, {
                'Access-Control-Allow-Credentials':true,
                headers: {
                    'Access-Control-Allow-Origin': '*', 
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${nonce()}` 
                }
            }).then(r => {
                this.processing = false
                r.data.forEach(i => {
                    const index = this.logs.findIndex(j => j.id == i.id)
                    if(index == -1) {
                        this.logs = [...this.logs, i]
                    } else {
                        this.logs[index] = i
                    }
                })
                if(r.data.length >= this.limit) {
                    this.offset = this.limit + this.offset
                    this.getFromServer(user_id)
                } else {
                    this.fetching = false
                    this.offset = 0
                }
            }).catch(e => {
                this.processing = false
            })
        }
    },
    getters: {
        get: (state) => {
            const authStore = storeToRefs(useAuthStore())
            const user = authStore.getUser.value
            if(user.id == undefined) return []
            if(!state.fetching) state.getFromServer(user.id)
            return state.logs
        }
    }
})