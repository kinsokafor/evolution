<template>
    <div class="filter-container summary-filter col-md-6 col-sm-12 ">
        Showing {{filterStore.records}} of {{filterStore.totalRecords}} record<span v-if="filterStore.totalRecords > 1">s</span>
    </div>
</template>

<script setup>
    import { useFilterStore } from '@/store/filter';
    const filterStore = useFilterStore();
    
</script>

<style lang="scss" scoped>
    .summary-filter {
        text-align: right;
    }
    @media screen and (max-width: 767px) {
        .summary-filter {
            text-align: center;
        }
    }
</style>