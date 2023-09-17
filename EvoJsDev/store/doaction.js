import {defineStore} from 'pinia';
import axios from 'axios';
import { nonce } from '@/helpers';

export const useDoActionStore = defineStore('useDoActionStore', {
    state: () => {
        return {
            template: ""
        }
    },
    actions: {
        async doAction(action, args) {
            var data = {
                data: {
                    action: action,
                    args: args
                },
                nonce: this.nonceStore.nonce
            }
            await axios.post(process.env.EVO_API_URL + '/api/doaction', JSON.stringify(data), {
                withCredentials: true,
                headers: {'Access-Control-Allow-Origin': '*', 'Content-Type': 'application/json'}
            }).then(response => {
                this.template = response.data
            })
        }
    }
})