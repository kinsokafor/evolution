<template>
    <div class="tools">
        <div class="controls">
            <div class="mb-2">
                <div class="btn-group">
                    <button 
                        class="btn btn-secondary btn-sm"
                        v-for="index in pageArray"
                        :key="index"
                        @click="$emit('setPage', index)"
                        :class="{active: page == index}"
                        >{{ index }}</button>
                    <button class="btn btn-outline-secondary btn-sm mr-3" @click="$emit('print')">Print</button>
                </div>
                
            </div>
            <div class="mb-4">
                <select :value="modelValue" @input="$emit('update:modelValue', $event.target.value)">
                    <option :value="20">20</option>
                    <option :value="50">50</option>
                    <option :value="75">75</option>
                    <option :value="100">100</option>
                    <option :value="200">200</option>
                    <option :value="500">500</option>
                    <option :value="0">All</option>
                </select>
                <em> showing {{ computedData.length }} of {{ data.length }} records on page {{ page }}</em>
            </div>
        </div>
        <slot></slot>
    </div>
</template>

<script setup>
    const props = defineProps({
        modelValue: Number,
        pageArray: Array,
        page: Number,
        computedData: Array,
        data: Array
    });

    const emit = defineEmits(["setPage", "update:modelValue", "print"])
</script>

<style lang="scss" scoped>

</style>