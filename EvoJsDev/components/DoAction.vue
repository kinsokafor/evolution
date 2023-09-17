<template>
    <span v-html="doActionStore.template">
    </span>
</template>

<script setup>
    import { onBeforeMount } from 'vue';
    import { useDoActionStore } from '@/store/doaction';

    const doActionStore = useDoActionStore();

    const props = defineProps({
        action: {
            type: String
        },
        args: {
            type: Object,
            default: {}
        }
    })

    onBeforeMount(() => {
        doActionStore.doAction(props.action, props.args)
    })
</script>

<style lang="scss" scoped>

</style>