import {defineStore} from 'pinia';
import { useFilterStore } from '@/store/filter';
import { useAlertStore } from '@/store/alert';

import { Users } from '@/helpers';

export const useUsersStore = defineStore('useUsersStore', {
    state: () => {
        return {
            users: [],
            filterStore: useFilterStore(),
            alertStore: useAlertStore(),
            processing: false,
            usersClass: new Users,
            limit: 100,
            offset: 0
        }
    },
    actions: {
        async getFromServer(params = {}) {
            this.processing = true
            const users = new Users()
            params.limit = this.limit
            params.offset = this.offset
            await users.get(params).then(r => {
                if(Array.isArray(r.data)) {
                    r.data.forEach(j => {
                        const index = this.users.findIndex(i => i.id == j.id)
                        if(index == -1) {
                            this.users = [...this.users, j]
                        } else {
                            this.users[index] = j
                        }
                    }) 
                    this.processing = false
                    if(r.data.length >= this.limit) {
                        this.offset = this.offset + this.limit
                        this.getFromServer(params)
                    }
                }
                else if(typeof(r.data) == 'object') {
                    const index = this.users.findIndex(i => i.id == r.data.id)
                    if(index == -1) {
                        this.users = [...this.users, r.data]
                    } else this.users[index] = r.data
                }
            }).catch(r => {
                this.alertStore.add(r.message, "danger");
                this.processing = false;
            })
        },
        async enable(row, index) {
            this.processing = true;
            index = this.users.findIndex(i => i.id == row.id)
            await this.usersClass.update(row.id, {
                "status" : "active",
                "id": row.id
            }).then(response => {
                this.processing = false;
                this.users[index] = response.data
                this.alertStore.add("Done", "success");
            }).catch(response => {
                this.alertStore.add(response.message, "danger");
                this.processing = false;
            });
        },
        async disable(row, index) {
            this.processing = true;
            index = this.users.findIndex(i => i.id == row.id)
            await this.usersClass.update(row.id, {
                "status" : "inactive",
                "id": row.id
            }).then(response => {
                this.processing = false;
                this.users[index] = response.data
                this.alertStore.add("Done", "success");
            }).catch(response => {
                this.alertStore.add(response.message, "danger");
                this.processing = false;
            });
        },
        async deleteUser(row, index) {
            this.processing = true;
            index = this.users.findIndex(i => i.id == row.id)
            await this.usersClass.delete({
                "status" : "inactive",
                "id": row.id
            }).then(response => {
                this.processing = false;
                delete this.users[index]
                this.alertStore.add("Done", "success");
            }).catch(response => {
                this.alertStore.add(response.message, "danger");
                this.processing = false;
            });
        }
    },
    getters: {
        get: (state) => {
            state.getFromServer()
            return state.users
        },
        getUser: (state) => {
            return (id) => {
                const user = state.users.find(i => i.id == id)
                if(user == undefined) {
                    state.getFromServer({id: id})
                    return {}
                }
                return user
            }
        }
    }
})