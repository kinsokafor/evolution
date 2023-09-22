<template>
    <Restricted access="3">
        <div>
            <Filters filterKey="usersList" v-slot="{key}">
                <SelectFilter name="status" :filterKey="key" :options="status" selected="active" label="Status"/>
                <TextFilter name="fullname" :filterKey="key" value="" label="Name"/>
                <SelectFilter name="role" :filterKey="key" :options="configStore.roles" label="Role"/>
            </Filters>
        </div>
        <Table 
            @disableUser="disable" 
            @enableUser="enable" 
            @deleteUser="deleteUser" 
            :columns="columns" 
            :serial-number="filterStore.getSerialNumber" 
            :actions="actions" 
            :data="users"
            :processing="usersStore.processing"
        ></Table>
    </Restricted>
</template>

<script setup>
    import { onMounted, computed, watch } from 'vue'
    import { useUsersStore } from '@/Modules/Main/store/users'
    import SelectFilter from '@/components/filters/SelectFilter.vue';
    import TextFilter from '@/components/filters/TextFilter.vue';
    import Filters from '@/components/filters/Filters.vue';
    import Limit from '@/components/filters/Limit.vue';
    import Table from '@/components/Table.vue';
    import Pagination from '@/components/filters/Pagination.vue';
    import { useFilterStore } from '@/store/filter';
    import { useConfigStore } from '@/store/config';
    import Summary from '@/components/filters/Summary.vue';
    import {getFullname} from '@/helpers'
    import config from '/config.json'
    import male from '@/components/images/male_avatar.svg'

    const filterStore = useFilterStore();

    const usersStore = useUsersStore();
    
    const configStore = useConfigStore();

    const allUsers = computed(() => usersStore.get)

    const users = computed(() => {
        const filters = filterStore.getFilters('usersList')
        const all = allUsers.value
        if(all != undefined) {
            return all.filter(j => {
                for(var filter in filters) {
                    if(filter == 'fullname') {
                        const pos = getFullname(j).toLowerCase().search(filters[filter].replaceAll('%', '').toLowerCase())
                        if(pos == -1) return false
                    }else if(filter in j) {
                        if(filters[filter] != j[filter]) return false
                    }
                }
                return true
            }).map(i => {
                i.fullname = getFullname(i)
                i.role_name = config.Auth.roles[i.role].name ?? ""
                i.profile_display = "<img src=\""+(i.profile_picture ?? male)+"\" style=\"margin: 0 !important;width: 49px !important;\">"
                i.link = getLink(i.id, i.role)
                return i
            })
        }
    })

    const columns = {
        fullname: "Fullname",
        email: "Email",
        phone: "Phone Number",
        role_name: "Role",
        profile_display: "Photo"
    }

    const getLink = (id, role) => {
        if("profile" in config.Auth.roles[role]) {
            return ("/"+config.Auth.roles[role].profile+"/"+id).replace("//", "/").replace("//", "/");
        }
        return ("/admin/#/profile/"+id).replace("//", "/").replace("//", "/")
    } 

    const actions = [
        // {
        //     name: "Profile",
        //     type: "router-link",
        //     url: "/profile/{id}",
        //     params: "id"
        // },
        // {
        //     name: "Profile2",
        //     type: "link",
        //     url: "/profile?id={id}",
        //     params: ["id"],
        //     notConditions: {
        //         email: "info@swiftwaylog.com"
        //     }
        // },
        {
            name: "Disable",
            type: "action",
            callback: "disableUser",
            conditions: {
                status: "active"
            }
        },
        {
            name: "Enable",
            type: "action",
            callback: "enableUser",
            conditions: {
                status: "inactive"
            }
        },
        {
            name: "Delete",
            type: "action",
            callback: "deleteUser",
            conditions: {
                status: "inactive"
            }
        }
    ]

    const status = [
        {name: "Active", value: "active"},
        {name: "Inactive", value: "inactive"}
    ]

    onMounted(() => {

    })

    function disable(row, index) {
        usersStore.disable(row, index);
    }

    function enable(row, index) {
        usersStore.enable(row, index);
    }

    function deleteUser(row, index) {
        if(confirm("Are you sure that you want to delete "+getFullname(row)+" from the system?")) {
            usersStore.deleteUser(row, index);
        }
    }
</script>

<style lang="scss" scoped>

</style>