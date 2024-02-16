import {defineStore} from 'pinia';
import { useFilterStore } from '@/store/filter';
import { useAlertStore } from '@/store/alert';
import { useLocalStorage } from '@vueuse/core'
import config from '/config.json';

import { Users } from '@/helpers';

export const useUsersStore = defineStore('useUsersStore', {
    state: () => {
        return {
            data: useLocalStorage(`${config.salt}evo-users`, []),
            filterStore: useFilterStore(),
            alertStore: useAlertStore(),
            processing: false,
            usersClass: new Users,
            limit: 50,
            offset: 0,
            fetching: false,
            sent: []
        }
    },
    actions: {
        async loadFromServer(params = {}) {
            for (var k in params) {
                //return when all data are not ready
                if (params[k] == undefined) return;
            }
            this.fetching = true;
            this.processing = true;
            const u = new Users;
            u.get({
                limit: this.limit,
                offset: this.offset,
                ...params
            }).then(r => {
                if ("id" in params) {
                    // const meta = JSON.parse(r.data.meta)
                    // delete r.data.meta
                    let i = { ...r.data }
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
                    this.loadFromServer(params)
                } else {
                    this.offset = 0
                    setTimeout(() => {
                        this.fetching = false
                    }, 60000)
                }
            })
        },
        async enable(row, index) {
            this.processing = true;
            index = this.data.findIndex(i => i.id == row.id)
            const u = new Users;
            await u.update(row.id, {
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
            const u = new Users;
            await u.update(row.id, {
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
            const u = new Users;
            await u.delete({
                "status" : "inactive",
                "id": row.id
            }).then(response => {
                this.processing = false;
                delete this.data[index]
                this.alertStore.add("Done", "success");
            }).catch(response => {
                this.alertStore.add(response.message, "danger");
                this.processing = false;
            });
        }
    },
    getters: {
        all: (state) => {
            if (!state.fetching) {
                state.loadFromServer()
            }
            return state.data;
        },
        get: (state) => {
            const data = state.data
            return (params = {}) => {
                if (!state.fetching || !_.isEqual(params, state.lastParams)) {
                    state.lastParams = params;
                    state.loadFromServer(params)
                }
                const r = data.filter(i => {
                    for (var k in params) {
                        if(typeof params[k] == "string") {
                            return new RegExp('^' + params[k].replace(/\%/g, '.*') + '$').test(i[k])
                        }
                        if (k in i && params[k] != i[k]) return false
                        return true
                    }
                    return true
                })
                return r
            }
        },
        getUser: (state) => {
            return (id) => {
                return state.get({id: id})
            }
        }
    }
})