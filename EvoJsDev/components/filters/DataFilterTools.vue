<template>
    <slot name="newLine"></slot>
    <div class="tools" v-if="data.length > 20">
        <div class="controls">
            <div class="mb-2 d-flex buttons-set">
                <div class="btn-group">
                    <button 
                        class="btn btn-secondary btn-sm"
                        v-for="index in pageArray"
                        :key="index"
                        @click="$emit('setPage', index)"
                        :class="{active: page == index}"
                        >{{ index }}</button>
                    <button class="btn btn-outline-secondary btn-sm mr-3" @click="$emit('print')" v-if="position=='header'">Print</button>
                </div>
            </div>
            <div class="mb-2" v-if="position=='header'">
                <select class="limit" :value="modelValue" @input="$emit('update:modelValue', $event.target.value)">
                    <option :value="20">20</option>
                    <option :value="50">50</option>
                    <option :value="75">75</option>
                    <option :value="100">100</option>
                    <option :value="200">200</option>
                    <option :value="500">500</option>
                    <option :value="0">All</option>
                </select>
                <em><small> showing {{ computedData.length }} of {{ data.length }} records on page {{ page }}</small></em>
            </div>
        </div>
        <slot></slot>
    </div>
    <div class="tools" v-else>
        <div class="controls">
            <div class="mb-2 d-flex buttons-set">
                <div class="btn-group">
                    <button class="btn btn-outline-secondary btn-sm mr-3" @click="$emit('print')" v-if="position=='header'">Print</button>
                </div>
            </div>
            <div class="mb-2" v-if="position=='header'">
                <em><small> showing {{ computedData.length }} of {{ data.length }} records on page {{ page }}</small></em>
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
        data: Array,
        position: {
            type: String,
            default: "header"
        }
    });

    const emit = defineEmits(["setPage", "update:modelValue", "print"])
</script>

<style lang="scss" scoped>
    .limit {
        background: transparent;
        border: 1px solid #e1dede;
        border-radius: 5px;
        padding: 1px 10px;
    }
    .tools {
        display: flex;
        justify-content: space-between;
        .controls div {
            margin-bottom: 5px;
        }
    }
</style>