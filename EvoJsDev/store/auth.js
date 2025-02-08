import {defineStore} from 'pinia';
import axios from 'axios';
import { nonce, Request } from '@/helpers';
import { arrayIntersect } from '@/helpers';
import { useLocalStorage } from '@vueuse/core';
import config from '/config.json';

export const useAuthStore = defineStore('useAuthStore', {
    state: () => {
        return {
            isloggedIn: false,
            currentUser: {},
            userScope: [],
            access: [],
            expiry: 0,
            failedTest: false,
            fetching: false
        }
    },
    actions: {
        async getLoginStatus() {
            const req = new Request()
            return await req.post(req.root + '/api/loginStatus', {});
        },
        async loginStatus() {
            this.fetching = true;
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
                this.fetching = false;
            });
        },
        isExpired() {
            const expiry = this.expiry * 1000
            const date = new Date();
            return expiry < date.valueOf() ? true : false;
        },
        testAccess(scope = null) {
            if(this.isExpired()) {
                this.loginStatus();
                return false;
            }
            if(!this.isloggedIn) {
                if(!this.fetching && !this.failedTest) {
                    this.loginStatus();
                }
                return false;
            } else {
                if(scope == null) {
                    scope = this.getScope
                } else if(typeof(scope) == "string") {
                    scope = this.toArray(scope)
                }
                if(scope.length == 0) return true;
                const intersect = arrayIntersect(scope, this.userScope);
                return intersect.length > 0;
            }
        },
        toArray(string) {
            return string.trim() == "" ? [] : string.trim().split(",").map((x) => parseInt(x))
        },
        async login(data) {
            const req = new Request()
            return await req.post(req.root + "/api/login", data).then(response => {
                this.isloggedIn = response.data.loginStatus;
                this.expiry = response.data.expiry;
                if(response.data.loginStatus) {
                    this.failedTest = false
                    this.currentUser = response.data.currentUser;
                    this.userScope = response.data.userScope;
                } else {
                    this.failedTest = true
                }
                return response
            })
        }
    },
    getters: {
        getAccess: (state) => {
            return state.testAccess();
        },
        getScope: (state) => {
            if(typeof(state.access) == "string") {
                return state.toArray(state.access)
            }
            return state.access
        },
        getUser: (state) => {
            if(state.currentUser.id !== undefined) {
                return state.currentUser
            }
            state.loginStatus()
            return state.currentUser
        },
        hasAccess: (state) => {
            const c = state.currentUser;
            return (access) => {
                return state.testAccess(access);
            }
        }
    }
})