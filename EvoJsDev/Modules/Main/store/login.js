import {defineStore} from 'pinia';
import axios from 'axios';
import { nonce } from '@/helpers';
import { useAlertStore } from '@/store/alert';

export const useLoginStore = defineStore('useLoginStore', {
    state: () => {
        return {
            ismail: true,
            alertStore: useAlertStore(),
            submitting: false,
        }
    },
    actions: {
        switchTab() {
            this.ismail = !this.ismail;
        },
        async doLogin(values) {
            this.submitting = true;
            await axios.post(process.env.EVO_API_URL + '/api/login', JSON.stringify(values), {
                withCredentials: true,
                headers: {
                    'Access-Control-Allow-Origin': '*', 
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${nonce()}` 
                }
            }).then(response => {
                this.submitting = false;
                if(response.data.loginStatus) {
                  window.location = "/";
                } else {
                    this.alertStore.add(response.data.msg, "danger");
                }
            }).catch(error => {
                this.alertStore.add(error.message, "danger");
            });
        }
    }
})