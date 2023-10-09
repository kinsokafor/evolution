import {createApp} from 'vue'
import admin from './admin.vue'
import router from './router'
import { createPinia } from 'pinia'
import restricted from '@/plugins/restricted'

app = createApp(admin)
        .use(createPinia())
        .use(restricted)
        .use(router)
        .mount("#app")