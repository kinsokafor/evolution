<template>
    <div class="filter-container pagination-filter col-md-3 col-sm-6 col-sm-12 ">
        <button @click.prevent="prevPage()" class="prev">prev</button>
        <span>Page {{filterStore.page}}</span>
        <button @click.prevent="nextPage()" class="next">next</button>
    </div>
</template>

<script setup>
    import { useFilterStore } from '@/store/filter';
    const filterStore = useFilterStore();

    const props = defineProps({
        filterGroup: {
            type: String,
            default: ""
        }
    })

    function prevPage() {
        if(filterStore.page > 1) {
            filterStore.page--
            updateFilter()
        }
    }

    function nextPage() {
        if(filterStore.totalRecords == 0) return;
        if(filterStore.totalRecords/filterStore.filters[filterStore.filterKey].limit <= filterStore.page) return;
        filterStore.page++
        updateFilter()
    }

    function updateFilter() {
        const limit = filterStore.filters[filterStore.filterKey].limit
        const offset = (filterStore.page - 1) * limit;
        filterStore.addFilter("offset", offset);
    }
</script>

<style lang="scss" scoped>
    button {
        border: none;
        padding: 2.5px 9px;
        background: #f2efef;
        border-radius: 8px 0 0 8px;
        color: #6e6d6d;
    }

    .prev {
        border-radius: 8px 0 0 8px;
    }

    .next {
        border-radius: 0 8px 8px 0;
    }

    .pagination-filter span {
        padding: 0 10px;
        font-size: 12px;
        color: #898787;
    }
</style>