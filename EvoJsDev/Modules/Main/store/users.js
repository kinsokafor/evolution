import {defineStore} from 'pinia';
import { useFilterStore } from '@/store/filter';
import { useAlertStore } from '@/store/alert';
import { useConfigStore } from '@/store/config';
import { Users } from '@/helpers';

export const useUsersStore = defineStore('useUsersStore', {
    state: () => {
        return {
            users: [],
            filterStore: useFilterStore(),
            alertStore: useAlertStore(),
            configStore: useConfigStore(),
            processing: false,
            usersClass: new Users,
            limit: 100,
            offset: 0
        }
    },
    actions: {
        async loadUsers() {
            this.processing = true;
            this.filterStore.filterKey = "usersList";
            await this.usersClass.get(this.filterStore.getFilters)
            .then(response => {
                this.users = response.data
                this.filterStore.addRequestURL(response)
                this.processing = false;
            })
            .catch(response => {
                this.alertStore.add(response.message, "danger");
                this.processing = false;
            });
        },
        async getFromServer(params) {
            this.processing = true
            const users = new Users()
            await users.get(params).then(r => {
                if(Array.isArray(r.data)) {
                    r.data.map(item => {
                        item['fullname'] = item.surname + ' ' + item.other_names;
                        item['role_name'] = this.configStore.Auth.roles[item.role].name ?? "";
                        item['profile_display'] = "<img src=\""+item.profile_picture+"\" style=\"margin: 0 !important;width: 49px !important;\">"
                        const index = this.users.findIndex(i => i.id == item.id)
                        if(index == -1) {
                            this.users = [...this.users, item]
                        } else this.users[index] = item
                    })
                    this.processing = false
                    if(r.data.length >= this.limit) {
                        this.offset = this.offset + this.limit
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
        getUsers: (state) => {
            return state.users.map((item, index, arr) => {
                item['fullname'] = item.surname + ' ' + item.other_names;
                item['role_name'] = state.configStore.Auth.roles[item.role].name ?? "";
                item['profile_display'] = "<img src=\""+item.profile_picture+"\" style=\"margin: 0 !important;width: 49px !important;\">"
                return item;
            })
        },
        get: (state) => {
            let params = state.filterStore.getFilters("usersList")
            params.limit = state.limit
            params.offset = state.offset
            state.getFromServer(params)
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