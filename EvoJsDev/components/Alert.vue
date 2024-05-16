<template>
    <div class="alert-container" v-for="position in positions" :class="position" :key="position">
        <div 
            v-for="(alert, index) in alertStore['get'+position]" 
            :key="index" 
            :class="'alert-'+alert.bg+' evo-'+alert.bg" 
            class="alert animate__animated animate__jello"
            @click="alertStore.close(alert.key)"
            >{{alert.message}}</div>
        </div>
</template>

<script setup>
    import { useAlertStore } from '@/store/alert';
    const alertStore = useAlertStore();
    import "animate.css";
    
    const positions = [
        "TopRight",
        "TopCenter",
        "TopLeft",
        "CenterRight",
        "CenterCenter",
        "CenterLeft",
        "BottomRight",
        "BottomCenter",
        "BottomLeft",
    ]
</script>

<style lang="scss" scoped>
    .alert-container {
        position: fixed;
        z-index: 1000;
        max-width: 300px;
    }

    .TopRight {
        right: 8px;
        top: 92px;
    }

    .TopLeft {
        left: 8px;
        top: 92px;
    }

    .TopCenter {
        left: 50%;
        top: 92px;
        transform: translateX(-50%);
    }

    .CenterRight {
        top: 50%;
        right: 8px;
        transform: translateY(-50%);
    }

    .CenterLeft {
        top: 50%;
        left: 8px;
        transform: translateY(-50%);
    }

    .CenterCenter {
        top: 50%;
        left: 50%;
        transform: translateY(-50%) translateX(-50%);
    }

    .BottomRight {
        right: 8px;
        bottom: 92px;
    }

    .BottomLeft {
        left: 8px;
        bottom: 92px;
    }

    .BottomCenter {
        left: 50%;
        bottom: 92px;
        transform: translateX(-50%);
    }

    .alert {
        display: block;
        width: 100%;
        // background: #d2d2d2;
        // color: #292727;
        cursor: pointer;
    }
</style>