<template>
    <div>
        <component :is="modalComponent" v-bind="props" @onSubmit="closeModal()"></component>
    </div>
</template>
<script>
    // import {shallowRef} from 'vue';
    import { closeModal } from 'jenesius-vue-modal';
    export default {
        data() {
            return {
                
            }
        },
        props: {
            modalComponent: Object,
            props: {
                type: Object,
                default: {}
            }
        },
        methods: {
            closeModal() {
                closeModal();
            }
        }
    }
</script>

<style lang="scss" scoped>
    .modal-item.widget__modal-wrap {
        background: white;
        padding: 20px;
        max-height: 80vh;
        overflow-y: scroll;
    }
</style>

<style lang="scss">
    .modal-container {
        z-index: 2;
    }
</style>