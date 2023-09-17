<template>
    <SelectFilter 
    name="limit" 
    :options="limits" 
    :selected="selected" 
    label="Limit"/>
</template>

<script setup>
    import SelectFilter from '@/components/filters/SelectFilter.vue';
    const limits = [ 
        {name: "2 Records", value: 2},
        {name: "10 Records", value: 10},
        {name: "20 Records", value: 20},
        {name: "50 Records", value: 50},
        {name: "100 Records", value: 100},
        {name: "All Records", value: ""}
    ]

    const props = defineProps({
        selected: {
            type: Number,
            default: 10
        }
    })
</script>

<style lang="scss" scoped>

</style>