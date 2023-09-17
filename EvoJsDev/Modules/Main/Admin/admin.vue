<template>
    <div class="row">
        <div class="col-12">
            <router-view></router-view>
        </div>
        <!-- <div class="col-md-3">
            <Menu
                title=""
                :items="menuItems"
                container-class="col-12"
            ></Menu>
            <Main></Main> 
        </div> -->
    </div>
    <AlertVue />
</template>

<script setup>
    import AlertVue from '@/components/Alert.vue';
    // import Menu from '@/components/menu/Menu.vue';
    // import Main from '@/components/menu/Main.vue'
    const menuItems = [
        {
            label: "Dashboard",
            link: "/",
            iconClass: "fa-solid fa-gauge",
            access: "1,2,3,4,5,6,7,8,9"
        },
        {
            label: "Users",
            link: "/users",
            iconClass: "fa-solid fa-users",
            access: "3"
        }
    ]
</script>

<style lang="scss" scoped>

</style>