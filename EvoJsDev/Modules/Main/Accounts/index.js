import {createApp} from 'vue'
import accounts from './accounts.vue'
import router from './router'
import { createPinia } from 'pinia'
const pinia = createPinia()
const app = createApp(accounts)
        .use(pinia)
        .use(router)
        .mount("#app")