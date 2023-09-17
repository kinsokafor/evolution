import {createApp} from 'vue'
import admin from './admin.vue'
import router from './router'
import { createPinia } from 'pinia'
app = createApp(admin)
        .use(createPinia())
        .use(router)
        .mount("#app")