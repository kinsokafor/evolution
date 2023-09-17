<template>
    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-column align-items-center text-center">
            <img :src="image" alt="Admin" class="rounded-circle" width="150">
            <div class="mt-3">
                <h4>{{ name }}</h4>
                <p class="t-secondary mb-1">{{ caption }}</p>
                <p class="t-muted font-size-sm">{{ address }}</p>
                <!-- <button class="btn btn-primary">Follow</button>
                <button class="btn btn-outline-primary">Message</button> -->
            </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    import '/color-scheme.css'

    const props = defineProps({
        name: {
            type: String,
            default: "Full Name"
        },
        caption: {
            type: String,
            default: "Caption"
        },
        address: {
            type: String,
            default: "Address"
        },
        image: {
            type: String,
            default: "https://bootdey.com/img/Content/avatar/avatar7.png"
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
    .t-secondary{
        color: var(--shadow2)
    }
    .t-muted{
        color: var(--shadow3)
    }
</style>