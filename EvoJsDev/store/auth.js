import {defineStore} from 'pinia';
import axios from 'axios';
import { nonce } from '@/helpers';
import { arrayIntersect } from '@/helpers';

export const useAuthStore = defineStore('useAuthStore', {
    state: () => {
        return {
            isloggedIn: false,
            currentUser: {},
            userScope: [],
            access: [],
            expiry: 0,
            failedTest: false
        }
    },
    actions: {
        async getLoginStatus() {
            return await axios.post(process.env.EVO_API_URL + '/api/loginStatus', {}, {
                withCredentials: true,
                headers: {
                    'Access-Control-Allow-Origin': '*', 
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${nonce()}` 
                }
            })
        },
        async loginStatus() {
            await this.getLoginStatus().then(response => {
                this.isloggedIn = response.data.loginStatus;
                this.expiry = response.data.expiry;
                if(response.data.loginStatus) {
                    this.failedTest = false
                    this.currentUser = response.data.currentUser;
                    this.userScope = response.data.userScope;
                } else {
                    this.failedTest = true
                }
            });
        },
        isExpired() {
            const expiry = this.expiry * 1000
            const date = new Date();
            return expiry < date.valueOf() ? true : false;
        },
        testAccess() {
            if(this.isExpired()) {
                this.loginStatus();
                return false;
            }
            if(!this.isloggedIn) {
                this.loginStatus();
                return false;
            } else {
                if(this.getScope.length == 0) return true;
                const intersect = arrayIntersect(this.getScope, this.userScope);
                return intersect.length > 0;
            }
        }
    },
    getters: {
        getAccess: (state) => {
            return state.testAccess();
        },
        getScope: (state) => {
            if(typeof(state.access) == "string") {
                return state.access.trim() == "" ? [] : state.access.trim().split(",").map((x) => parseInt(x))
            }
            return state.access
        },
        getUser: (state) => {
            if(state.currentUser.id !== undefined) {
                return state.currentUser
            }
            state.loginStatus()
            return state.currentUser
        }
    }
})