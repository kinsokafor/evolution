<template>
    <div class="filter-container text-filter col-md-3 col-sm-6 col-sm-12">
        <input type="text" v-model="currentValue" :id="id" @keyup="onChange" :placeholder="label">
    </div>
</template>

<script setup>
    import { onMounted, ref } from 'vue';
    import { randomId } from '@/helpers';
    import { useFilterStore } from '@/store/filter';

    const filterStore = useFilterStore();
    const currentValue = ref("");

    const props = defineProps({
        label: {
            type: String,
            default: "",
        },
        name: {
            type: String
        },
        id: {
            type: String,
            default: randomId(10)
        },
        value: {
            default: ""
        },
        prepend: {
            type: String,
            default: "%"
        },
        append: {
            type: String,
            default: "%"
        },
        filterKey: {
            type: String,
            default: "default"
        }
    });

    onMounted(() => {
        var oldValue = filterStore.get(props.name, props.filterKey);
        if(oldValue) {
            oldValue = oldValue.replace(props.prepend, "");
            currentValue.value = oldValue.replace(props.append, "");
        } else if (props.value !== "") {
            filterStore.add(props.name, props.value, props.filterKey);
            currentValue.value = props.value;
        }
    })

    const onChange = () => {
        setTimeout(() => {
            const value = currentValue.value !== "" ? props.prepend + currentValue.value + props.append : "";
            filterStore.add(props.name, value, props.filterKey);
        }, 500)
    }
</script>

<style lang="scss" scoped>
    .filter-container.text-filter input {
        width: 100%;
        background: #f2efef;
        border: none;
        padding: 3px 10px;
        color: #6e6d6d;
        background: transparent;
        border-bottom: 1px solid #c8c8c8;
    }
</style>