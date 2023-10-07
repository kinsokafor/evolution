<template>
    <div>
        <LinearMenu :items="config.get('mainMenu')" title="Modules"></LinearMenu>
    </div>
</template>

<script setup>
    import LinearMenu from './LinearMenu.vue'
    import { useConfigStore } from '@/store/config'

    const config = useConfigStore();
</script>

<style lang="scss" scoped>
    
</style>