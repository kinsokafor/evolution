<template>
    <div class="k-input-group">
        <label :for="name" class="label">{{ label }}</label>
    </div>
</template>

<script setup>
    const props = defineProps({
        name: {
            type: String,
            default: ""
        },
        label: String,
        column: String
    })
</script>

<style lang="scss" scoped>

</style>