<template>
    <Input v-bind="$attrs" as="input" :attrs="attributes">
        <template #hint>
            <span @click="show" v-if="attributes.type == 'password'" class="password-btn">Show</span>
            <span @click="hide" v-if="attributes.type == 'text'" class="password-btn">Hide</span>
        </template>
    </Input>
</template>

<script setup>
    import Input from './Input.vue'
    import { ref, onMounted } from 'vue';
    import { useAttrs } from 'vue';

    const attributes = ref(useAttrs().attrs);
    
    const props = defineProps({
        as: {
            type: String,
            default: "input"
        }
    })

    onMounted(() => {
        attributes.value.type = "password"
        attributes.value.class = attributes.value.class + " password-input" ?? "password-input"
    })

    const show = () => {
        attributes.value.type = "text"
        setTimeout(() => {
            hide()
        }, 2000)
    }

    const hide = () => {
        attributes.value.type = "password"
    }
</script>

<script>
    export default {
        inheritAttrs: false
    }
</script>

<style lang="scss" scoped>
    .k-input-group {
        position: relative;
    }
    .k-input-group .password-btn {
        position: absolute;
        right: 13px;
        bottom: 10px;
        opacity: 0.2;
        cursor: pointer;
    }
    .k-input-group .password-btn:hover {
        opacity: 1;
    }
</style>