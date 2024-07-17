<template>
    <div class="k-input-group" :class="layout">
        <label :for="name" class="label">{{ label }}</label>
        <Croppie :name="name" :attrs="attrs" v-bind="filteredAttr" />
    </div>
</template>

<script setup>
    import { computed } from 'vue';
    import Croppie from '@/components/croppie/Croppie.vue';

    const filteredAttr = computed(() => {
        const copy = {...props.attrs}
        delete copy.rules
        delete copy.placeholder
        delete copy.position
        return copy
    });

    const props = defineProps({
        layout: {
            type: String,
            default: ""
        },
        label: {
            type: String,
            default: ""
        },
        name: {
            type: String
        },
        as : {
            type: String,
            default: "croppie"
        },
        attrs: {
            type: Object,
            default: {}
        },
        column: {
            type: String
        }
    })

    // onMounted(() => {
    //     filteredAttr.value = props.attrs;
    //     delete filteredAttr.value.placeholder;
    // })

</script>

<style lang="scss" scoped>
    
</style>