<template>
    <div class="card mb-3">
        <div class="card-body">
            <div v-for="item in data" :key="item">
                <div class="row">
                <div class="col-sm-3">
                    <h6 class="mb-0">{{ item.label }}</h6>
                </div>
                <div class="col-sm-9 text-secondary">
                    {{ item.value }}
                </div>
                </div>
                <hr>
            </div>
        </div>
    </div>
</template>

<script setup>
    import '/color-scheme.css'

    const props = defineProps({
        data: {
            type: Array,
            default: []
        }
    })
</script>

<style lang="scss" scoped>
    .card {
        position: relative;
        display: flex;
        flex-direction: column;
        min-width: 0;
        word-wrap: break-word;
        background-color: var(--highlight1);
        background-clip: border-box;
        border: 0 solid rgba(0,0,0,.125);
        border-radius: .25rem;
    }
    .card-body {
        flex: 1 1 auto;
        min-height: 1px;
        padding: 1rem;
    }
    .text-secondary{
        color: var(--shadow2)
    }
</style>