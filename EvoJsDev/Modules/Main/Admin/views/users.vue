<template>
    <Restricted access="3">
        <div>
            <Filters filterKey="usersList" v-slot="{key}">
                <SelectFilter name="status" :filterKey="key" :options="status" selected="active" label="Status"/>
                <TextFilter name="surname" :filterKey="key" value="" label="Surname"/>
                <TextFilter name="other_names" :filterKey="key" value="" label="Other Names"/>
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
            :data="usersStore.getUsers"
            :processing="usersStore.processing"
        ></Table>
        <div>
            <Filters filterKey="usersList">
                <Limit :selected="limit" />
                <Pagination />
                <Summary></Summary>
            </Filters>
        </div>
    </Restricted>
</template>

<script setup>
    import { onMounted, watch } from 'vue'
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
    import Restricted from '@/components/Restricted.vue';


    const filterStore = useFilterStore();
    const limit = 20;
    const usersStore = useUsersStore();
    
    const configStore = useConfigStore();

    const columns = {
        fullname: "Fullname",
        email: "Email",
        phone: "Phone Number",
        role_name: "Role",
        profile_display: "Photo"
    }

    const actions = [
        {
            name: "Profile",
            type: "router-link",
            url: "/profile/{id}",
            params: "id"
        },
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

    watch(filterStore.filters, () => {
        usersStore.loadUsers()
    })

    function disable(row, index) {
        usersStore.disable(row, index);
    }

    function enable(row, index) {
        usersStore.enable(row, index);
    }

    function deleteUser(row, index) {
        usersStore.deleteUser(row, index);
    }
</script>

<style lang="scss" scoped>

</style>