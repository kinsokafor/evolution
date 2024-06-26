import {defineStore} from 'pinia';
import { useFilterStore } from '@/store/filter';
import { useAlertStore } from '@/store/alert';
import { useLocalStorage } from '@vueuse/core'
import config from '/config.json';
import {getFullname, storeGetter} from '@/helpers'

import { Users } from '@/helpers';

export const useUsersStore = defineStore('useUsersStore', {
    state: () => {
        return {
            data: useLocalStorage(`${config.salt}evo-users`, []),
            filterStore: useFilterStore(),
            alertStore: useAlertStore(),
            processing: false,
            usersClass: new Users,
            format: "SMO",
            limit: 50,
            offset: 0,
            fetching: false,
            sent: [],
            lastTimeOut: null,
            loaded: [],
            u: new Users
        }
    },
    actions: {
        async loadFromServer(params = {}) {
            for (var k in params) {
                //return when all data are not ready
                if (params[k] == undefined) return;
            }
            this.fetching = true;
            this.u.get({
                limit: this.limit,
                offset: this.offset,
                ...params
            }).then(r => {
                if ("id" in params) {
                    // const meta = JSON.parse(r.data.meta)
                    // delete r.data.meta
                    let i = { ...r.data }
                    i.fullname = getFullname(i, this.format)
                    this.loaded.push(i?.id)
                    const index = this.data.findIndex(j => j.id == i.id)
                    if (index == -1) {
                        this.data = [...this.data, i]
                    } else {
                        if (!_.isEqual(this.data[index], i)) {
                            this.data[index] = i
                        }
                    }
                } else {
                    r.data.forEach(i => {
                        i.fullname = getFullname(i, this.format)
                        const index = this.data.findIndex(j => j.id == i.id)
                        if (index == -1) {
                            this.data = [...this.data, i]
                        } else {
                            if (!_.isEqual(this.data[index], i)) {
                                this.data[index] = i
                            }
                        }
                    })
                }
                if (r.data.length >= this.limit) {
                    this.offset = this.limit + this.offset
                    this.processing = false;
                    this.loadFromServer(params)
                } else {
                    this.offset = 0
                    if(this.lastTimeOut != null) {
                        clearTimeout(this.lastTimeOut)
                    }
                    this.lastTimeOut = setTimeout(() => {
                        this.fetching = false
                    }, 300000)
                }
            }).finally(i => {
                this.processing = false;
            })
        },
        async enable(row, index) {
            this.processing = true;
            index = this.data.findIndex(i => i.id == row.id)
            await this.u.update(row.id, {
                "status" : "active",
                "id": row.id
            }).then(response => {
                this.processing = false;
                this.data[index] = response.data
                this.alertStore.add("Done", "success");
            }).catch(response => {
                this.alertStore.add(response.message, "danger");
                this.processing = false;
            });
        },
        async disable(row, index) {
            this.processing = true;
            index = this.data.findIndex(i => i.id == row.id)
            await this.u.update(row.id, {
                "status" : "inactive",
                "id": row.id
            }).then(response => {
                this.processing = false;
                this.data[index] = response.data
                this.alertStore.add("Done", "success");
            }).catch(response => {
                this.alertStore.add(response.message, "danger");
                this.processing = false;
            });
        },
        async deleteUser(row, index) {
            this.processing = true;
            index = this.data.findIndex(i => i.id == row.id)
            await this.u.delete({
                "status" : "inactive",
                "id": row.id
            }).then(response => {
                const index = this.loaded.findIndex(x => x.id == row.id);
                if(index != -1) {
                    this.loaded.splice(index, 1)
                }
                this.processing = false;
                delete this.data[index]
                this.alertStore.add("Done", "success");
            }).catch(response => {
                this.alertStore.add(response.message, "danger");
                this.processing = false;
            });
        },
        abort() {
            this.u.abort()
            this.fetching = false;
            this.offset = 0;
            if(this.lastTimeOut != null) {
                clearTimeout(this.lastTimeOut)
            }
        }
    },
    getters: {
        all: (state) => {
            if (!state.fetching) {
                state.processing = true;
                state.loadFromServer()
            }
            return state.data;
        },
        get: (state) => {
            const data = state.data
            return (params = {}, ...exclude) => {
                return storeGetter(state, data, (tempParams) => {
                    state.loadFromServer(tempParams)
                }, params, exclude)
            }
        },
        getUser: (state) => {
            return (id) => {
                if(state.loaded.findIndex(i => i == id) == -1) {
                    state.abort()
                    return state.get({id: id})[0] ?? {}
                }
                return state.data.find(i => i.id == id);
            }
        }
    }
})