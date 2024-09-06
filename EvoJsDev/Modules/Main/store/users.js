import {defineStore} from 'pinia';
import { useFilterStore } from '@/store/filter';
import { useAlertStore } from '@/store/alert';
import { useSessionStorage } from '@vueuse/core'
import config from '/config.json';
import {getFullname, storeGetter, Users} from '@/helpers'

export const useUsersStore = defineStore('useUsersStore', {
    state: () => {
        return {
            data: useSessionStorage(`${config.salt}evo-users`, []),
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
                this.processing = false;
                if (r.data.length >= this.limit) {
                    this.offset = this.limit + this.offset
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
        async tempDeleteUser(row, index) {
            this.processing = true;
            index = this.data.findIndex(i => i.id == row.id)
            await this.u.update(row.id, {
                "status" : "deleted",
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
            return (params = {}) => {
                if('id' in params) {
                    if(state.sent.findIndex(i => i == params.id) == -1) {
                        state.sent.push(params.id)
                        state.loadFromServer(params)
                    }
                } else if (!state.fetching || !_.isEqual(params, state.lastParams)) {
                    state.lastParams = params;
                    state.processing = true;
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
            const data = state.data
            return (id) => {
                if(state.loaded.findIndex(i => i == id) == -1) {
                    // state.abort()
                    return state.get({id: id})[0] ?? {}
                }
                return data.find(i => i.id == id);
            }
        }
    }
})