<template>
    <div class="filter-container select-filter col-md-3 col-sm-6 col-sm-12 ">
        <select v-model="currentValue" :id="id" @change="onChange">
            <option value="">Select {{label}}</option>
            <option 
                :value="typeof option == 'object' ? option.value : option" 
                v-for="option in options" 
                :key="typeof option == 'object' ? option.value : option"
            >{{typeof option == 'object' ? option.name : option}}</option>
        </select>
    </div>
</template>

<script setup>
    import { onMounted, ref } from 'vue';
    import { randomId } from '@/helpers';
    import { useFilterStore } from '@/store/filter';

    const filterStore = useFilterStore();
    

    const props = defineProps({
        label: {
            type: String,
            default: "",
        },
        name: {
            type: String
        },
        options: {
            type: Object,
            default: {}
        },
        id: {
            type: String,
            default: randomId(10)
        },
        selected: {
            default: ""
        },
        filterKey: {
            type: String,
            default: "default"
        }
    })
    const currentValue = ref(props.selected);

    onMounted(() => {
        var oldValue = filterStore.get(props.name, props.filterKey);
        if(oldValue) {
            currentValue.value = oldValue;
        } else if (props.selected !== "") {
            filterStore.add(props.name, props.selected, props.filterKey);
        }
    })

    const onChange = () => {
        filterStore.add(props.name, currentValue.value, props.filterKey);
    }
</script>

<style lang="scss" scoped>
    .filter-container.select-filter select {
        width: 100%;
        padding: 4px 10px;
        border: none;
        color: var(--shadow3);
        background: transparent;
        border-bottom: 1px solid var(--highlight3);
    }
</style>