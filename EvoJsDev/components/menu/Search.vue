<template>
    <div>
        <input type="text" v-model="searchQuery" @keyup.prevent="$emit('value', searchQuery)" class="search-menu mb-4" placeholder="search for...">
    </div>
</template>

<script setup>
    import { ref } from 'vue';

    const searchQuery = ref("")

    const emit = defineEmits(['value'])
</script>

<style lang="scss" scoped>
    .search-menu {
        border: 1px solid var(--highlight2);
        background: var(--highlight1);
        padding: 5px 10px;
        width: 100%;
        max-width: 300px;
        border-radius: 5px;
    }
    .search-menu::placeholder {
        font-style: italic;
        color: #c6c6c6;
        font-size: 14px;
    }
</style>